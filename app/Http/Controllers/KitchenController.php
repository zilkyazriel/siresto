<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    /**
     * 4.1 Kitchen Display System — papan kanban dapur.
     * Menampilkan pesanan dalam alur dapur: baru, diproses, siap.
     */
    public function index()
    {
        $orders = Order::with(['diningTable', 'items'])
            ->whereIn('status', ['baru', 'diproses', 'siap'])
            ->orderBy('created_at') // tertua di atas = prioritas
            ->get();

        $menuIds = $orders->flatMap(fn (Order $o) => $o->items->pluck('menu_id'))->unique();
        $menuNames = Menu::whereIn('id', $menuIds)->pluck('name', 'id');

        $map = function (Order $order) use ($menuNames) {
            $tableLabel = ($order->dining_table_id && $order->diningTable)
                ? $order->diningTable->number
                : 'Takeaway';

            return [
                'id'          => $order->id,
                'code'        => $order->code,
                'table_label' => $tableLabel,
                'created_ms'  => $order->created_at->timestamp * 1000, // epoch ms utk timer
                'items'       => $order->items->map(fn ($it) => [
                    'id'     => $it->id,
                    'qty'    => (int) $it->quantity,
                    'name'   => $menuNames[$it->menu_id] ?? 'Menu',
                    'note'   => $it->note,
                    'status' => $it->status ?? 'antri', // Pro-05: status per item
                ])->values(),
            ];
        };

        $antri   = $orders->where('status', 'baru')->map($map)->values();
        $dimasak = $orders->where('status', 'diproses')->map($map)->values();
        $siap    = $orders->where('status', 'siap')->map($map)->values();

        return view('kitchen.index', compact('antri', 'dimasak', 'siap'));
    }

    /**
     * Pro-05: ubah status masak SATU item, lalu sinkronkan status pesanan.
     */
    public function updateItemStatus(Request $request, OrderItem $item)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:antri,dimasak,siap'],
        ]);

        $item->status = $validated['status'];
        $item->save();

        $this->syncOrderStatus($item->order);

        return back();
    }

    /**
     * Hitung status pesanan dari status item-itemnya:
     * - semua item siap        -> pesanan "siap"
     * - ada item dimasak/siap  -> pesanan "diproses"
     * - selain itu             -> pesanan "baru"
     * Tidak menyentuh pesanan yang sudah "disajikan" / "batal".
     */
    private function syncOrderStatus(Order $order): void
    {
        $order->load('items');

        if (in_array($order->status, ['disajikan', 'batal'], true)) {
            return;
        }

        $statuses = $order->items->pluck('status');

        if ($statuses->isNotEmpty() && $statuses->every(fn ($s) => $s === 'siap')) {
            $order->status = 'siap';
        } elseif ($statuses->contains('dimasak') || $statuses->contains('siap')) {
            $order->status = 'diproses';
        } else {
            $order->status = 'baru';
        }

        $order->save();
    }
}