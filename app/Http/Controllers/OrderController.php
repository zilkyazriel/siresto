<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Pajak dalam persen desimal. Set 0 untuk menonaktifkan pajak.
     */
    private const TAX_RATE = 0.10;

    /**
     * Halaman POS "Buat Pesanan".
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);

        $menus = Menu::with('category')
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
                'image' => $m->image_path ? Storage::url($m->image_path) : null,
            ]);

        $tables = DiningTable::orderBy('number')->get(['id', 'number', 'capacity']);

        return view('orders.create', [
            'categories' => $categories,
            'menus' => $menus,
            'tables' => $tables,
            'taxRate' => self::TAX_RATE,
        ]);
    }

    /**
     * Simpan pesanan baru + item-itemnya, lalu kirim ke dapur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dining_table_id' => ['nullable', 'exists:dining_tables,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Keranjang masih kosong. Tambahkan minimal satu menu.',
            'items.min' => 'Keranjang masih kosong. Tambahkan minimal satu menu.',
        ]);

        $menuIds = collect($validated['items'])->pluck('menu_id')->all();
        $menus = Menu::whereIn('id', $menuIds)->get()->keyBy('id');

        $order = DB::transaction(function () use ($validated, $menus) {
            $subtotal = 0;
            $lines = [];

            foreach ($validated['items'] as $item) {
                $menu = $menus[$item['menu_id']];
                $qty = (int) $item['quantity'];
                $price = (float) $menu->price; // harga otoritatif dari DB, bukan dari client
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

            $tax = round($subtotal * self::TAX_RATE);
            $total = $subtotal + $tax;

            $order = Order::create([
                'code' => $this->generateCode(),
                'dining_table_id' => $validated['dining_table_id'] ?? null,
                'user_id' => auth()->id(),
                'status' => 'baru',
                'total' => $total,
            ]);

            $order->items()->createMany($lines);

            return $order;
        });

        return redirect()
            ->route('orders.create')
            ->with('success', "Pesanan {$order->code} berhasil dikirim ke dapur!");
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
