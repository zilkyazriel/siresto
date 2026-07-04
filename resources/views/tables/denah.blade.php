<x-app-layout>
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6">

@if (session('success'))
<div class="mb-5 flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
<span class="material-symbols-outlined text-[20px]">check_circle</span>{{ session('success') }}
</div>
@endif

<!-- Header + Legend -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
<div>
<h2 class="text-3xl font-bold font-['Poppins'] text-[#0b1c30]">Denah Meja</h2>
<p class="text-slate-500 mt-1">Pantau status & pesanan tiap meja secara langsung.</p>
</div>
<div class="flex flex-wrap items-center gap-4 bg-white px-4 py-2.5 rounded-xl shadow-sm border border-slate-100">
<div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span><span class="text-sm text-[#0b1c30]">Tersedia ({{ $availableCount }})</span></div>
<div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#f97316]"></span><span class="text-sm text-[#0b1c30]">Terisi ({{ $occupiedCount }})</span></div>
</div>
</div>

@if ($cards->isEmpty())
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
<span class="material-symbols-outlined text-5xl text-slate-300">table_restaurant</span>
<p class="mt-3 text-slate-500">Belum ada meja. Tambahkan meja dulu di halaman <span class="font-semibold">Meja</span>.</p>
@if (Route::has('tables.index'))
<a href="{{ route('tables.index') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-[#f97316] text-white font-semibold rounded-xl hover:bg-[#ea580c] transition-colors"><span class="material-symbols-outlined text-[18px]">add</span>Kelola Meja</a>
@endif
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
@foreach ($cards as $card)
@if ($card['occupied'])
<!-- Terisi -->
<div class="bg-orange-50 rounded-2xl p-4 border-l-4 border-[#f97316] shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between min-h-[170px]">
<div class="flex justify-between items-start mb-2">
<div>
<h3 class="text-xl font-bold font-['Poppins'] text-[#0b1c30]">Meja {{ $card['number'] }}</h3>
<p class="text-xs font-bold text-[#f97316] mt-1">{{ $card['order_code'] }}</p>
</div>
<span class="px-2.5 py-1 rounded-full bg-orange-200 text-orange-800 text-xs font-semibold">Terisi</span>
</div>
<div class="mb-3">
<p class="text-sm font-semibold text-[#0b1c30]">{{ $card['total_display'] }}</p>
<p class="text-xs text-slate-500">{{ $card['item_count'] }} Item</p>
</div>
@if (Route::has('orders.show') && $card['order_id'])
<a href="{{ route('orders.show', $card['order_id']) }}" class="w-full text-center bg-white text-[#0b1c30] font-semibold py-2 rounded-xl hover:bg-slate-100 transition-colors mt-auto border border-slate-200 shadow-sm flex items-center justify-center gap-1.5">
<span class="material-symbols-outlined text-[18px]">visibility</span>Lihat Pesanan
</a>
@endif
</div>
@else
<!-- Tersedia -->
<div class="bg-white rounded-2xl p-4 border-l-4 border-emerald-500 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between min-h-[170px]">
<div class="flex justify-between items-start mb-2">
<div>
<h3 class="text-xl font-bold font-['Poppins'] text-[#0b1c30]">Meja {{ $card['number'] }}</h3>
<p class="text-sm text-slate-500 flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[16px]">group</span>Kapasitas: {{ $card['capacity'] }} Orang</p>
</div>
<span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Tersedia</span>
</div>
@if (Route::has('orders.create'))
<a href="{{ route('orders.create') }}" class="w-full text-center bg-[#f97316] text-white font-semibold py-2 rounded-xl hover:bg-[#ea580c] transition-colors mt-auto flex items-center justify-center gap-1.5">
<span class="material-symbols-outlined text-[18px]">add</span>Buat Pesanan
</a>
@endif
</div>
@endif
@endforeach
</div>
@endif

</div>
</x-app-layout>