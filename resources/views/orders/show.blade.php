<x-app-layout>
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6">

@if (session('success'))
<div class="mb-5 flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
<span class="material-symbols-outlined text-[20px]">check_circle</span>{{ session('success') }}
</div>
@endif
@if (session('info'))
<div class="mb-5 flex items-center gap-2 rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">
<span class="material-symbols-outlined text-[20px]">info</span>{{ session('info') }}
</div>
@endif

@php
$badgeMeta = [
    'baru'      => ['Baru', 'bg-orange-100 text-orange-700'],
    'diproses'  => ['Diproses', 'bg-blue-100 text-blue-700'],
    'siap'      => ['Siap', 'bg-green-100 text-green-700'],
    'disajikan' => ['Disajikan', 'bg-slate-100 text-slate-700'],
    'selesai'   => ['Selesai', 'bg-emerald-100 text-emerald-700'],
];
$bm = $badgeMeta[$boardStatus] ?? $badgeMeta['baru'];
$itemMeta = [
    'baru'      => ['Antri', 'bg-orange-100 text-orange-700'],
    'diproses'  => ['Dimasak', 'bg-blue-100 text-blue-700'],
    'siap'      => ['Siap', 'bg-green-100 text-green-700'],
    'disajikan' => ['Disajikan', 'bg-slate-100 text-slate-700'],
];
$im = $itemMeta[$status] ?? $itemMeta['baru'];
$steps = ['Dibuat', 'Diproses', 'Siap', 'Disajikan', 'Selesai'];
$pct = $currentStep > 1 ? (($currentStep - 1) / 4) * 100 : 0;
@endphp

<!-- Breadcrumb -->
<nav class="flex text-sm text-slate-500 mb-5">
<a href="{{ route('orders.index') }}" class="hover:text-[#f97316] transition-colors">Daftar Pesanan</a>
<span class="material-symbols-outlined text-[18px] mx-1">chevron_right</span>
<span class="text-[#0b1c30] font-medium">Detail Pesanan</span>
</nav>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
<div>
<h2 class="text-3xl font-bold font-['Poppins'] text-[#0b1c30] mb-1">Detail Pesanan #{{ $order->code }}</h2>
<div class="flex flex-wrap items-center gap-y-2 gap-x-4 text-slate-500">
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-[18px]">table_restaurant</span>
<span class="font-semibold text-[#0b1c30]">{{ $tableLabel }}</span>
</div>
<div class="w-1 h-1 rounded-full bg-slate-300"></div>
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-[18px]">person</span>
<span>{{ $order->user->name ?? 'Staf' }}</span>
</div>
<div class="w-1 h-1 rounded-full bg-slate-300"></div>
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-[18px]">schedule</span>
<span>{{ $order->created_at->format('d M Y, H:i') }}</span>
</div>
<div class="w-1 h-1 rounded-full bg-slate-300"></div>
<span class="px-3 py-1 rounded-full text-xs font-semibold {{ $bm[1] }}">{{ $bm[0] }}</span>
</div>
</div>
<div class="flex items-center gap-3">
<a href="{{ route('orders.index') }}" class="px-4 py-2 bg-slate-100 text-[#0b1c30] font-semibold rounded-xl hover:bg-slate-200 transition-colors flex items-center gap-2">
<span class="material-symbols-outlined text-[20px]">arrow_back</span>Kembali
</a>
@if ($isPaid && Route::has('cashier.receipt'))
<a href="{{ route('cashier.receipt', $order->id) }}" class="px-4 py-2 border border-slate-200 text-[#0b1c30] font-semibold rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-2">
<span class="material-symbols-outlined text-[20px]">print</span>Cetak Nota
</a>
@endif
</div>
</div>

<!-- Two column -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

<!-- Left -->
<div class="lg:col-span-8 flex flex-col gap-6">

<!-- Timeline -->
<div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
<h3 class="text-lg font-bold font-['Poppins'] text-[#0b1c30] mb-8">Status Pesanan</h3>
<div class="relative flex justify-between items-start w-full px-2">
<div class="absolute top-4 left-0 w-full h-[2px] bg-slate-200 -translate-y-1/2 z-0"></div>
<div class="absolute top-4 left-0 h-[2px] bg-[#f97316] -translate-y-1/2 z-0" style="width: {{ $pct }}%"></div>
@foreach ($steps as $i => $label)
@php $n = $i + 1; @endphp
<div class="relative z-10 flex flex-col items-center gap-2 w-1/5">
@if ($n < $currentStep)
<div class="w-8 h-8 rounded-full bg-[#f97316] text-white flex items-center justify-center shadow-sm">
<span class="material-symbols-outlined text-[18px]">check</span>
</div>
<span class="text-xs font-semibold text-[#0b1c30] text-center">{{ $label }}</span>
@elseif ($n === $currentStep)
<div class="w-8 h-8 rounded-full bg-white border-2 border-[#f97316] flex items-center justify-center shadow-sm">
<div class="w-3 h-3 rounded-full bg-[#f97316]"></div>
</div>
<span class="text-xs font-semibold text-[#f97316] text-center">{{ $label }}</span>
@else
<div class="w-8 h-8 rounded-full bg-white border-2 border-slate-200"></div>
<span class="text-xs font-medium text-slate-400 text-center">{{ $label }}</span>
@endif
</div>
@endforeach
</div>
</div>

<!-- Items -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
<div class="p-6 border-b border-slate-100">
<h3 class="text-lg font-bold font-['Poppins'] text-[#0b1c30]">Daftar Item</h3>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-[#eff4ff] text-slate-500 text-xs uppercase tracking-wide border-b border-slate-100">
<th class="p-4 font-semibold">Menu</th>
<th class="p-4 font-semibold text-center">Qty</th>
<th class="p-4 font-semibold text-right">Harga</th>
<th class="p-4 font-semibold text-right">Subtotal</th>
<th class="p-4 font-semibold text-center">Status</th>
</tr>
</thead>
<tbody class="text-[#0b1c30]">
@foreach ($items as $it)
<tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
<td class="p-4">
<div class="flex items-center gap-3">
<div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-100 flex items-center justify-center shrink-0">
@if ($it['image'])
<img src="{{ $it['image'] }}" alt="{{ $it['name'] }}" class="w-full h-full object-cover">
@else
<span class="material-symbols-outlined text-slate-400">restaurant</span>
@endif
</div>
<div>
<p class="font-semibold">{{ $it['name'] }}</p>
@if (!empty($it['note']))
<p class="text-xs text-slate-500">Catatan: {{ $it['note'] }}</p>
@endif
</div>
</div>
</td>
<td class="p-4 text-center font-semibold">{{ $it['qty'] }}</td>
<td class="p-4 text-right">Rp {{ number_format($it['price'], 0, ',', '.') }}</td>
<td class="p-4 text-right font-semibold">Rp {{ number_format($it['subtotal'], 0, ',', '.') }}</td>
<td class="p-4 text-center">
<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $im[1] }}">{{ $im[0] }}</span>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>

<!-- Right -->
<div class="lg:col-span-4 flex flex-col gap-6">

<!-- Summary -->
<div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
<h3 class="text-lg font-bold font-['Poppins'] text-[#0b1c30] mb-4">Ringkasan Pesanan</h3>
<div class="space-y-3 mb-5 text-slate-500">
<div class="flex justify-between">
<span>Subtotal</span>
<span class="font-semibold text-[#0b1c30]">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
</div>
<div class="flex justify-between">
<span>Pajak (10%)</span>
<span class="font-semibold text-[#0b1c30]">Rp {{ number_format($tax, 0, ',', '.') }}</span>
</div>
</div>
<div class="border-t border-slate-100 pt-4 mb-5">
<div class="flex justify-between items-center">
<span class="text-lg font-bold text-[#0b1c30]">Total</span>
<span class="text-2xl font-bold font-['Poppins'] text-[#f97316]">Rp {{ number_format($total, 0, ',', '.') }}</span>
</div>
</div>

@if ($status === 'baru')
<form method="POST" action="{{ route('orders.updateStatus', $order->id) }}">
@csrf
<input type="hidden" name="status" value="diproses">
<button type="submit" class="w-full py-3.5 bg-[#f97316] text-white font-semibold rounded-xl hover:bg-[#ea580c] transition-colors flex justify-center items-center gap-2 shadow-sm active:scale-[0.98]">
<span class="material-symbols-outlined text-[20px]">skillet</span>Mulai Proses
</button>
</form>
@elseif ($status === 'diproses')
<form method="POST" action="{{ route('orders.updateStatus', $order->id) }}">
@csrf
<input type="hidden" name="status" value="siap">
<button type="submit" class="w-full py-3.5 bg-[#f97316] text-white font-semibold rounded-xl hover:bg-[#ea580c] transition-colors flex justify-center items-center gap-2 shadow-sm active:scale-[0.98]">
<span class="material-symbols-outlined text-[20px]">check_circle</span>Tandai Siap
</button>
</form>
@elseif ($status === 'siap')
<form method="POST" action="{{ route('orders.updateStatus', $order->id) }}">
@csrf
<input type="hidden" name="status" value="disajikan">
<button type="submit" class="w-full py-3.5 bg-[#f97316] text-white font-semibold rounded-xl hover:bg-[#ea580c] transition-colors flex justify-center items-center gap-2 shadow-sm active:scale-[0.98]">
<span class="material-symbols-outlined text-[20px]">room_service</span>Tandai Disajikan
</button>
</form>
@elseif ($status === 'disajikan' && ! $isPaid)
@if (Route::has('cashier.show'))
<a href="{{ route('cashier.show', $order->id) }}" class="w-full py-3.5 bg-[#f97316] text-white font-semibold rounded-xl hover:bg-[#ea580c] transition-colors flex justify-center items-center gap-2 shadow-sm active:scale-[0.98]">
<span class="material-symbols-outlined text-[20px]">payments</span>Proses Pembayaran
</a>
@else
<div class="w-full py-3 text-center rounded-xl bg-amber-50 text-amber-700 text-sm font-medium">Menunggu pembayaran di kasir</div>
@endif
@else
<div class="w-full py-3 rounded-xl bg-emerald-50 text-emerald-700 font-semibold flex justify-center items-center gap-2">
<span class="material-symbols-outlined text-[20px]">verified</span>Pesanan Selesai & Lunas
</div>
@endif
</div>

@if ($isPaid && $order->payment)
<!-- Payment info -->
<div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
<div class="flex items-center gap-2 mb-3 text-emerald-600">
<span class="material-symbols-outlined text-[20px]">paid</span>
<h4 class="text-xs font-semibold uppercase tracking-wider">Pembayaran</h4>
</div>
<div class="space-y-2 text-sm text-slate-500">
<div class="flex justify-between"><span>Metode</span><span class="font-semibold text-[#0b1c30] capitalize">{{ $order->payment->method }}</span></div>
<div class="flex justify-between"><span>Dibayar</span><span class="font-semibold text-[#0b1c30]">Rp {{ number_format((float) $order->payment->amount, 0, ',', '.') }}</span></div>
<div class="flex justify-between"><span>Kembalian</span><span class="font-semibold text-[#0b1c30]">Rp {{ number_format((float) $order->payment->change, 0, ',', '.') }}</span></div>
<div class="flex justify-between"><span>Waktu</span><span class="font-semibold text-[#0b1c30]">{{ optional($order->payment->paid_at)->format('d M Y, H:i') }}</span></div>
</div>
</div>
@endif

@if (!empty($order->note))
<!-- Customer note -->
<div class="bg-[#FFF8F3] border border-[#ffdbca] rounded-2xl p-6">
<div class="flex items-center gap-2 mb-2 text-[#f97316]">
<span class="material-symbols-outlined text-[20px]">edit_note</span>
<h4 class="text-xs font-semibold uppercase tracking-wider">Catatan Pesanan</h4>
</div>
<p class="text-slate-600 italic">"{{ $order->note }}"</p>
</div>
@endif

</div>
</div>
</div>
</x-app-layout>