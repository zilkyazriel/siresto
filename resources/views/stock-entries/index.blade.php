<x-app-layout>
    <div class="mx-auto w-full max-w-[1440px]">
        <!-- Header -->
        <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="font-heading text-2xl font-bold text-slate-800 dark:text-slate-100">Barang Masuk</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Riwayat penerimaan bahan baku dari supplier.</p>
            </div>
            <a href="{{ route('stock-entries.create') }}"
                class="flex items-center gap-2 rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-orange-600 hover:shadow-md active:scale-95">
                <span class="material-symbols-outlined">add</span>
                Barang Masuk
            </a>
        </div>

        <!-- Flash -->
        @if (session('success'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabel -->
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-900/40 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Kode</th>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold">Supplier</th>
                            <th class="px-6 py-4 font-semibold">Jumlah Bahan</th>
                            <th class="px-6 py-4 font-semibold">Total Biaya</th>
                            <th class="px-6 py-4 font-semibold">Petugas</th>
                            <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($entries as $entry)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-100">{{ $entry->code }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $entry->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-200">{{ $entry->supplier->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $entry->items_count }} bahan</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-200">Rp {{ number_format($entry->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $entry->user->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('stock-entries.show', $entry) }}"
                                        class="inline-flex items-center gap-1 rounded-lg border border-orange-500 px-3 py-1.5 text-sm font-semibold text-orange-600 transition-all hover:bg-orange-500 hover:text-white active:scale-95">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <span class="material-symbols-outlined mb-2 block text-4xl text-slate-300">move_to_inbox</span>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada catatan barang masuk. Klik "Barang Masuk" untuk mencatat penerimaan bahan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($entries->hasPages())
                <div class="border-t border-slate-100 px-6 py-4 dark:border-slate-700">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>