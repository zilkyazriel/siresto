<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <div x-data="orderFilter()" class="px-4 py-6 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <h2 class="font-['Poppins'] text-[28px] font-bold text-[#0b1c30] dark:text-slate-100">Daftar Pesanan</h2>
                <p class="mt-1 text-sm text-[#584237] dark:text-slate-400">Pantau dan kelola semua pesanan pelanggan hari ini.</p>
            </div>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 self-start rounded-xl bg-[#f97316] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#ea580c] md:self-auto">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                Pesanan Baru
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">{{ session('info') }}</div>
        @endif

        {{-- Search --}}
        <div class="relative mb-5 max-w-xl">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-[#584237] dark:text-slate-400">search</span>
            <input x-model="q" type="text" placeholder="Cari kode pesanan atau nomor meja..."
                   class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white py-2.5 pl-10 pr-4 text-sm text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        </div>

        {{-- Filter tabs --}}
        <div class="mb-6 flex items-center gap-2 overflow-x-auto pb-2">
            <template x-for="t in tabs" :key="t.key">
                <button type="button" x-on:click="tab = t.key"
                        :class="tab === t.key ? 'bg-[#f97316] text-white shadow-sm' : 'border border-[#e0c0b1]/40 bg-white text-[#584237] hover:border-[#f97316] hover:text-[#f97316] dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                        class="flex shrink-0 items-center gap-2 whitespace-nowrap rounded-xl px-5 py-2.5 text-sm font-semibold transition-all">
                    <span x-text="t.label"></span>
                    <span :class="tab === t.key ? 'bg-white/25 text-white' : 'bg-[#eff4ff] text-[#006398] dark:bg-slate-700 dark:text-slate-200'" class="rounded-lg px-2 py-0.5 text-[10px] font-bold" x-text="t.count"></span>
                </button>
            </template>
        </div>

        @if ($cards->isEmpty())
            <div class="rounded-2xl border border-dashed border-[#e0c0b1]/50 bg-white py-16 text-center dark:border-slate-700 dark:bg-slate-800">
                <span class="material-symbols-outlined text-4xl text-[#584237]/50">receipt_long</span>
                <p class="mt-2 text-sm text-[#584237] dark:text-slate-400">Belum ada pesanan hari ini.</p>
            </div>
        @else
            {{-- Grid kartu --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                @foreach ($cards as $c)
                    <div x-show='match(@js($c["board_status"]), @js($c["search"]))' x-cloak
                         class="flex flex-col rounded-2xl border border-[#e0c0b1]/30 bg-white p-4 shadow-sm transition-all hover:border-[#f97316]/30 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                        <div class="mb-4 flex items-start justify-between gap-2">
                            <div>
                                <p class="mb-1 text-[12px] font-bold uppercase tracking-wider text-[#f97316]">{{ $c['code'] }}</p>
                                <h3 class="font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">{{ $c['table_label'] }}</h3>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold {{ $c['kitchen_badge'] }}">{{ $c['kitchen_label'] }}</span>
                                <span class="whitespace-nowrap rounded-full px-3 py-1 text-[11px] font-semibold {{ $c['is_paid'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $c['is_paid'] ? 'Lunas' : 'Belum Dibayar' }}</span>
                            </div>
                        </div>
                        <div class="mb-5 space-y-2.5">
                            <div class="flex items-center gap-3 text-sm text-[#584237] dark:text-slate-400">
                                <span class="material-symbols-outlined text-[18px]">schedule</span>
                                <span>{{ $c['time_display'] }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-[#584237] dark:text-slate-400">
                                <span class="material-symbols-outlined text-[18px]">restaurant</span>
                                <span>{{ $c['item_count'] }} Item Pesanan</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm font-bold text-[#0b1c30] dark:text-slate-100">
                                <span class="material-symbols-outlined text-[18px]">payments</span>
                                <span>{{ $c['total_display'] }}</span>
                            </div>
                        </div>
                        <div class="mt-auto flex gap-2 border-t border-[#e0c0b1]/30 pt-4 dark:border-slate-700">
                            @if ($c['kitchen'] === 'baru')
                                <form method="POST" action="{{ route('orders.updateStatus', $c['id']) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="status" value="diproses">
                                    <button type="submit" class="w-full rounded-xl bg-[#f97316] py-2 text-sm font-semibold text-white transition-colors hover:bg-[#ea580c]">Proses</button>
                                </form>
                            @elseif ($c['kitchen'] === 'diproses')
                                <form method="POST" action="{{ route('orders.updateStatus', $c['id']) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="status" value="siap">
                                    <button type="submit" class="w-full rounded-xl bg-[#006398] py-2 text-sm font-semibold text-white transition-colors hover:brightness-110">Siap Disajikan</button>
                                </form>
                            @elseif ($c['kitchen'] === 'siap')
                                <form method="POST" action="{{ route('orders.updateStatus', $c['id']) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="status" value="disajikan">
                                    <button type="submit" class="w-full rounded-xl bg-green-600 py-2 text-sm font-semibold text-white transition-colors hover:bg-green-700">Selesaikan</button>
                                </form>
                            @elseif ($c['is_paid'])
                                <span class="flex flex-1 items-center justify-center gap-1 rounded-xl bg-emerald-50 py-2 text-sm font-semibold text-emerald-700"><span class="material-symbols-outlined text-[18px]">check_circle</span> Selesai</span>
                            @else
                                <span class="flex flex-1 items-center justify-center rounded-xl bg-amber-50 py-2 text-center text-xs font-semibold text-amber-700">Menunggu pembayaran di kasir</span>
                            @endif
                            @if (Route::has('orders.show'))
                                <a href="{{ route('orders.show', $c['id']) }}" class="flex items-center justify-center rounded-xl border border-[#e0c0b1]/40 px-3 py-2 text-sm font-semibold text-[#584237] transition-colors hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300"><span class="material-symbols-outlined text-[20px]">visibility</span></a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>[x-cloak]{display:none!important}</style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('orderFilter', () => ({
                tab: 'all',
                q: '',
                tabs: [
                    { key: 'all',       label: 'Semua',     count: {{ $counts['all'] }} },
                    { key: 'baru',      label: 'Baru',      count: {{ $counts['baru'] }} },
                    { key: 'diproses',  label: 'Diproses',  count: {{ $counts['diproses'] }} },
                    { key: 'siap',      label: 'Siap',      count: {{ $counts['siap'] }} },
                    { key: 'disajikan', label: 'Disajikan', count: {{ $counts['disajikan'] }} },
                    { key: 'selesai',   label: 'Selesai',   count: {{ $counts['selesai'] }} },
                ],
                match(status, text) {
                    const okTab = this.tab === 'all' || this.tab === status;
                    const okQ = this.q === '' || text.includes(this.q.toLowerCase());
                    return okTab && okQ;
                },
            }))
        })
    </script>
</x-app-layout>
