<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <div class="mx-auto max-w-[1440px]">
        {{-- Header --}}
        <div class="mb-8">
            <h2 class="font-['Poppins'] text-[32px] font-bold leading-tight text-[#0b1c30] dark:text-slate-100">
                Selamat datang, {{ Auth::user()->name }} 
            </h2>
            <p class="mt-1 text-[#584237] dark:text-slate-400">
                Ringkasan operasional restoran hari ini — {{ now()->translatedFormat('l, d F Y') }}. Peran Anda: <strong>{{ ucfirst(Auth::user()->role) }}</strong>.
            </p>
        </div>

        {{-- KPI cards --}}
        <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Pendapatan hari ini --}}
            <div class="relative overflow-hidden rounded-3xl border border-[#e0c0b1]/20 bg-white p-6 shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-[#f97316]/5 blur-2xl"></div>
                <div class="flex items-start justify-between">
                    <h3 class="font-medium text-[#584237] dark:text-slate-400">Pendapatan Hari Ini</h3>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fff7ed] text-[#f97316]">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <div class="mt-4 font-['Poppins'] text-[28px] font-bold tracking-tight text-[#0b1c30] dark:text-slate-100">
                    Rp {{ number_format($revenueToday, 0, ',', '.') }}
                </div>
            </div>

            {{-- Pesanan aktif --}}
            <div class="rounded-3xl border border-[#e0c0b1]/20 bg-white p-6 shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-start justify-between">
                    <h3 class="font-medium text-[#584237] dark:text-slate-400">Pesanan Aktif</h3>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#d3e4fe] text-[#1e40af]">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                </div>
                <div class="mt-4 font-['Poppins'] text-[28px] font-bold tracking-tight text-[#0b1c30] dark:text-slate-100">
                    {{ number_format($activeOrders, 0, ',', '.') }}
                </div>
                <p class="mt-1 text-sm text-[#584237] dark:text-slate-400">Belum dibayar · hari ini</p>
            </div>

            {{-- Stok menipis --}}
            <div class="rounded-3xl border border-[#e0c0b1]/20 bg-white p-6 shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-start justify-between">
                    <h3 class="font-medium text-[#584237] dark:text-slate-400">Stok Perlu Restock</h3>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl @if ($lowStockCount > 0) bg-red-50 text-[#ba1a1a] @else bg-green-50 text-green-600 @endif">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                </div>
                <div class="mt-4 font-['Poppins'] text-[28px] font-bold tracking-tight text-[#0b1c30] dark:text-slate-100">
                    {{ number_format($lowStockCount, 0, ',', '.') }}
                </div>
                <p class="mt-1 text-sm text-[#584237] dark:text-slate-400">Menipis / habis</p>
            </div>

            {{-- Meja terisi --}}
            <div class="rounded-3xl border border-[#e0c0b1]/20 bg-white p-6 shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-start justify-between">
                    <h3 class="font-medium text-[#584237] dark:text-slate-400">Meja Terisi</h3>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-50 text-green-600">
                        <span class="material-symbols-outlined">table_restaurant</span>
                    </div>
                </div>
                <div class="mt-4 font-['Poppins'] text-[28px] font-bold tracking-tight text-[#0b1c30] dark:text-slate-100">
                    {{ $occupiedTables }} <span class="text-lg font-medium text-[#584237] dark:text-slate-400">/ {{ $totalTables }}</span>
                </div>
                <p class="mt-1 text-sm text-[#584237] dark:text-slate-400">Sedang digunakan</p>
            </div>
        </div>

        {{-- Grid: pesanan terbaru + stok perhatian --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Pesanan terbaru --}}
            <div class="lg:col-span-2 rounded-3xl border border-[#e0c0b1]/20 bg-white shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center justify-between border-b border-[#e0c0b1]/20 px-6 py-5 dark:border-slate-700">
                    <h3 class="font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Pesanan Terbaru Hari Ini</h3>
                    <a href="{{ route('orders.index') }}" class="text-sm font-medium text-[#9d4300] hover:underline dark:text-orange-300">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-[#eff4ff] text-[#584237] dark:bg-slate-700/50 dark:text-slate-300">
                                <th class="px-6 py-3 font-medium">Kode</th>
                                <th class="px-6 py-3 font-medium">Meja</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium">Bayar</th>
                                <th class="px-6 py-3 text-right font-medium">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e0c0b1]/15 dark:divide-slate-700">
                            @forelse ($recentOrders as $o)
                                @php
                                    $tableNo = $o->diningTable?->number;
                                    $statusBadge = match ($o->status) {
                                        'baru'      => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                                        'diproses'  => 'bg-[#fff7ed] text-[#9d4300]',
                                        'siap'      => 'bg-[#e0f2fe] text-[#006398]',
                                        'disajikan' => 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300',
                                        'batal'     => 'bg-red-50 text-[#ba1a1a] dark:bg-red-900/30 dark:text-red-300',
                                        default     => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <tr class="text-[#0b1c30] transition-colors hover:bg-[#f8f9ff] dark:text-slate-200 dark:hover:bg-slate-700/40">
                                    <td class="px-6 py-4 font-medium">{{ $o->code }}</td>
                                    <td class="px-6 py-4">{{ $tableNo ? 'Meja ' . $tableNo : 'Takeaway' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $statusBadge }}">{{ ucfirst($o->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($o->payment)
                                            <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-600 dark:bg-green-900/30 dark:text-green-300">Lunas</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-600 dark:bg-amber-900/30 dark:text-amber-300">Belum</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <span class="material-symbols-outlined mb-2 block text-4xl text-[#e0c0b1]">receipt_long</span>
                                        <p class="text-[#584237] dark:text-slate-400">Belum ada pesanan hari ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Stok perlu perhatian --}}
            <div class="rounded-3xl border border-[#e0c0b1]/20 bg-white shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center justify-between border-b border-[#e0c0b1]/20 px-6 py-5 dark:border-slate-700">
                    <h3 class="font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Stok Perlu Perhatian</h3>
                    <a href="{{ route('stocks.index') }}" class="text-sm font-medium text-[#9d4300] hover:underline dark:text-orange-300">Kelola</a>
                </div>
                <div class="divide-y divide-[#e0c0b1]/15 px-2 dark:divide-slate-700">
                    @forelse ($lowStockItems as $s)
                        <div class="flex items-center justify-between px-4 py-4">
                            <div>
                                <p class="font-medium text-[#0b1c30] dark:text-slate-100">{{ $s->name }}</p>
                                <p class="text-sm text-[#584237] dark:text-slate-400">Min. {{ $s->min_quantity }} {{ $s->unit }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium @if ($s->quantity <= 0) bg-red-50 text-[#ba1a1a] dark:bg-red-900/30 dark:text-red-300 @else bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300 @endif">
                                {{ $s->quantity }} {{ $s->unit }}
                            </span>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <span class="material-symbols-outlined mb-2 block text-4xl text-green-400">check_circle</span>
                            <p class="text-[#584237] dark:text-slate-400">Semua stok aman </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>