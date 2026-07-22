<x-app-layout>
    <div class="mx-auto w-full max-w-4xl">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('stock-entries.index') }}" class="mb-2 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-orange-600 dark:text-slate-400">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
            </a>
            <h1 class="font-heading text-2xl font-bold text-slate-800 dark:text-slate-100">Catat Barang Masuk</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Catat penerimaan bahan dari supplier. Stok akan otomatis bertambah setelah disimpan.</p>
        </div>

        <!-- Flash / errors -->
        @if (session('error'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">
                <span class="material-symbols-outlined text-[20px]">error</span>
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-300">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('stock-entries.store') }}" x-data="barangMasuk()">
            @csrf

            <!-- Info umum -->
            <div class="mb-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Supplier (opsional)</label>
                        <select name="supplier_id"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                            <option value="">— Tanpa supplier —</option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Catatan (opsional)</label>
                        <input type="text" name="note" value="{{ old('note') }}" placeholder="mis. No. nota / keterangan"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                    </div>
                </div>
            </div>

            <!-- Daftar bahan -->
            <div class="mb-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-heading text-lg font-bold text-slate-800 dark:text-slate-100">Bahan yang Masuk</h3>
                    <button type="button" @click="addRow()"
                        class="flex items-center gap-1 rounded-lg border border-orange-500 px-3 py-1.5 text-sm font-semibold text-orange-600 transition-all hover:bg-orange-500 hover:text-white active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">add</span> Tambah Bahan
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(row, i) in rows" :key="row.uid">
                        <div class="grid grid-cols-12 items-end gap-2 rounded-xl border border-slate-100 p-3 dark:border-slate-700">
                            <div class="col-span-12 sm:col-span-5">
                                <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Bahan</label>
                                <select :name="`items[${i}][stock_id]`" x-model="row.stock_id"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                                    <option value="">Pilih bahan...</option>
                                    <template x-for="s in stocks" :key="s.id">
                                        <option :value="s.id" x-text="s.name" :selected="String(s.id) === String(row.stock_id)"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-span-5 sm:col-span-3">
                                <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Jumlah</label>
                                <div class="relative">
                                    <input type="number" step="0.01" min="0" :name="`items[${i}][quantity]`" x-model="row.quantity" placeholder="0"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 pr-12 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" x-text="unitOf(row)"></span>
                                </div>
                            </div>
                            <div class="col-span-5 sm:col-span-3">
                                <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Harga/satuan (opsional)</label>
                                <input type="number" step="0.01" min="0" :name="`items[${i}][price]`" x-model="row.price" placeholder="0"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                            </div>
                            <div class="col-span-2 flex justify-end sm:col-span-1">
                                <button type="button" @click="removeRow(i)"
                                    class="rounded-lg p-2 text-red-500 transition-colors hover:bg-red-50 dark:hover:bg-red-900/30">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <p x-show="rows.length === 0" class="rounded-xl bg-slate-50 py-6 text-center text-sm text-slate-400 dark:bg-slate-900/40">
                    Belum ada bahan. Klik "Tambah Bahan".
                </p>

                <!-- Total -->
                <div class="mt-4 flex items-center justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-700">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Perkiraan total biaya:</span>
                    <span class="font-heading text-xl font-bold text-slate-800 dark:text-slate-100" x-text="rupiah(total())"></span>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('stock-entries.index') }}" class="rounded-xl px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</a>
                <button type="submit" class="flex items-center gap-2 rounded-xl bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-orange-600 active:scale-95">
                    <span class="material-symbols-outlined">save</span> Simpan & Tambah Stok
                </button>
            </div>
        </form>
    </div>

    <script>
        function barangMasuk() {
            return {
                stocks: @json($stocks),
                rows: [],
                init() {
                    const old = @json(old('items', []));
                    if (Array.isArray(old) && old.length) {
                        old.forEach(r => this.rows.push({
                            uid: this._uid(),
                            stock_id: r.stock_id ?? '',
                            quantity: r.quantity ?? '',
                            price: r.price ?? '',
                        }));
                    } else {
                        this.addRow();
                    }
                },
                _uid() { return Math.random().toString(36).slice(2); },
                addRow() { this.rows.push({ uid: this._uid(), stock_id: '', quantity: '', price: '' }); },
                removeRow(i) { this.rows.splice(i, 1); },
                unitOf(row) {
                    const s = this.stocks.find(s => String(s.id) === String(row.stock_id));
                    return s ? s.unit : '';
                },
                total() {
                    return this.rows.reduce((sum, r) => sum + (parseFloat(r.price || 0) * parseFloat(r.quantity || 0)), 0);
                },
                rupiah(n) { return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID'); },
            };
        }
    </script>
</x-app-layout>