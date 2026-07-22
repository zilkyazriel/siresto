<x-app-layout>
    <div class="mx-auto w-full max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('stock-entries.index') }}" class="mb-2 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-orange-600 dark:text-slate-400">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke Barang Masuk
            </a>
            <h1 class="font-heading text-2xl font-bold text-slate-800 dark:text-slate-100">Detail Barang Masuk</h1>
        </div>

        @if (session('success'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <p class="text-xs text-slate-400">Kode</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $stockEntry->code }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Tanggal</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $stockEntry->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Supplier</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $stockEntry->supplier->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Petugas</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $stockEntry->user->name ?? '—' }}</p>
                </div>
            </div>

            @if ($stockEntry->note)
                <div class="mb-6 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:bg-slate-900/40 dark:text-slate-300">
                    <span class="font-semibold">Catatan:</span> {{ $stockEntry->note }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-900/40 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Bahan</th>
                            <th class="px-4 py-3 text-right font-semibold">Jumlah</th>
                            <th class="px-4 py-3 text-right font-semibold">Harga</th>
                            <th class="px-4 py-3 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach ($stockEntry->items as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100">{{ $item->stock->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-300">{{ $item->quantity }} {{ $item->stock->unit ?? '' }}</td>
                                <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-300">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-300">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-200 dark:border-slate-600">
                            <td colspan="3" class="px-4 py-3 text-right font-semibold text-slate-700 dark:text-slate-200">Total Biaya</td>
                            <td class="px-4 py-3 text-right font-heading text-lg font-bold text-orange-600">Rp {{ number_format($stockEntry->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>