<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota {{ $order->code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print { .no-print { display: none !important; } body { background: #fff !important; } }
    </style>
</head>
<body class="min-h-screen bg-slate-100 py-8">
    <div class="mx-auto max-w-sm rounded-2xl bg-white p-6 shadow-sm">
        <div class="text-center">
            <h1 class="font-['Poppins'] text-2xl font-bold text-[#f97316]">SIRESTO</h1>
            <p class="text-xs text-slate-500">Terima kasih atas kunjungan Anda</p>
        </div>
        <div class="my-4 border-t border-dashed border-slate-300"></div>
        <div class="space-y-1 text-sm text-slate-600">
            <div class="flex justify-between"><span>No. Pesanan</span><span class="font-semibold text-slate-800">{{ $order->code }}</span></div>
            <div class="flex justify-between"><span>Tanggal</span><span>{{ optional($order->payment->paid_at ?? $order->created_at)->format('d/m/Y H:i') }}</span></div>
            <div class="flex justify-between"><span>Kasir</span><span>{{ optional($order->user)->name ?? '-' }}</span></div>
            <div class="flex justify-between"><span>Meja</span><span>{{ $order->diningTable ? 'Meja '.$order->diningTable->number : 'Takeaway' }}</span></div>
        </div>
        <div class="my-4 border-t border-dashed border-slate-300"></div>
        <table class="w-full text-sm text-slate-700">
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td class="py-1 align-top">
                            {{ $menuNames[$item->menu_id] ?? 'Menu #'.$item->menu_id }}<br>
                            <span class="text-xs text-slate-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-1 text-right align-top">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="my-4 border-t border-dashed border-slate-300"></div>
        <div class="space-y-1 text-sm">
            <div class="flex justify-between text-slate-600"><span>Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-slate-600"><span>Pajak (10%)</span><span>Rp {{ number_format($tax, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-base font-bold text-slate-900"><span>Total</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
        </div>
        <div class="my-4 border-t border-dashed border-slate-300"></div>
        <div class="space-y-1 text-sm text-slate-600">
            <div class="flex justify-between"><span>Metode</span><span class="uppercase">{{ $order->payment->method }}</span></div>
            @if ($order->payment->method === 'tunai')
                <div class="flex justify-between"><span>Tunai</span><span>Rp {{ number_format($total + $order->payment->change, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Kembalian</span><span>Rp {{ number_format($order->payment->change, 0, ',', '.') }}</span></div>
            @endif
        </div>
        <div class="my-4 border-t border-dashed border-slate-300"></div>
        <p class="text-center text-xs text-slate-500">Struk ini adalah bukti pembayaran yang sah.</p>
    </div>
    <div class="no-print mx-auto mt-6 flex max-w-sm gap-3 px-2">
        <a href="{{ route('cashier.index') }}" class="flex-1 rounded-xl border border-slate-300 bg-white py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
        <button type="button" onclick="window.print()" class="flex-1 rounded-xl bg-[#f97316] py-2.5 text-center text-sm font-semibold text-white hover:bg-[#ea580c]">Cetak Nota</button>
    </div>
</body>
</html>
