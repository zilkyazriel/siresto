<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    /**
     * 5.1 Daftar Tagihan — daftar pesanan yang perlu diproses pembayarannya.
     */
    public function index(Request $request)
    {
        $orders = Order::with(['diningTable', 'items', 'payment'])
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $bills = $orders->map(function (Order $order) {
            $isPaid = $order->payment && (bool) ($order->payment->paid ?? false);

            $tableLabel = ($order->dining_table_id && $order->diningTable)
                ? 'Meja ' . $order->diningTable->number
                : 'Takeaway';

            $minutes = (int) abs($order->created_at->diffInMinutes(now()));
            $timeDisplay = $minutes < 60
                ? $minutes . ' menit'
                : $order->created_at->format('H:i');

            return [
                'id'            => $order->id,
                'code'          => $order->code,
                'table_label'   => $tableLabel,
                'is_paid'       => $isPaid,
                'item_count'    => (int) $order->items->sum('quantity'),
                'total'         => (float) $order->total,
                'total_display' => 'Rp ' . number_format((float) $order->total, 0, ',', '.'),
                'time_display'  => $timeDisplay,
                'search'        => mb_strtolower($order->code . ' ' . $tableLabel),
            ];
        })->values();

        $unpaidCount = $bills->where('is_paid', false)->count();
        $paidCount   = $bills->where('is_paid', true)->count();

        return view('cashier.index', [
            'bills'       => $bills,
            'unpaidCount' => $unpaidCount,
            'paidCount'   => $paidCount,
        ]);
    }

    /**
     * 5.2 Pembayaran — halaman proses pembayaran untuk satu pesanan.
     */
    public function show(Order $order)
    {
        $order->load(['diningTable', 'items', 'payment', 'user']);

        if ($order->payment && (bool) ($order->payment->paid ?? false)) {
            return redirect()->route('cashier.receipt', $order->id)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        $menuNames = Menu::whereIn('id', $order->items->pluck('menu_id'))->pluck('name', 'id');
        $subtotal  = (float) $order->items->sum('subtotal');
        $total     = (float) $order->total;
        $tax       = max(0, $total - $subtotal);

        return view('cashier.show', compact('order', 'menuNames', 'subtotal', 'tax', 'total'));
    }

    /**
     * 5.2 Pembayaran — proses & simpan pembayaran, lalu ke nota.
     */
    public function pay(Request $request, Order $order)
    {
        if ($order->payment && (bool) ($order->payment->paid ?? false)) {
            return redirect()->route('cashier.receipt', $order->id)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        $validated = $request->validate([
            'method'   => 'required|in:tunai,kartu,qris',
            'received' => 'nullable|numeric|min:0',
        ]);

        $total    = (float) $order->total;
        $method   = $validated['method'];
        $received = (float) ($validated['received'] ?? 0);

        if ($method === 'tunai') {
            if ($received < $total) {
                return back()
                    ->withErrors(['received' => 'Uang diterima kurang dari total tagihan.'])
                    ->withInput();
            }
            $change = $received - $total;
        } else {
            $change = 0.0;
        }

        DB::transaction(function () use ($order, $method, $total, $change) {
            Payment::create([
                'order_id' => $order->id,
                'user_id'  => auth()->id(),
                'amount'   => $total,
                'paid'     => true,
                'change'   => $change,
                'method'   => $method,
                'paid_at'  => now(),
            ]);

            $order->update(['status' => 'selesai']);
        });

        return redirect()->route('cashier.receipt', $order->id)
            ->with('success', 'Pembayaran berhasil diproses.');
    }

    /**
     * 5.2 Pembayaran — nota / struk pembayaran (printable).
     */
    public function receipt(Order $order)
    {
        $order->load(['diningTable', 'items', 'payment', 'user']);

        if (! $order->payment || ! (bool) ($order->payment->paid ?? false)) {
            return redirect()->route('cashier.show', $order->id)
                ->with('info', 'Pesanan ini belum dibayar.');
        }

        $menuNames = Menu::whereIn('id', $order->items->pluck('menu_id'))->pluck('name', 'id');
        $subtotal  = (float) $order->items->sum('subtotal');
        $total     = (float) $order->total;
        $tax       = max(0, $total - $subtotal);

        return view('cashier.receipt', compact('order', 'menuNames', 'subtotal', 'tax', 'total'));
    }
}
