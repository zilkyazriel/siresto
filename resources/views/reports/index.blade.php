<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <div class="mx-auto max-w-[1440px]">
        {{-- Header --}}
        <div class="mb-8">
            <h2 class="font-['Poppins'] text-[32px] font-bold leading-tight text-[#0b1c30] dark:text-slate-100">Laporan Pendapatan</h2>
            <p class="mt-1 text-[#584237] dark:text-slate-400">Analisis pendapatan, transaksi, dan tren penjualan restoran Anda.</p>
        </div>

        {{-- Action bar: period toggle + export --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="inline-flex items-center gap-1 rounded-xl border border-[#e0c0b1]/40 bg-white p-1 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                @foreach (['today' => 'Hari Ini', '7d' => '7 Hari', '30d' => '30 Hari', 'month' => 'Bulan Ini', 'year' => 'Tahun Ini'] as $key => $label)
                    <a href="{{ route('reports.index', ['period' => $key]) }}"
                       class="rounded-lg px-4 py-2 text-sm font-medium transition-colors @if ($period === $key) bg-[#f97316] text-white shadow @else text-[#584237] hover:bg-[#e5eeff] dark:text-slate-300 dark:hover:bg-slate-700 @endif">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('reports.export', ['period' => $period]) }}"
               class="inline-flex items-center gap-2 rounded-xl border border-[#e0c0b1]/60 bg-white px-4 py-2.5 text-sm font-medium text-[#9d4300] transition-colors hover:bg-[#fff7ed] dark:border-slate-700 dark:bg-slate-800 dark:text-orange-300">
                <span class="material-symbols-outlined text-[20px]">download</span>
                Ekspor CSV
            </a>
        </div>

        {{-- Summary cards --}}
        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
            {{-- Total Pendapatan --}}
            <div class="relative overflow-hidden rounded-3xl border border-[#e0c0b1]/20 bg-white p-6 shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                <div class="absolute -right-6 -top-6 h-32 w-32 rounded-full bg-[#f97316]/5 blur-2xl"></div>
                <div class="flex items-start justify-between">
                    <h3 class="font-medium text-[#584237] dark:text-slate-400">Total Pendapatan</h3>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fff7ed] text-[#f97316]">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="font-['Poppins'] text-[32px] font-bold tracking-tight text-[#0b1c30] dark:text-slate-100">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
                    @if (!is_null($revenueGrowth))
                        <div class="mt-2 flex items-center gap-2 text-sm">
                            <span class="flex items-center gap-0.5 rounded-full px-2 py-0.5 font-medium @if ($revenueGrowth >= 0) bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300 @else bg-red-50 text-[#ba1a1a] dark:bg-red-900/30 dark:text-red-300 @endif">
                                <span class="material-symbols-outlined text-[16px]">@if ($revenueGrowth >= 0) trending_up @else trending_down @endif</span>
                                @if ($revenueGrowth >= 0)+@endif{{ number_format($revenueGrowth, 1) }}%
                            </span>
                            <span class="text-[#584237] dark:text-slate-400">dari periode lalu</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Jumlah Transaksi --}}
            <div class="relative overflow-hidden rounded-3xl border border-[#e0c0b1]/20 bg-white p-6 shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-start justify-between">
                    <h3 class="font-medium text-[#584237] dark:text-slate-400">Jumlah Transaksi</h3>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#d3e4fe] text-[#584237]">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="font-['Poppins'] text-[32px] font-bold tracking-tight text-[#0b1c30] dark:text-slate-100">{{ number_format($count, 0, ',', '.') }}</div>
                    <div class="mt-2 flex items-center gap-2 text-sm text-[#584237] dark:text-slate-400">
                        @if (!is_null($countGrowth))
                            <span class="flex items-center gap-0.5 rounded-full px-2 py-0.5 font-medium @if ($countGrowth >= 0) bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300 @else bg-red-50 text-[#ba1a1a] dark:bg-red-900/30 dark:text-red-300 @endif">
                                <span class="material-symbols-outlined text-[16px]">@if ($countGrowth >= 0) trending_up @else trending_down @endif</span>
                                @if ($countGrowth >= 0)+@endif{{ number_format($countGrowth, 1) }}%
                            </span>
                        @endif
                        <span>Transaksi berhasil</span>
                    </div>
                </div>
            </div>

            {{-- Rata-rata per Transaksi --}}
            <div class="relative overflow-hidden rounded-3xl border border-[#e0c0b1]/20 bg-white p-6 shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-start justify-between">
                    <h3 class="font-medium text-[#584237] dark:text-slate-400">Rata-rata / Transaksi</h3>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#d3e4fe] text-[#584237]">
                        <span class="material-symbols-outlined">shopping_basket</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="font-['Poppins'] text-[32px] font-bold tracking-tight text-[#0b1c30] dark:text-slate-100">Rp {{ number_format($avg, 0, ',', '.') }}</div>
                    @if (!is_null($avgGrowth))
                        <div class="mt-2 flex items-center gap-2 text-sm">
                            <span class="flex items-center gap-0.5 rounded-full px-2 py-0.5 font-medium @if ($avgGrowth >= 0) bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300 @else bg-red-50 text-[#ba1a1a] dark:bg-red-900/30 dark:text-red-300 @endif">
                                <span class="material-symbols-outlined text-[16px]">@if ($avgGrowth >= 0) trending_up @else trending_down @endif</span>
                                @if ($avgGrowth >= 0)+@endif{{ number_format($avgGrowth, 1) }}%
                            </span>
                            <span class="text-[#584237] dark:text-slate-400">dari periode lalu</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tren Pendapatan chart --}}
        <div class="mb-6 rounded-3xl border border-[#e0c0b1]/20 bg-white p-6 shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Tren Pendapatan</h3>
                <span class="flex items-center gap-2 text-sm font-medium text-[#584237] dark:text-slate-400">
                    <span class="inline-block h-3 w-3 rounded-full bg-[#f97316]"></span> Pendapatan
                </span>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Daftar Transaksi --}}
        <div class="rounded-3xl border border-[#e0c0b1]/20 bg-white shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between border-b border-[#e0c0b1]/20 px-6 py-5 dark:border-slate-700">
                <h3 class="font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Daftar Transaksi</h3>
                <span class="text-sm text-[#584237] dark:text-slate-400">{{ $transactions->total() }} transaksi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-[#eff4ff] text-[#584237] dark:bg-slate-700/50 dark:text-slate-300">
                            <th class="px-6 py-3 font-medium">Tanggal</th>
                            <th class="px-6 py-3 font-medium">Kode Pesanan</th>
                            <th class="px-6 py-3 font-medium">Meja</th>
                            <th class="px-6 py-3 font-medium">Metode Bayar</th>
                            <th class="px-6 py-3 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e0c0b1]/15 dark:divide-slate-700">
                        @forelse ($transactions as $trx)
                            @php
                                $date = $trx->paid_at ?? $trx->created_at;
                                $tableNo = $trx->order?->diningTable?->number;
                                $mejaLabel = $tableNo ? 'Meja ' . $tableNo : 'Takeaway';
                                $method = $trx->method;
                                $methodLabel = match ($method) {
                                    'cash', 'tunai' => 'Tunai',
                                    'qris' => 'QRIS',
                                    'debit' => 'Debit',
                                    'card', 'kartu' => 'Kartu',
                                    default => $method ? ucfirst($method) : '-',
                                };
                                $badge = match ($method) {
                                    'qris' => 'bg-[#e0f2fe] text-[#006398]',
                                    'debit' => 'bg-[#e5eeff] text-[#1e40af]',
                                    'card', 'kartu' => 'bg-[#f3e8ff] text-[#6b21a8]',
                                    default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                                };
                            @endphp
                            <tr class="text-[#0b1c30] transition-colors hover:bg-[#f8f9ff] dark:text-slate-200 dark:hover:bg-slate-700/40">
                                <td class="whitespace-nowrap px-6 py-4">{{ optional($date)->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 font-medium">{{ $trx->order?->code ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $mejaLabel }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $badge }}">{{ $methodLabel }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <span class="material-symbols-outlined mb-2 block text-4xl text-[#e0c0b1]">receipt_long</span>
                                    <p class="text-[#584237] dark:text-slate-400">Belum ada transaksi pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="border-t border-[#e0c0b1]/20 px-6 py-4 dark:border-slate-700">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('revenueChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 288);
            gradient.addColorStop(0, 'rgba(249, 115, 22, 0.35)');
            gradient.addColorStop(1, 'rgba(249, 115, 22, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [{
                        label: 'Pendapatan',
                        data: @json($trendValues),
                        borderColor: '#f97316',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#f97316',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#584237' } },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#584237',
                                callback: function (value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                                }
                            },
                            grid: { color: 'rgba(224, 192, 177, 0.15)' }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
