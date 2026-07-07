<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIRESTO — Sistem Informasi Restoran</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="sticky top-0 z-30 border-b border-slate-100 bg-white/80 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500 text-white"><span class="material-symbols-outlined">restaurant</span></span>
                <span class="font-heading text-xl font-extrabold text-slate-800">SIRESTO</span>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-600">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 hover:text-orange-600">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-600">Daftar</a>
                @endauth
            </div>
        </nav>
    </header>

    <section class="mx-auto grid max-w-6xl items-center gap-10 px-6 py-16 lg:grid-cols-2 lg:py-24">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">
                <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span> Sistem Informasi Restoran
            </span>
            <h1 class="mt-5 font-heading text-4xl font-extrabold leading-tight text-slate-900 sm:text-5xl">Kelola restoran Anda <span class="text-orange-500">dalam satu tempat</span></h1>
            <p class="mt-5 max-w-lg text-lg text-slate-500">Dari mencatat pesanan, memantau dapur, mengatur stok, sampai laporan penjualan — SIRESTO bikin operasional restoran jadi rapi, cepat, dan gampang dipantau.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="rounded-xl bg-orange-500 px-6 py-3 font-semibold text-white shadow-lg shadow-orange-500/30 transition hover:bg-orange-600">Mulai Sekarang</a>
                <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 bg-white px-6 py-3 font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-600">Masuk</a>
            </div>
        </div>
        <div class="relative">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-orange-200/60 blur-2xl"></div>
            <div class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-amber-200/50 blur-2xl"></div>
            <div class="relative rounded-3xl border border-slate-100 bg-white p-6 shadow-2xl shadow-slate-200/70">
                <div class="mb-4 flex items-center justify-between">
                    <p class="font-heading font-bold text-slate-800">Ringkasan Hari Ini</p>
                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Live</span>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-orange-50 p-4"><p class="text-xs text-slate-500">Pesanan</p><p class="font-heading text-xl font-bold text-orange-600">128</p></div>
                    <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Meja Terisi</p><p class="font-heading text-xl font-bold text-slate-800">14</p></div>
                    <div class="rounded-2xl bg-green-50 p-4"><p class="text-xs text-slate-500">Omzet</p><p class="font-heading text-xl font-bold text-green-600">8jt</p></div>
                </div>
                <div class="mt-4 flex items-end gap-2">
                    <div class="h-16 w-full rounded-lg bg-orange-200"></div>
                    <div class="h-24 w-full rounded-lg bg-orange-300"></div>
                    <div class="h-12 w-full rounded-lg bg-orange-200"></div>
                    <div class="h-28 w-full rounded-lg bg-orange-500"></div>
                    <div class="h-20 w-full rounded-lg bg-orange-300"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-20">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-500"><span class="material-symbols-outlined">point_of_sale</span></span>
                <h3 class="mt-4 font-heading font-bold text-slate-800">Kasir & POS</h3>
                <p class="mt-1 text-sm text-slate-500">Catat pesanan pelanggan dan proses pembayaran dengan cepat.</p>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-500"><span class="material-symbols-outlined">skillet</span></span>
                <h3 class="mt-4 font-heading font-bold text-slate-800">Dapur (KDS)</h3>
                <p class="mt-1 text-sm text-slate-500">Pantau antrian masakan secara real-time di layar dapur.</p>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-500"><span class="material-symbols-outlined">inventory_2</span></span>
                <h3 class="mt-4 font-heading font-bold text-slate-800">Manajemen Stok</h3>
                <p class="mt-1 text-sm text-slate-500">Kelola stok bahan baku dan dapatkan peringatan stok menipis.</p>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-500"><span class="material-symbols-outlined">assessment</span></span>
                <h3 class="mt-4 font-heading font-bold text-slate-800">Laporan Penjualan</h3>
                <p class="mt-1 text-sm text-slate-500">Lihat ringkasan penjualan harian untuk keputusan bisnis.</p>
            </div>
        </div>
    </section>

    <footer class="border-t border-slate-100 bg-white">
        <div class="mx-auto max-w-6xl px-6 py-8 text-center text-sm text-slate-400">© {{ date('Y') }} SIRESTO — Sistem Informasi Restoran.</div>
    </footer>
</body>
</html>
