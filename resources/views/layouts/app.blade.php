<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SIRESTO') }}</title>

    <!-- Cegah kedip dark mode -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <!-- Font & Ikon -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">

    <!-- ===== Overlay (hanya mobile) ===== -->
    <div id="sidebarOverlay" onclick="closeSidebar()"
         class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>

    <!-- ===== Sidebar ===== -->
    <aside id="sidebar"
           class="fixed left-0 top-0 z-50 flex h-screen w-64 -translate-x-full flex-col rounded-r-2xl bg-white py-6 shadow-lg transition-transform duration-300 dark:bg-slate-800 lg:translate-x-0">
        <div class="mb-8 flex items-center justify-between px-6">
            <div class="text-center">
                <h1 class="font-heading text-2xl font-bold text-orange-500">SIRESTO</h1>
                <p class="text-sm text-slate-400">Management System</p>
            </div>
            <button onclick="closeSidebar()" aria-label="Tutup menu"
                    class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 lg:hidden">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-2">
            @php
                $nav = [
                    ['route' => 'dashboard',        'label' => 'Dashboard', 'icon' => 'dashboard'],
                    ['route' => 'menus.index',      'label' => 'Menu',      'icon' => 'restaurant_menu'],
                    ['route' => 'categories.index', 'label' => 'Kategori',  'icon' => 'category'],
                    ['route' => 'tables.index',     'label' => 'Meja',      'icon' => 'table_restaurant'],
                    ['route' => 'orders.index',     'label' => 'Pesanan',   'icon' => 'receipt_long'],
                    ['route' => 'kitchen.index',    'label' => 'Dapur',     'icon' => 'skillet'],
                    ['route' => 'cashier.index',    'label' => 'Kasir',     'icon' => 'payments'],
                    ['route' => 'stocks.index',     'label' => 'Stok',      'icon' => 'inventory_2'],
                    ['route' => 'suppliers.index',  'label' => 'Supplier',  'icon' => 'local_shipping'],
                    ['route' => 'reports.index',    'label' => 'Laporan',   'icon' => 'assessment'],
                    ['route' => 'staff.index',      'label' => 'Staf',      'icon' => 'group'],
                ];
            @endphp

            @foreach ($nav as $item)
                @php
                    $exists = \Illuminate\Support\Facades\Route::has($item['route']);
                    $active = $exists && request()->routeIs($item['route']);
                    $href = $exists ? route($item['route']) : '#';
                @endphp
                <a href="{{ $href }}"
                   class="flex items-center gap-3 rounded-xl px-4 py-3 transition-colors {{ $active ? 'bg-orange-500 text-white shadow' : 'text-slate-500 hover:bg-orange-50 hover:text-orange-600 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                    <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                    <span class="text-sm font-semibold">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="mt-4 space-y-1 border-t border-slate-100 px-2 pt-4 dark:border-slate-700">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 rounded-xl px-4 py-3 text-slate-500 transition-colors hover:bg-orange-50 hover:text-orange-600 dark:text-slate-400 dark:hover:bg-slate-700">
                <span class="material-symbols-outlined">account_circle</span>
                <span class="text-sm font-semibold">Profil Saya</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-slate-500 transition-colors hover:bg-red-50 hover:text-red-600 dark:text-slate-400 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-sm font-semibold">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ===== Top bar ===== -->
    <header class="fixed left-0 right-0 top-0 z-30 flex h-16 items-center justify-between bg-white px-4 shadow-sm dark:bg-slate-800 md:px-8 lg:left-64">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" aria-label="Buka menu"
                    class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700 lg:hidden">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div class="relative hidden w-96 md:block">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" placeholder="Cari pesanan, menu, atau meja..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-4 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 md:gap-4">
            <button onclick="toggleDarkMode()" aria-label="Ganti tema"
                    class="rounded-full p-2 text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700">
                <span class="material-symbols-outlined">dark_mode</span>
            </button>
            <button aria-label="Notifikasi"
                    class="relative rounded-full p-2 text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-red-500"></span>
            </button>
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-500 font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="hidden text-left md:block">
                    <div class="text-sm font-semibold">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-400">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== Konten utama ===== -->
    <main class="ml-0 mt-16 min-h-screen p-4 md:p-8 lg:ml-64">
        @isset($header)
            <div class="mb-6">{{ $header }}</div>
        @endisset
        {{ $slot }}
    </main>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.remove('hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
        function toggleSidebar() {
            document.getElementById('sidebar').classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
        }
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        }
        // Tutup sidebar otomatis saat klik menu di HP
        document.querySelectorAll('#sidebar a').forEach(function (el) {
            el.addEventListener('click', function () {
                if (window.innerWidth < 1024) closeSidebar();
            });
        });
    </script>
</body>
</html>
