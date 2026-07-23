<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <div x-data="payment({ total: {{ (int) $total }} })" class="px-4 py-6 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('cashier.index') }}" class="flex h-9 w-9 items-center justify-center rounded-full border border-[#e0c0b1]/40 text-[#584237] hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <h2 class="font-['Poppins'] text-[28px] font-bold text-[#0b1c30] dark:text-slate-100">Pembayaran</h2>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- KIRI: Ringkasan --}}
            <div class="lg:col-span-7">
                <div class="rounded-2xl border border-[#e0c0b1]/30 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="mb-5 flex items-start justify-between border-b border-[#e0c0b1]/30 pb-4 dark:border-slate-700">
                        <div>
                            <h3 class="font-['Poppins'] text-xl font-bold text-[#0b1c30] dark:text-slate-100">{{ $order->diningTable ? 'Meja '.$order->diningTable->number : 'Takeaway' }}</h3>
                            <p class="mt-0.5 text-sm text-[#584237] dark:text-slate-400">Kode Pesanan: {{ $order->code }}</p>
                        </div>
                        <span class="rounded-full bg-[#eff4ff] px-3 py-1 text-xs font-semibold text-[#006398] dark:bg-slate-700 dark:text-slate-200">{{ $order->dining_table_id ? 'Dine In' : 'Takeaway' }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-[#e0c0b1]/30 text-xs uppercase text-[#584237] dark:border-slate-700 dark:text-slate-400">
                                    <th class="py-2 font-semibold">Nama Menu</th>
                                    <th class="py-2 text-center font-semibold">Qty</th>
                                    <th class="py-2 text-right font-semibold">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-[#0b1c30] dark:text-slate-200">
                                @foreach ($order->items as $item)
                                    <tr class="border-b border-[#e0c0b1]/20 dark:border-slate-700/60">
                                        <td class="py-3">{{ $menuNames[$item->menu_id] ?? 'Menu #'.$item->menu_id }}</td>
                                        <td class="py-3 text-center">{{ $item->quantity }}</td>
                                        <td class="py-3 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-5 flex flex-col gap-2 border-t border-[#e0c0b1]/30 pt-4 dark:border-slate-700">
                        <div class="flex justify-between text-sm text-[#584237] dark:text-slate-400">
                            <span>Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-[#584237] dark:text-slate-400">
                            <span>Pajak (10%)</span><span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-2 flex items-end justify-between">
                            <span class="font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Total</span>
                            <span class="font-['Poppins'] text-2xl font-bold text-[#f97316]">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: Proses --}}
            <div class="lg:col-span-5">
                <form method="POST" action="{{ route('cashier.pay', $order->id) }}" class="flex h-full flex-col rounded-2xl border border-[#e0c0b1]/30 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    @csrf
                    <input type="hidden" name="method" :value="method">
                    <input type="hidden" name="received" :value="method === 'tunai' ? received : total">
                    <h3 class="mb-4 font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Pembayaran</h3>

                    {{-- Metode --}}
                    <label class="mb-2 block text-sm font-semibold text-[#584237] dark:text-slate-400">Metode Pembayaran</label>
                    <div class="mb-6 flex gap-1 rounded-xl border border-[#e0c0b1]/40 bg-[#f8f9ff] p-1 dark:border-slate-700 dark:bg-slate-900">
                        <button type="button" x-on:click="method = 'tunai'" :class="method === 'tunai' ? 'bg-white text-[#0b1c30] shadow-sm dark:bg-slate-700 dark:text-white' : 'text-[#584237] dark:text-slate-400'" class="flex-1 rounded-lg py-2 text-sm font-semibold transition-colors">Tunai</button>
                        <button type="button" x-on:click="method = 'kartu'" :class="method === 'kartu' ? 'bg-white text-[#0b1c30] shadow-sm dark:bg-slate-700 dark:text-white' : 'text-[#584237] dark:text-slate-400'" class="flex-1 rounded-lg py-2 text-sm font-semibold transition-colors">Kartu</button>
                        <button type="button" x-on:click="method = 'qris'" :class="method === 'qris' ? 'bg-white text-[#0b1c30] shadow-sm dark:bg-slate-700 dark:text-white' : 'text-[#584237] dark:text-slate-400'" class="flex-1 rounded-lg py-2 text-sm font-semibold transition-colors">QRIS</button>
                    </div>

                    {{-- Tunai: uang diterima --}}
                    <div x-show="method === 'tunai'" x-cloak>
                        <label class="mb-2 block text-sm font-semibold text-[#584237] dark:text-slate-400">Uang Diterima</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#584237] dark:text-slate-400">Rp</span>
                            <input type="text" inputmode="numeric" x-model="receivedDisplay" x-on:input="onInput($event)"
                                   class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white py-3 pl-12 pr-4 text-lg font-bold text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="0">
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" x-on:click="setAmount(total)" class="rounded-lg border border-[#e0c0b1]/40 px-3 py-1.5 text-xs font-semibold text-[#584237] hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300">Uang Pas</button>
                            <button type="button" x-on:click="setAmount(50000)" class="rounded-lg border border-[#e0c0b1]/40 px-3 py-1.5 text-xs font-semibold text-[#584237] hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300">50.000</button>
                            <button type="button" x-on:click="setAmount(100000)" class="rounded-lg border border-[#e0c0b1]/40 px-3 py-1.5 text-xs font-semibold text-[#584237] hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300">100.000</button>
                        </div>
                        <div class="mt-4 rounded-xl border border-[#e0c0b1]/30 bg-[#f8f9ff] p-4 text-center dark:border-slate-700 dark:bg-slate-900">
                            <span class="block text-xs font-semibold uppercase text-[#584237] dark:text-slate-400">Kembalian</span>
                            <span class="mt-1 block font-['Poppins'] text-2xl font-bold" :class="change < 0 ? 'text-[#ba1a1a]' : 'text-[#0b1c30] dark:text-slate-100'" x-text="formatRp(change)"></span>
                        </div>
                    </div>

                    {{-- Non-tunai --}}
                    <div x-show="method !== 'tunai'" x-cloak class="space-y-4">
                        <div class="rounded-xl border border-[#e0c0b1]/30 bg-[#f8f9ff] p-4 text-center dark:border-slate-700 dark:bg-slate-900">
                            <span class="block text-xs font-semibold uppercase text-[#584237] dark:text-slate-400">Total Dibayar</span>
                            <span class="mt-1 block font-['Poppins'] text-2xl font-bold text-[#0b1c30] dark:text-slate-100">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#584237] dark:text-slate-400">
                                No. Referensi <span x-text="method === 'qris' ? 'QRIS' : 'Kartu'"></span>
                            </label>
                            <input type="text" name="reference_no" x-model="reference" placeholder="Mis. no. approval / ID transaksi"
                                class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white px-4 py-3 text-sm text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                        </div>
                    </div>

                    <div class="mt-auto pt-6">
                        <button type="submit" :disabled="method === 'tunai' && received < total"
                                :class="(method === 'tunai' && received < total) ? 'cursor-not-allowed opacity-50' : 'hover:bg-[#ea580c]'"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#f97316] py-3 font-semibold text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">print</span>
                            Proses &amp; Cetak Nota
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>[x-cloak]{display:none!important}</style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('payment', (opts) => ({
                total: opts.total,
                method: 'tunai',
                received: 0,
                receivedDisplay: '',
                reference: '',
                get change() { return this.received - this.total; },
                onInput(e) {
                    const digits = (e.target.value || '').replace(/[^0-9]/g, '');
                    this.received = digits ? parseInt(digits, 10) : 0;
                    this.receivedDisplay = digits ? new Intl.NumberFormat('id-ID').format(this.received) : '';
                },
                setAmount(v) {
                    this.received = v;
                    this.receivedDisplay = new Intl.NumberFormat('id-ID').format(v);
                },
                formatRp(v) {
                    const n = v < 0 ? 0 : v;
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
                },
            }))
        })
    </script>
</x-app-layout>
