<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <div class="mx-auto max-w-[1600px]">
        @if (session('success'))
            <div class="mb-4 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-300">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-[#ba1a1a] dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300">
                <span class="material-symbols-outlined text-[20px]">error</span>
                {{ $errors->first() }}
            </div>
        @endif

        <div x-data="posCart()" class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-start">
            {{-- KIRI: menu --}}
            <div>
                <div class="mb-5">
                    <h2 class="font-['Poppins'] text-[28px] font-bold text-[#0b1c30] dark:text-slate-100">Buat Pesanan</h2>
                    <p class="mt-1 text-[#584237] dark:text-slate-400">Pilih menu, atur jumlah, lalu kirim ke dapur.</p>
                </div>

                <div class="relative mb-4 max-w-xl">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-[#584237]">search</span>
                    <input type="text" x-model="search" placeholder="Cari menu makanan atau minuman..."
                           class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white py-2.5 pl-11 pr-4 text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                </div>

                <div class="mb-6 flex gap-2 overflow-x-auto pb-1">
                    <button type="button" x-on:click="category = 'all'"
                            :class="category === 'all' ? 'bg-[#f97316] text-white shadow' : 'bg-white text-[#584237] border border-[#e0c0b1]/40 hover:bg-[#eff4ff] dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                            class="whitespace-nowrap rounded-full px-4 py-1.5 text-sm font-medium transition-colors">
                        Semua
                    </button>
                    @foreach ($categories as $c)
                        <button type="button" x-on:click="category = '{{ $c->id }}'"
                                :class="category === '{{ $c->id }}' ? 'bg-[#f97316] text-white shadow' : 'bg-white text-[#584237] border border-[#e0c0b1]/40 hover:bg-[#eff4ff] dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                                class="whitespace-nowrap rounded-full px-4 py-1.5 text-sm font-medium transition-colors">
                            {{ $c->name }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
                    <template x-for="menu in filteredMenus" :key="menu.id">
                        <div class="group flex flex-col overflow-hidden rounded-2xl border border-[#e0c0b1]/25 bg-white shadow-[0px_4px_20px_rgba(100,116,139,0.06)] transition-shadow hover:shadow-lg dark:border-slate-700 dark:bg-slate-800"
                             :class="!menu.available && 'opacity-60'">
                            <div class="relative h-36 w-full bg-[#f8f9ff] dark:bg-slate-700">
                                <template x-if="menu.image">
                                    <img :src="menu.image" :alt="menu.name" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!menu.image">
                                    <div class="flex h-full w-full items-center justify-center text-[#e0c0b1]">
                                        <span class="material-symbols-outlined text-4xl">restaurant</span>
                                    </div>
                                </template>
                                <div x-show="!menu.available" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-slate-900/50">
                                    <span class="rounded-full bg-[#ba1a1a] px-3 py-1 text-xs font-semibold text-white">Habis</span>
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <h3 class="font-['Poppins'] font-semibold text-[#0b1c30] dark:text-slate-100" x-text="menu.name"></h3>
                                <p class="mt-1 line-clamp-2 flex-1 text-sm text-[#584237] dark:text-slate-400" x-text="menu.description"></p>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="font-semibold text-[#f97316]" x-text="rupiah(menu.price)"></span>
                                    <button type="button" x-on:click="add(menu)" :disabled="!menu.available"
                                            class="flex h-9 w-9 items-center justify-center rounded-full bg-[#f97316] text-white transition hover:brightness-110 active:scale-95 disabled:cursor-not-allowed disabled:bg-slate-300 dark:disabled:bg-slate-600">
                                        <span class="material-symbols-outlined text-[20px]">add</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="filteredMenus.length === 0" class="py-16 text-center">
                    <span class="material-symbols-outlined mb-2 block text-4xl text-[#e0c0b1]">search_off</span>
                    <p class="text-[#584237] dark:text-slate-400">Menu tidak ditemukan.</p>
                </div>
            </div>

            {{-- KANAN: keranjang --}}
            <aside class="lg:sticky lg:top-6">
                <div class="flex max-h-[calc(100vh-3rem)] flex-col overflow-hidden rounded-3xl border border-[#e0c0b1]/25 bg-white shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:border-slate-700 dark:bg-slate-800">
                    {{-- Header keranjang --}}
                    <div class="border-b border-[#e0c0b1]/25 p-4 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <h2 class="font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Pesanan</h2>
                            <button type="button" x-show="items.length > 0" x-on:click="clear()"
                                    class="rounded-full p-1.5 text-[#584237] transition hover:bg-[#ba1a1a]/10 hover:text-[#ba1a1a]" title="Kosongkan keranjang">
                                <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
                            </button>
                        </div>
                        <div class="mt-2">
                            <select x-model="tableId"
                                    class="w-full appearance-none rounded-lg border border-[#e0c0b1]/40 bg-white bg-none px-3 py-2 text-sm text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                                <option value="">Takeaway / Tanpa Meja</option>
                                @foreach ($tables as $t)
                                    <option value="{{ $t->id }}">Meja {{ $t->number }} &middot; {{ $t->capacity }} org</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-[#584237] dark:text-slate-400"><span x-text="count"></span> item dalam pesanan</p>
                        </div>
                    </div>

                    {{-- Daftar item --}}
                    <div class="flex-1 space-y-2 overflow-y-auto p-4">
                        <template x-for="item in items" :key="item.id">
                            <div class="rounded-xl border border-[#e0c0b1]/25 bg-[#f8f9ff] p-3 dark:border-slate-700 dark:bg-slate-700/40">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="truncate font-medium text-[#0b1c30] dark:text-slate-100" x-text="item.name"></h4>
                                        <span class="text-sm font-semibold text-[#f97316]" x-text="rupiah(item.price * item.qty)"></span>
                                    </div>
                                    <button type="button" x-on:click="remove(item)"
                                            class="rounded-full p-1 text-[#584237] transition hover:bg-[#ba1a1a]/10 hover:text-[#ba1a1a]">
                                        <span class="material-symbols-outlined text-[18px]">close</span>
                                    </button>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <input type="text" x-model="item.note" placeholder="Catatan..."
                                           class="min-w-0 flex-1 rounded-lg border border-transparent bg-white px-2 py-1 text-xs text-[#584237] focus:border-[#e0c0b1] focus:ring-0 dark:bg-slate-800 dark:text-slate-300">
                                    <div class="flex items-center gap-1 rounded-lg bg-white p-1 dark:bg-slate-800">
                                        <button type="button" x-on:click="dec(item)"
                                                class="flex h-6 w-6 items-center justify-center rounded-md text-[#584237] hover:bg-[#eff4ff] dark:text-slate-300 dark:hover:bg-slate-700">
                                            <span class="material-symbols-outlined text-[16px]">remove</span>
                                        </button>
                                        <span class="w-6 text-center text-sm font-semibold text-[#0b1c30] dark:text-slate-100" x-text="item.qty"></span>
                                        <button type="button" x-on:click="inc(item)"
                                                class="flex h-6 w-6 items-center justify-center rounded-md text-[#584237] hover:bg-[#eff4ff] dark:text-slate-300 dark:hover:bg-slate-700">
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="items.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                            <span class="material-symbols-outlined mb-2 text-4xl text-[#e0c0b1]">shopping_cart</span>
                            <p class="text-sm text-[#584237] dark:text-slate-400">Keranjang masih kosong.<br>Pilih menu di sebelah kiri.</p>
                        </div>
                    </div>

                    {{-- Ringkasan + CTA --}}
                    <div class="border-t border-[#e0c0b1]/25 p-4 dark:border-slate-700">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm text-[#584237] dark:text-slate-400">
                                <span>Subtotal</span><span x-text="rupiah(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-sm text-[#584237] dark:text-slate-400">
                                <span x-text="'Pajak (' + (taxRate * 100) + '%')"></span><span x-text="rupiah(tax)"></span>
                            </div>
                            <div class="flex items-center justify-between border-t border-dashed border-[#e0c0b1]/40 pt-2">
                                <span class="font-['Poppins'] font-bold text-[#0b1c30] dark:text-slate-100">Total</span>
                                <span class="font-['Poppins'] text-lg font-bold text-[#f97316]" x-text="rupiah(total)"></span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('orders.store') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="dining_table_id" :value="tableId">
                            <template x-for="item in items" :key="'f-' + item.id">
                                <span>
                                    <input type="hidden" :name="`items[${item.id}][menu_id]`" :value="item.id">
                                    <input type="hidden" :name="`items[${item.id}][quantity]`" :value="item.qty">
                                    <input type="hidden" :name="`items[${item.id}][note]`" :value="item.note">
                                </span>
                            </template>
                            <button type="submit" :disabled="items.length === 0"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#f97316] py-3 font-['Poppins'] font-semibold text-white shadow-md transition hover:bg-[#ea580c] active:scale-[0.98] disabled:cursor-not-allowed disabled:bg-slate-300 dark:disabled:bg-slate-600">
                                <span class="material-symbols-outlined">send</span>
                                Kirim ke Dapur
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        window.__POS__ = { menus: @json($menus), taxRate: {{ $taxRate }} };
        document.addEventListener('alpine:init', () => {
            Alpine.data('posCart', () => ({
                items: [],
                search: '',
                category: 'all',
                tableId: '',
                menus: window.__POS__.menus,
                taxRate: window.__POS__.taxRate,
                get filteredMenus() {
                    const q = this.search.trim().toLowerCase();
                    return this.menus.filter((m) => {
                        const okCat = this.category === 'all' || String(m.category_id) === this.category;
                        const okSearch = q === '' || (m.name || '').toLowerCase().includes(q) || (m.description || '').toLowerCase().includes(q);
                        return okCat && okSearch;
                    });
                },
                add(menu) {
                    if (!menu.available) return;
                    const found = this.items.find((i) => i.id === menu.id);
                    if (found) { found.qty++; }
                    else { this.items.push({ id: menu.id, name: menu.name, price: menu.price, qty: 1, note: '' }); }
                },
                inc(item) { item.qty++; },
                dec(item) { item.qty--; if (item.qty <= 0) this.remove(item); },
                remove(item) { this.items = this.items.filter((i) => i.id !== item.id); },
                clear() { this.items = []; },
                get subtotal() { return this.items.reduce((s, i) => s + i.price * i.qty, 0); },
                get tax() { return Math.round(this.subtotal * this.taxRate); },
                get total() { return this.subtotal + this.tax; },
                get count() { return this.items.reduce((s, i) => s + i.qty, 0); },
                rupiah(v) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(v || 0); },
            }));
        });
    </script>
</x-app-layout>
