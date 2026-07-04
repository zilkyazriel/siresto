<!DOCTYPE html>
<html lang="id" class="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIRESTO - Kitchen Display System</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
<style>
body { background-color: #0f172a; font-family: 'Inter', sans-serif; }
[x-cloak] { display: none !important; }
::-webkit-scrollbar { width: 8px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
</style>
</head>
<body class="h-screen flex overflow-hidden text-slate-100" x-data="kds()">
<nav class="hidden md:flex bg-slate-900 h-screen w-64 fixed left-0 top-0 flex-col py-4 px-2 overflow-y-auto border-r border-slate-800 z-50">
<div class="mb-8 px-2">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[#f97316] text-3xl" style="font-variation-settings: 'FILL' 1;">restaurant</span>
<div>
<h2 class="text-xl font-bold font-['Poppins'] text-slate-100">SIRESTO</h2>
<p class="text-xs text-slate-400">Restaurant Management</p>
</div>
</div>
</div>
<div class="flex flex-col gap-1 flex-1">
<a class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors" href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}">
<span class="material-symbols-outlined">dashboard</span>
<span class="text-sm font-semibold">Dashboard</span>
</a>
<a class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors" href="{{ Route::has('menus.index') ? route('menus.index') : '#' }}">
<span class="material-symbols-outlined">restaurant_menu</span>
<span class="text-sm font-semibold">Menu</span>
</a>
<a class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors" href="{{ Route::has('categories.index') ? route('categories.index') : '#' }}">
<span class="material-symbols-outlined">category</span>
<span class="text-sm font-semibold">Kategori</span>
</a>
<a class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors" href="{{ Route::has('tables.index') ? route('tables.index') : '#' }}">
<span class="material-symbols-outlined">table_restaurant</span>
<span class="text-sm font-semibold">Meja</span>
</a>
<a class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors" href="{{ Route::has('orders.index') ? route('orders.index') : '#' }}">
<span class="material-symbols-outlined">receipt_long</span>
<span class="text-sm font-semibold">Pesanan</span>
</a>
<a class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-[#f97316] font-bold border-r-4 border-[#f97316] bg-[#f97316]/10" href="{{ Route::has('kitchen.index') ? route('kitchen.index') : '#' }}">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">skillet</span>
<span class="text-sm font-semibold">Dapur</span>
</a>
<a class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors" href="{{ Route::has('cashier.index') ? route('cashier.index') : '#' }}">
<span class="material-symbols-outlined">payments</span>
<span class="text-sm font-semibold">Kasir</span>
</a>
<a class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors" href="{{ Route::has('reports.index') ? route('reports.index') : '#' }}">
<span class="material-symbols-outlined">assessment</span>
<span class="text-sm font-semibold">Laporan</span>
</a>
<a class="flex items-center gap-4 px-4 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors" href="{{ Route::has('staff.index') ? route('staff.index') : '#' }}">
<span class="material-symbols-outlined">badge</span>
<span class="text-sm font-semibold">Staf</span>
</a>
</div>
</nav>
<main class="flex-1 md:ml-64 flex flex-col h-screen">
<header class="bg-slate-900 border-b border-slate-800 flex justify-between items-center w-full h-16 px-6 sticky top-0 z-40">
<h1 class="text-2xl font-bold font-['Poppins'] text-slate-100">Kitchen Display System</h1>
<div class="flex items-center gap-4">
<div class="flex items-center gap-2 text-amber-500 bg-amber-500/10 px-4 py-2 rounded-full">
<span class="material-symbols-outlined">pending_actions</span>
<span class="text-sm font-semibold">{{ $antri->count() + $dimasak->count() + $siap->count() }} Antri</span>
</div>
<div class="hidden sm:flex items-center gap-2 text-slate-400 text-xs">
<span class="material-symbols-outlined text-[18px] animate-spin" style="animation-duration:3s">autorenew</span>
Auto-refresh 30s
</div>
<a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="text-slate-400 hover:text-slate-100" title="Keluar KDS">
<span class="material-symbols-outlined">logout</span>
</a>
</div>
</header>
<div class="flex-1 overflow-hidden p-6">
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-full">
<div class="flex flex-col h-full bg-slate-800/50 rounded-2xl border border-slate-700/50 overflow-hidden">
<div class="bg-amber-500/20 border-b border-amber-500/30 p-4 flex justify-between items-center">
<h2 class="text-xl font-bold font-['Poppins'] text-amber-500 uppercase tracking-wider">ANTRI</h2>
<span class="bg-amber-500 text-slate-900 px-3 py-1 rounded-full text-sm font-semibold">{{ $antri->count() }}</span>
</div>
<div class="flex-1 overflow-y-auto p-4 space-y-4">
@forelse ($antri as $c)
<div class="bg-slate-800 rounded-2xl p-4 border border-slate-700 shadow-lg relative overflow-hidden">
<div class="absolute top-0 left-0 w-2 h-full bg-amber-500"></div>
<div class="flex justify-between items-start mb-4 pl-2" x-data="{ start: {{ $c['created_ms'] }} }">
<div>
<h3 class="text-4xl font-bold font-['Poppins'] text-slate-100">{{ $c['table_label'] }}</h3>
<p class="text-sm text-slate-400">{{ $c['code'] }}</p>
</div>
<div class="flex items-center gap-1 px-3 py-1 rounded-lg font-mono text-sm shrink-0" :class="isLate(now - start) ? 'text-red-400 bg-red-500/20 border border-red-500/30 animate-pulse' : 'text-slate-300 bg-slate-700'">
<span class="material-symbols-outlined text-sm">timer</span>
<span x-text="fmt(now - start)"></span>
</div>
</div>
<div class="space-y-2 mb-5 pl-2 border-t border-slate-700 pt-4">
@foreach ($c['items'] as $it)
<div class="flex items-start gap-2">
<span class="text-sm font-semibold text-amber-400 mt-1 shrink-0">{{ $it['qty'] }}x</span>
<p class="text-base text-slate-200">{{ $it['name'] }}@if (!empty($it['note']))<br><span class="text-sm text-slate-400 italic">{{ $it['note'] }}</span>@endif</p>
</div>
@endforeach
</div>
<form method="POST" action="{{ route('orders.updateStatus', $c['id']) }}">
@csrf
<input type="hidden" name="status" value="diproses">
<button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white text-base font-semibold py-2.5 rounded-xl transition-colors active:scale-95 shadow-md">Mulai Masak</button>
</form>
</div>
@empty
<div class="flex flex-col items-center justify-center h-full text-slate-600 py-10">
<span class="material-symbols-outlined text-4xl mb-2">inbox</span>
<p class="text-sm">Tidak ada pesanan</p>
</div>
@endforelse
</div>
</div>
<div class="flex flex-col h-full bg-slate-800/50 rounded-2xl border border-slate-700/50 overflow-hidden">
<div class="bg-blue-500/20 border-b border-blue-500/30 p-4 flex justify-between items-center">
<h2 class="text-xl font-bold font-['Poppins'] text-blue-400 uppercase tracking-wider">DIMASAK</h2>
<span class="bg-blue-500 text-slate-900 px-3 py-1 rounded-full text-sm font-semibold">{{ $dimasak->count() }}</span>
</div>
<div class="flex-1 overflow-y-auto p-4 space-y-4">
@forelse ($dimasak as $c)
<div class="bg-slate-800 rounded-2xl p-4 border border-slate-700 shadow-lg relative overflow-hidden">
<div class="absolute top-0 left-0 w-2 h-full bg-blue-500"></div>
<div class="flex justify-between items-start mb-4 pl-2" x-data="{ start: {{ $c['created_ms'] }} }">
<div>
<h3 class="text-4xl font-bold font-['Poppins'] text-slate-100">{{ $c['table_label'] }}</h3>
<p class="text-sm text-slate-400">{{ $c['code'] }}</p>
</div>
<div class="flex items-center gap-1 px-3 py-1 rounded-lg font-mono text-sm shrink-0" :class="isLate(now - start) ? 'text-red-400 bg-red-500/20 border border-red-500/30 animate-pulse' : 'text-slate-300 bg-slate-700'">
<span class="material-symbols-outlined text-sm">timer</span>
<span x-text="fmt(now - start)"></span>
</div>
</div>
<div class="space-y-2 mb-5 pl-2 border-t border-slate-700 pt-4">
@foreach ($c['items'] as $it)
<div class="flex items-start gap-2">
<span class="text-sm font-semibold text-blue-400 mt-1 shrink-0">{{ $it['qty'] }}x</span>
<p class="text-base text-slate-200">{{ $it['name'] }}@if (!empty($it['note']))<br><span class="text-sm text-slate-400 italic">{{ $it['note'] }}</span>@endif</p>
</div>
@endforeach
</div>
<form method="POST" action="{{ route('orders.updateStatus', $c['id']) }}">
@csrf
<input type="hidden" name="status" value="siap">
<button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-base font-semibold py-2.5 rounded-xl transition-colors active:scale-95 shadow-md flex items-center justify-center gap-2"><span class="material-symbols-outlined text-base">check_circle</span>Tandai Siap</button>
</form>
</div>
@empty
<div class="flex flex-col items-center justify-center h-full text-slate-600 py-10">
<span class="material-symbols-outlined text-4xl mb-2">inbox</span>
<p class="text-sm">Tidak ada pesanan</p>
</div>
@endforelse
</div>
</div>
<div class="flex flex-col h-full bg-slate-800/50 rounded-2xl border border-slate-700/50 overflow-hidden">
<div class="bg-emerald-500/20 border-b border-emerald-500/30 p-4 flex justify-between items-center">
<h2 class="text-xl font-bold font-['Poppins'] text-emerald-400 uppercase tracking-wider">SIAP</h2>
<span class="bg-emerald-500 text-slate-900 px-3 py-1 rounded-full text-sm font-semibold">{{ $siap->count() }}</span>
</div>
<div class="flex-1 overflow-y-auto p-4 space-y-4">
@forelse ($siap as $c)
<div class="bg-slate-800 rounded-2xl p-4 border border-slate-700 shadow-lg relative overflow-hidden">
<div class="absolute top-0 left-0 w-2 h-full bg-emerald-500"></div>
<div class="flex justify-between items-start mb-4 pl-2" x-data="{ start: {{ $c['created_ms'] }} }">
<div>
<h3 class="text-4xl font-bold font-['Poppins'] text-slate-100">{{ $c['table_label'] }}</h3>
<p class="text-sm text-slate-400">{{ $c['code'] }}</p>
</div>
<div class="flex items-center gap-1 px-3 py-1 rounded-lg font-mono text-sm shrink-0" :class="isLate(now - start) ? 'text-red-400 bg-red-500/20 border border-red-500/30 animate-pulse' : 'text-slate-300 bg-slate-700'">
<span class="material-symbols-outlined text-sm">timer</span>
<span x-text="fmt(now - start)"></span>
</div>
</div>
<div class="space-y-2 mb-5 pl-2 border-t border-slate-700 pt-4">
@foreach ($c['items'] as $it)
<div class="flex items-start gap-2">
<span class="text-sm font-semibold text-emerald-400 mt-1 shrink-0">{{ $it['qty'] }}x</span>
<p class="text-base text-slate-200">{{ $it['name'] }}@if (!empty($it['note']))<br><span class="text-sm text-slate-400 italic">{{ $it['note'] }}</span>@endif</p>
</div>
@endforeach
</div>
<form method="POST" action="{{ route('orders.updateStatus', $c['id']) }}">
@csrf
<input type="hidden" name="status" value="disajikan">
<button type="submit" class="w-full bg-slate-700 hover:bg-slate-600 text-emerald-400 border border-emerald-500/30 text-base font-semibold py-2.5 rounded-xl transition-colors active:scale-95 shadow-md flex items-center justify-center gap-2"><span class="material-symbols-outlined text-base">room_service</span>Selesaikan</button>
</form>
</div>
@empty
<div class="flex flex-col items-center justify-center h-full text-slate-600 py-10">
<span class="material-symbols-outlined text-4xl mb-2">inbox</span>
<p class="text-sm">Tidak ada pesanan</p>
</div>
@endforelse
</div>
</div>
</div>
</div>
</main>
<script>
document.addEventListener('alpine:init', () => {
Alpine.data('kds', () => ({
now: Date.now(),
init() { setInterval(() => { this.now = Date.now() }, 1000); setTimeout(() => location.reload(), 30000); },
fmt(ms) { if (ms < 0) ms = 0; const s = Math.floor(ms / 1000); const m = Math.floor(s / 60); const sec = s % 60; return String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0'); },
isLate(ms) { return ms > 15 * 60 * 1000; },
}))
})
</script>
</body>
</html>