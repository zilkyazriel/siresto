<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;

class KitchenController extends Controller
{
    /**
     * 4.1 Kitchen Display System — papan kanban dapur.
     * Menampilkan pesanan yang masih dalam alur dapur: baru, diproses, siap.
     * Status bayar TIDAK diurus di sini (dimensi terpisah).
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
                    'qty'  => (int) $it->quantity,
                    'name' => $menuNames[$it->menu_id] ?? 'Menu',
                    'note' => $it->note,
                ])->values(),
            ];
        };

        $antri   = $orders->where('status', 'baru')->map($map)->values();
        $dimasak = $orders->where('status', 'diproses')->map($map)->values();
        $siap    = $orders->where('status', 'siap')->map($map)->values();

        return view('kitchen.index', compact('antri', 'dimasak', 'siap'));
    }
}
