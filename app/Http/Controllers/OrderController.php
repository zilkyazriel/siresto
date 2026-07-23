<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Stock;

class OrderController extends Controller
{
    /**
     * Tarif pajak dari config (config/restaurant.php ← env TAX_RATE).
     */
    private function taxRate(): float
    {
        return (float) config('restaurant.tax_rate', 0.10);
    }

    /**
     * 3.2 POS — halaman buat pesanan baru.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);

        $menus = Menu::with(['category', 'ingredients'])
            ->orderBy('name')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'description' => $m->description,
                'price' => (float) $m->price,
                'category_id' => $m->category_id,
                'category' => $m->category?->name,
                'available' => (bool) $m->is_available,
                'has_recipe' => $m->ingredients->isNotEmpty(),
                'recipe' => $m->ingredients->map(fn ($i) => [
                    'stock_id' => (int) $i->stock_id,
                    'quantity' => (float) $i->quantity,
                ])->values(),
                'image' => $m->image_path ? Storage::url($m->image_path) : null,
            ]);

        $stocks = Stock::orderBy('name')->get(['id', 'name', 'unit', 'quantity'])
            ->map(fn ($s) => [
                'id' => (int) $s->id,
                'name' => $s->name,
                'unit' => $s->unit,
                'quantity' => (float) $s->quantity,
            ]);

        $tables = DiningTable::orderBy('number')->get(['id', 'number', 'capacity']);

        return view('orders.create', [
            'categories' => $categories,
            'menus' => $menus,
            'stocks' => $stocks,
            'tables' => $tables,
            'taxRate' => $this->taxRate(),
        ]);
    }
    /**
     * Simpan pesanan baru + item-itemnya, lalu kirim ke dapur.
     * Termasuk cek ketersediaan bahan baku (Pro-04) & potong stok otomatis (Pro-09).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dining_table_id' => ['nullable', 'exists:dining_tables,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'items.required' => 'Keranjang masih kosong. Tambahkan minimal satu menu.',
            'items.min' => 'Keranjang masih kosong. Tambahkan minimal satu menu.',
        ]);

        $menuIds = collect($validated['items'])->pluck('menu_id')->unique()->all();
        $menus = Menu::with('ingredients')->whereIn('id', $menuIds)->get()->keyBy('id');

        // 1) Semua menu yang dipesan WAJIB punya resep.
        $tanpaResep = $menus->filter(fn ($m) => $m->ingredients->isEmpty());
        if ($tanpaResep->isNotEmpty()) {
            return back()->withInput()->with(
                'error',
                'Menu berikut belum punya resep sehingga belum bisa dipesan: '
                    . $tanpaResep->pluck('name')->join(', ') . '. Lengkapi resepnya dulu di menu Menu.'
            );
        }

        // 2) Hitung total kebutuhan bahan: [stock_id => jumlah dibutuhkan].
        $needed = [];
        foreach ($validated['items'] as $item) {
            $menu = $menus[$item['menu_id']];
            $qty = (int) $item['quantity'];
            foreach ($menu->ingredients as $ing) {
                $needed[$ing->stock_id] = ($needed[$ing->stock_id] ?? 0) + ((float) $ing->quantity * $qty);
            }
        }

        DB::beginTransaction();

        try {
            // Kunci baris stok yang terlibat agar aman dari pesanan bersamaan.
            $stocks = Stock::whereIn('id', array_keys($needed))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // 3) Cek ketersediaan stok.
            $kurang = [];
            foreach ($needed as $stockId => $need) {
                $stock = $stocks[$stockId] ?? null;
                $tersedia = $stock ? (float) $stock->quantity : 0;

                if (! $stock || $tersedia < $need) {
                    $nama = $stock->name ?? ('Bahan #' . $stockId);
                    $satuan = $stock->unit ?? '';
                    $needFmt = rtrim(rtrim(number_format($need, 2, '.', ''), '0'), '.');
                    $sisaFmt = rtrim(rtrim(number_format($tersedia, 2, '.', ''), '0'), '.');
                    $kurang[] = "{$nama} (butuh {$needFmt} {$satuan}, sisa {$sisaFmt} {$satuan})";
                }
            }

            if (! empty($kurang)) {
                DB::rollBack();

                return back()->withInput()->with(
                    'error',
                    'Pesanan ditolak — stok bahan tidak cukup: ' . implode('; ', $kurang) . '.'
                );
            }

            // 4) Buat order + item-itemnya (harga otoritatif dari DB).
            $subtotal = 0;
            $lines = [];
            foreach ($validated['items'] as $item) {
                $menu = $menus[$item['menu_id']];
                $qty = (int) $item['quantity'];
                $price = (float) $menu->price;
                $lineSubtotal = $price * $qty;
                $subtotal += $lineSubtotal;

                $lines[] = [
                    'menu_id' => $menu->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $lineSubtotal,
                    'status' => 'antri',
                    'note' => $item['note'] ?? null,
                ];
            }

            $tax = round($subtotal * $this->taxRate());
            $total = $subtotal + $tax;

            $order = Order::create([
                'code' => $this->generateCode(),
                'dining_table_id' => $validated['dining_table_id'] ?? null,
                'user_id' => auth()->id(),
                'status' => 'baru',
                'note' => $validated['note'] ?? null,
                'total' => $total,
            ]);

            $order->items()->createMany($lines);

            // 5) Potong stok sesuai kebutuhan.
            foreach ($needed as $stockId => $need) {
                $stocks[$stockId]->decrement('quantity', $need);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        // Pro-13: pesanan baru -> meja jadi "terisi"
        if ($order->dining_table_id) {
            $order->diningTable?->update(['status' => 'terisi']);
        }

        return redirect()
            ->route('orders.create')
            ->with('success', "Pesanan {$order->code} berhasil dikirim ke dapur!");
    }
    /**
     * 3.3 Daftar Pesanan — papan pantau pesanan hari ini.
     * Model 2 status: status dapur (baru→diproses→siap→disajikan) + status bayar (lunas/belum).
     * 'Selesai' = disajikan & lunas.
     */
    public function index(Request $request)
    {
        $orders = Order::with(['diningTable', 'items', 'payment'])
            ->whereDate('created_at', today())
            ->where('status', '!=', 'batal')
            ->latest()
            ->get();

        $kitchenMeta = [
            'baru'      => ['label' => 'Baru',      'badge' => 'bg-orange-100 text-orange-700'],
            'diproses'  => ['label' => 'Diproses',  'badge' => 'bg-blue-100 text-blue-700'],
            'siap'      => ['label' => 'Siap',       'badge' => 'bg-green-100 text-green-700'],
            'disajikan' => ['label' => 'Disajikan', 'badge' => 'bg-slate-100 text-slate-700'],
        ];

        $cards = $orders->map(function (Order $order) use ($kitchenMeta) {
            $status = $order->status ?: 'baru';
            if (! isset($kitchenMeta[$status])) {
                $status = 'baru';
            }

            $isPaid = $order->payment && (bool) ($order->payment->paid ?? false);

            // Status papan untuk filter: 'selesai' = sudah disajikan & lunas.
            $boardStatus = ($status === 'disajikan' && $isPaid) ? 'selesai' : $status;

            $tableLabel = ($order->dining_table_id && $order->diningTable)
                ? 'Meja ' . $order->diningTable->number
                : 'Takeaway';

            $minutes = (int) abs($order->created_at->diffInMinutes(now()));
            $timeDisplay = $minutes < 60
                ? $minutes . ' Menit lalu'
                : $order->created_at->format('d M, H:i');

            return [
                'id'            => $order->id,
                'code'          => $order->code,
                'table_label'   => $tableLabel,
                'kitchen'       => $status,
                'kitchen_label' => $kitchenMeta[$status]['label'],
                'kitchen_badge' => $kitchenMeta[$status]['badge'],
                'is_paid'       => $isPaid,
                'board_status'  => $boardStatus,
                'item_count'    => (int) $order->items->sum('quantity'),
                'total_display' => 'Rp ' . number_format((float) $order->total, 0, ',', '.'),
                'time_display'  => $timeDisplay,
                'search'        => mb_strtolower($order->code . ' ' . $tableLabel),
            ];
        })->values();

        $counts = [
            'all'       => $cards->count(),
            'baru'      => $cards->where('board_status', 'baru')->count(),
            'diproses'  => $cards->where('board_status', 'diproses')->count(),
            'siap'      => $cards->where('board_status', 'siap')->count(),
            'disajikan' => $cards->where('board_status', 'disajikan')->count(),
            'selesai'   => $cards->where('board_status', 'selesai')->count(),
        ];

        return view('orders.index', compact('cards', 'counts'));
    }

    /**
     * 3.4 Detail Pesanan — tampilan lengkap satu pesanan.
     * Timeline: Dibuat → Diproses → Siap → Disajikan → Selesai (Selesai = disajikan & lunas).
     */
    public function show(Order $order)
    {
        $order->load(['diningTable', 'items', 'payment', 'user']);

        $ids        = $order->items->pluck('menu_id');
        $menuNames  = Menu::whereIn('id', $ids)->pluck('name', 'id');
        $menuImages = Menu::whereIn('id', $ids)->pluck('image_path', 'id');

        $status = $order->status ?: 'baru';
        $isPaid = $order->payment && (bool) ($order->payment->paid ?? false);
        $boardStatus = ($status === 'disajikan' && $isPaid) ? 'selesai' : $status;

        // Langkah timeline (1..5). Selesai hanya jika disajikan & lunas.
        $stepOrder = ['baru' => 1, 'diproses' => 2, 'siap' => 3, 'disajikan' => 4];
        $currentStep = ($status === 'disajikan' && $isPaid) ? 5 : ($stepOrder[$status] ?? 1);

        $subtotal = (float) $order->items->sum('subtotal');
        $total    = (float) $order->total;
        $tax      = max(0, $total - $subtotal);

        $tableLabel = ($order->dining_table_id && $order->diningTable)
            ? 'Meja ' . $order->diningTable->number
            : 'Takeaway';

        $items = $order->items->map(fn ($it) => [
            'name'     => $menuNames[$it->menu_id] ?? 'Menu',
            'image'    => ($menuImages[$it->menu_id] ?? null) ? Storage::url($menuImages[$it->menu_id]) : null,
            'qty'      => (int) $it->quantity,
            'price'    => (float) $it->price,
            'subtotal' => (float) $it->subtotal,
            'note'     => $it->note,
        ])->values();

        return view('orders.show', compact(
            'order', 'items', 'tableLabel', 'status', 'boardStatus',
            'isPaid', 'currentStep', 'subtotal', 'tax', 'total'
        ));
    }

    /**
     * 3.3 — majukan status DAPUR pesanan (baru → diproses → siap → disajikan).
     * Status bayar terpisah, diurus modul kasir (pembayaran).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:diproses,siap,disajikan',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', "Status pesanan {$order->code} diperbarui.");
    }

    /**
     * Pro-11 — Batalkan pesanan (hanya saat status "Baru" & belum lunas).
     * Stok bahan yang tadi dipotong (Pro-04) dikembalikan.
     */
    public function cancel(Request $request, Order $order)
    {
        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($order->status === 'batal') {
            return back()->with('error', "Pesanan {$order->code} sudah dibatalkan.");
        }

        if ($order->status !== 'baru') {
            return back()->with('error', 'Pesanan hanya bisa dibatalkan saat status masih "Baru".');
        }

        $order->loadMissing('payment');
        if ($order->payment && (bool) ($order->payment->paid ?? false)) {
            return back()->with('error', 'Pesanan yang sudah lunas tidak bisa dibatalkan.');
        }

        DB::transaction(function () use ($order, $validated) {
            $order->load('items');

            $menus = Menu::with('ingredients')
                ->whereIn('id', $order->items->pluck('menu_id')->unique())
                ->get()
                ->keyBy('id');

            // Kembalikan stok bahan sesuai resep tiap item.
            foreach ($order->items as $item) {
                $menu = $menus[$item->menu_id] ?? null;
                if (! $menu) {
                    continue;
                }
                foreach ($menu->ingredients as $ing) {
                    Stock::whereKey($ing->stock_id)->increment('quantity', $ing->quantity * $item->quantity);
                }
            }

            $order->update([
                'status' => 'batal',
                'cancel_reason' => $validated['cancel_reason'] ?? null,
                'cancelled_at' => now(),
            ]);

            // Pro-13: pesanan dibatalkan -> hitung ulang status meja
            if ($order->dining_table_id) {
                $order->diningTable?->refreshStatusFromOrders();
            }
        });

        return redirect()
            ->route('orders.index')
            ->with('success', "Pesanan {$order->code} dibatalkan & stok bahan dikembalikan.");
    }

    /**
     * Pro-11 — Form ubah pesanan (hanya status "Baru" & belum lunas).
     */
    public function edit(Order $order)
    {
        if ($order->status !== 'baru') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan hanya bisa diubah saat status masih "Baru".');
        }

        $order->loadMissing('payment');
        if ($order->payment && (bool) ($order->payment->paid ?? false)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan yang sudah lunas tidak bisa diubah.');
        }

        $order->load('items');

        $menus = Menu::with('ingredients')->orderBy('name')->get()->map(fn ($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'price' => (float) $m->price,
            'available' => (bool) $m->is_available,
            'has_recipe' => $m->ingredients->isNotEmpty(),
        ]);

        $tables = DiningTable::orderBy('number')->get(['id', 'number', 'capacity']);

        $currentItems = $order->items->map(fn ($it) => [
            'menu_id' => $it->menu_id,
            'quantity' => (int) $it->quantity,
            'note' => $it->note,
        ]);

        return view('orders.edit', [
            'order' => $order,
            'menus' => $menus,
            'tables' => $tables,
            'currentItems' => $currentItems,
            'taxRate' => $this->taxRate(),
        ]);
    }

    /**
     * Pro-11 — Simpan perubahan pesanan + sesuaikan stok berdasarkan selisih resep.
     */
    public function update(Request $request, Order $order)
    {
        if ($order->status !== 'baru') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan hanya bisa diubah saat status masih "Baru".');
        }

        $order->loadMissing('payment');
        if ($order->payment && (bool) ($order->payment->paid ?? false)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan yang sudah lunas tidak bisa diubah.');
        }

        $validated = $request->validate([
            'dining_table_id' => ['nullable', 'exists:dining_tables,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Pesanan harus punya minimal satu menu.',
            'items.min' => 'Pesanan harus punya minimal satu menu.',
        ]);

        // Gabungkan menu yang sama.
        $newByMenu = collect($validated['items'])
            ->groupBy('menu_id')
            ->map(fn ($g, $menuId) => [
                'menu_id' => (int) $menuId,
                'quantity' => (int) $g->sum(fn ($r) => (int) $r['quantity']),
                'note' => collect($g)->pluck('note')->filter()->implode('; ') ?: null,
            ])->values();

        $menuIds = $newByMenu->pluck('menu_id')
            ->merge($order->items()->pluck('menu_id'))
            ->unique();

        $menus = Menu::with('ingredients')->whereIn('id', $menuIds)->get()->keyBy('id');

        // Semua menu baru wajib punya resep.
        foreach ($newByMenu as $row) {
            $menu = $menus[$row['menu_id']] ?? null;
            if (! $menu || $menu->ingredients->isEmpty()) {
                return back()->withInput()->with('error',
                    'Menu "' . ($menu->name ?? 'tidak dikenal') . '" belum punya resep, tidak bisa dipesan.');
            }
        }

        try {
            DB::transaction(function () use ($order, $newByMenu, $menus, $validated) {
                $order->load('items');

                // Hitung selisih kebutuhan stok: kembalikan item lama, potong item baru.
                $needChange = [];
                foreach ($order->items as $item) {
                    $menu = $menus[$item->menu_id] ?? null;
                    if (! $menu) {
                        continue;
                    }
                    foreach ($menu->ingredients as $ing) {
                        $needChange[$ing->stock_id] = ($needChange[$ing->stock_id] ?? 0) - ($ing->quantity * $item->quantity);
                    }
                }

                $subtotal = 0;
                $lines = [];
                foreach ($newByMenu as $row) {
                    $menu = $menus[$row['menu_id']];
                    foreach ($menu->ingredients as $ing) {
                        $needChange[$ing->stock_id] = ($needChange[$ing->stock_id] ?? 0) + ($ing->quantity * $row['quantity']);
                    }
                    $price = (float) $menu->price;
                    $lineSub = $price * $row['quantity'];
                    $subtotal += $lineSub;
                    $lines[] = [
                        'menu_id' => $menu->id,
                        'quantity' => $row['quantity'],
                        'price' => $price,
                        'subtotal' => $lineSub,
                        'status' => 'antri',
                        'note' => $row['note'],
                    ];
                }

                // Kunci & cek ketersediaan hanya untuk stok yang berubah.
                $stockIds = array_keys(array_filter($needChange, fn ($v) => $v != 0));
                if (! empty($stockIds)) {
                    $stocks = Stock::whereIn('id', $stockIds)->lockForUpdate()->get()->keyBy('id');

                    foreach ($needChange as $stockId => $delta) {
                        if ($delta > 0) {
                            $stock = $stocks[$stockId] ?? null;
                            if (! $stock || $stock->quantity < $delta) {
                                throw new \RuntimeException(
                                    'Stok ' . ($stock->name ?? '') . ' tidak cukup (butuh tambahan ' . $delta . ' ' . ($stock->unit ?? '') . ', sisa ' . ($stock->quantity ?? 0) . ').'
                                );
                            }
                        }
                    }

                    foreach ($needChange as $stockId => $delta) {
                        if ($delta > 0) {
                            Stock::whereKey($stockId)->decrement('quantity', $delta);
                        } elseif ($delta < 0) {
                            Stock::whereKey($stockId)->increment('quantity', -$delta);
                        }
                    }
                }

                $tax = round($subtotal * $this->taxRate());
                $total = $subtotal + $tax;

                $order->items()->delete();
                $order->items()->createMany($lines);
                $order->update([
                    'dining_table_id' => $validated['dining_table_id'] ?? null,
                    'total' => $total,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order)
            ->with('success', "Pesanan {$order->code} berhasil diperbarui.");
    }

    /**
     * Kode pesanan unik: ORD-YYMMDD-0001 (reset harian).
     */
    private function generateCode(): string
    {
        $prefix = 'ORD-' . now()->format('ymd') . '-';
        $count = Order::where('code', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
