<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <div x-data="billFilter()" class="px-4 py-6 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-['Poppins'] text-[28px] font-bold text-[#0b1c30] dark:text-slate-100">Daftar Tagihan</h2>
                <p class="mt-1 text-[#584237] dark:text-slate-400">Pilih pesanan yang akan diproses pembayarannya.</p>
            </div>
            <div class="w-full sm:w-72">
                <div class="flex items-center gap-2 rounded-xl border border-[#e0c0b1]/40 bg-white px-3 py-2.5 focus-within:border-[#f97316] dark:border-slate-700 dark:bg-slate-800">
                    <span class="material-symbols-outlined shrink-0 text-[20px] text-[#584237]">search</span>
                    <input type="text" x-model="q" placeholder="Cari kode / meja..." class="w-full border-0 bg-transparent p-0 text-[#0b1c30] placeholder:text-[#584237]/60 focus:ring-0 dark:text-slate-100">
                </div>
            </div>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
        @endif

        {{-- Tabs --}}
        <div class="mb-6 flex gap-2 overflow-x-auto pb-1">
            <button type="button" x-on:click="tab='all'"
                    :class="tab==='all' ? 'bg-[#f97316] text-white shadow' : 'bg-white text-[#584237] border border-[#e0c0b1]/40 hover:bg-[#eff4ff] dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                    class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-colors">Semua ({{ $bills->count() }})</button>
            <button type="button" x-on:click="tab='unpaid'"
                    :class="tab==='unpaid' ? 'bg-[#f97316] text-white shadow' : 'bg-white text-[#584237] border border-[#e0c0b1]/40 hover:bg-[#eff4ff] dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                    class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-colors">Belum Dibayar ({{ $unpaidCount }})</button>
            <button type="button" x-on:click="tab='paid'"
                    :class="tab==='paid' ? 'bg-[#f97316] text-white shadow' : 'bg-white text-[#584237] border border-[#e0c0b1]/40 hover:bg-[#eff4ff] dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                    class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-colors">Selesai ({{ $paidCount }})</button>
        </div>

        {{-- Grid --}}
        @if ($bills->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-[#e0c0b1]/50 bg-white py-16 text-center dark:border-slate-700 dark:bg-slate-800">
                <span class="material-symbols-outlined mb-3 text-[48px] text-[#e0c0b1]">receipt_long</span>
                <p class="font-semibold text-[#0b1c30] dark:text-slate-100">Belum ada tagihan hari ini</p>
                <p class="mt-1 text-sm text-[#584237] dark:text-slate-400">Pesanan baru dari POS akan muncul di sini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($bills as $b)
                    <div x-show='match(@js($b["is_paid"] ? "paid" : "unpaid"), @js($b["search"]))'
                         class="flex flex-col justify-between rounded-2xl border border-[#e0c0b1]/30 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                        <div>
                            <div class="mb-3 flex items-start justify-between">
                                <div>
                                    <h3 class="font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">{{ $b['table_label'] }}</h3>
                                    <p class="text-sm text-[#584237] dark:text-slate-400">{{ $b['code'] }}</p>
                                </div>
                                @if ($b['is_paid'])
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Lunas</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Belum Dibayar</span>
                                @endif
                            </div>
                            <div class="mb-4 flex items-center gap-2 text-sm text-[#584237] dark:text-slate-400">
                                <span class="material-symbols-outlined text-[18px]">schedule</span>
                                <span>{{ $b['time_display'] }}</span>
                                <span class="mx-1 h-1 w-1 rounded-full bg-[#e0c0b1]"></span>
                                <span>{{ $b['item_count'] }} item</span>
                            </div>
                            <div class="mb-4 border-t border-[#e0c0b1]/30 pt-3 dark:border-slate-700">
                                <p class="text-sm text-[#584237] dark:text-slate-400">Total Tagihan</p>
                                <p class="font-['Poppins'] text-2xl font-bold text-[#f97316]">{{ $b['total_display'] }}</p>
                            </div>
                        </div>
                        @if ($b['is_paid'])
                            <a href="{{ route('cashier.show', $b['id']) }}" class="block w-full rounded-xl border border-[#e0c0b1]/50 bg-white py-2.5 text-center font-semibold text-[#584237] transition-colors hover:bg-[#eff4ff] dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200">Lihat Nota</a>
                        @else
                            <a href="{{ route('cashier.show', $b['id']) }}" class="block w-full rounded-xl bg-[#f97316] py-2.5 text-center font-semibold text-white transition-colors hover:bg-[#ea580c]">Bayar</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('billFilter', () => ({
                tab: 'all',
                q: '',
                match(status, text) {
                    const okTab = this.tab === 'all' || this.tab === status;
                    const okQ = this.q === '' || String(text).toLowerCase().includes(this.q.toLowerCase());
                    return okTab && okQ;
                },
            }))
        })
    </script>
</x-app-layout>
