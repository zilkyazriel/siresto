<x-app-layout>
<style>[x-cloak]{display:none!important}</style>
<div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <a href="{{ route('orders.show', $order->id) }}" class="mb-2 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-orange-600">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke Detail
        </a>
        <h1 class="font-['Poppins'] text-2xl font-bold text-[#0b1c30] dark:text-slate-100">Ubah Pesanan #{{ $order->code }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Ubah item atau jumlah selama pesanan masih berstatus "Baru". Stok otomatis menyesuaikan.</p>
    </div>

    @if (session('error'))
    <div class="mb-6 flex items-center gap-2 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <span class="material-symbols-outlined text-[20px]">error</span>{{ session('error') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('orders.update', $order->id) }}" x-data="editOrder()">
        @csrf
        @method('PUT')

        <div class="mb-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Meja</label>
            <select name="dining_table_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700 sm:max-w-xs">
                <option value="">Takeaway</option>
                @foreach ($tables as $t)
                    <option value="{{ $t->id }}" @selected(old('dining_table_id', $order->dining_table_id) == $t->id)>Meja {{ $t->number }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Item Pesanan</h3>
                <button type="button" @click="addRow()" class="flex items-center gap-1 rounded-lg border border-orange-500 px-3 py-1.5 text-sm font-semibold text-orange-600 hover:bg-orange-500 hover:text-white active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">add</span> Tambah Menu
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(row, i) in rows" :key="row.uid">
                    <div class="grid grid-cols-12 items-end gap-2 rounded-xl border border-slate-100 p-3 dark:border-slate-700">
                        <div class="col-span-12 sm:col-span-5">
                            <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Menu</label>
                            <select :name="`items[${i}][menu_id]`" x-model="row.menu_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                                <option value="">Pilih menu...</option>
                                <template x-for="m in menus" :key="m.id">
                                    <option :value="m.id" x-text="menuLabel(m)" :disabled="!m.has_recipe" :selected="String(m.id) === String(row.menu_id)"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-span-4 sm:col-span-2">
                            <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Qty</label>
                            <input type="number" min="1" step="1" :name="`items[${i}][quantity]`" x-model="row.quantity" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <label class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Catatan (opsional)</label>
                            <input type="text" :name="`items[${i}][note]`" x-model="row.note" maxlength="255" placeholder="mis. tanpa sambal" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                        </div>
                        <div class="col-span-2 flex justify-end sm:col-span-1">
                            <button type="button" @click="removeRow(i)" class="rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                        <div class="col-span-12 text-right text-xs text-slate-500 dark:text-slate-400" x-show="row.menu_id">
                            Subtotal: <span class="font-semibold" x-text="rupiah(lineTotal(row))"></span>
                        </div>
                    </div>
                </template>
            </div>

            <p x-show="rows.length === 0" class="rounded-xl bg-slate-50 py-6 text-center text-sm text-slate-400 dark:bg-slate-900/40">Belum ada item. Klik "Tambah Menu".</p>

            <div class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm dark:border-slate-700">
                <div class="flex justify-between text-slate-500 dark:text-slate-400"><span>Subtotal</span><span class="font-semibold text-[#0b1c30] dark:text-slate-100" x-text="rupiah(subtotal())"></span></div>
                <div class="flex justify-between text-slate-500 dark:text-slate-400"><span>Pajak (10%)</span><span class="font-semibold text-[#0b1c30] dark:text-slate-100" x-text="rupiah(tax())"></span></div>
                <div class="flex justify-between border-t border-slate-100 pt-2 text-base dark:border-slate-700"><span class="font-bold text-[#0b1c30] dark:text-slate-100">Total</span><span class="font-bold text-[#f97316]" x-text="rupiah(total())"></span></div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('orders.show', $order->id) }}" class="rounded-xl px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</a>
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 active:scale-95">
                <span class="material-symbols-outlined">save</span> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
function editOrder() {
    return {
        menus: @json($menus),
        taxRate: {{ $taxRate }},
        rows: [],
        init() {
            const old = @json(old('items'));
            const current = @json($currentItems);
            const source = (Array.isArray(old) && old.length) ? old : current;
            source.forEach(r => this.rows.push({
                uid: this._uid(),
                menu_id: r.menu_id ?? '',
                quantity: r.quantity ?? 1,
                note: r.note ?? '',
            }));
            if (this.rows.length === 0) this.addRow();
        },
        _uid() { return Math.random().toString(36).slice(2); },
        addRow() { this.rows.push({ uid: this._uid(), menu_id: '', quantity: 1, note: '' }); },
        removeRow(i) { this.rows.splice(i, 1); },
        menuOf(row) { return this.menus.find(m => String(m.id) === String(row.menu_id)); },
        menuLabel(m) { return m.name + ' — Rp ' + m.price.toLocaleString('id-ID') + (m.has_recipe ? '' : ' (belum ada resep)'); },
        priceOf(row) { const m = this.menuOf(row); return m ? m.price : 0; },
        lineTotal(row) { return this.priceOf(row) * (parseInt(row.quantity) || 0); },
        subtotal() { return this.rows.reduce((s, r) => s + this.lineTotal(r), 0); },
        tax() { return Math.round(this.subtotal() * this.taxRate); },
        total() { return this.subtotal() + this.tax(); },
        rupiah(n) { return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID'); },
    };
}
</script>
</x-app-layout>