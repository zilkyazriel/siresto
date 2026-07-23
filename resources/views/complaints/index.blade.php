<x-app-layout>
<div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-['Poppins'] text-2xl font-bold text-[#0b1c30] dark:text-slate-100">Keluhan Pelanggan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Catat dan tindak lanjuti keluhan pelanggan.</p>
        </div>
        <a href="{{ route('complaints.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 active:scale-95">
            <span class="material-symbols-outlined text-[20px]">add</span> Catat Keluhan
        </a>
    </div>

    @if (session('success'))
    <div class="mb-6 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        <span class="material-symbols-outlined text-[20px]">check_circle</span>{{ session('success') }}
    </div>
    @endif

    @php
        $tabs = [
            '' => ['Semua', array_sum($counts)],
            'baru' => ['Baru', $counts['baru']],
            'diproses' => ['Diproses', $counts['diproses']],
            'selesai' => ['Selesai', $counts['selesai']],
        ];
        $badge = [
            'baru' => ['Baru', 'bg-amber-100 text-amber-700'],
            'diproses' => ['Diproses', 'bg-blue-100 text-blue-700'],
            'selesai' => ['Selesai', 'bg-green-100 text-green-700'],
        ];
    @endphp

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach ($tabs as $key => [$label, $count])
            <a href="{{ route('complaints.index', $key ? ['status' => $key] : []) }}"
               class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold transition-colors {{ (string) $activeStatus === (string) $key ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300' }}">
                {{ $label }} <span class="rounded-full bg-black/10 px-2 text-xs">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        @if ($complaints->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-900/40 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Keluhan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Dicatat</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach ($complaints as $c)
                    @php [$stLabel, $stClass] = $badge[$c->status] ?? [$c->status, 'bg-slate-100 text-slate-600']; @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="whitespace-nowrap px-4 py-3 font-semibold text-[#0b1c30] dark:text-slate-100">{{ $c->code }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $c->customer_name ?: '—' }}</td>
                        <td class="max-w-xs px-4 py-3 text-slate-600 dark:text-slate-300"><span class="line-clamp-2">{{ $c->content }}</span></td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $stClass }}">{{ $stLabel }}</span></td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-500 dark:text-slate-400">
                            {{ $c->created_at->format('d M Y') }}<br>
                            <span class="text-xs">{{ $c->user->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('complaints.show', $c->id) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700">
                                Detail <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-4 py-3 dark:border-slate-700">{{ $complaints->links() }}</div>
        @else
        <div class="flex flex-col items-center gap-2 py-16 text-center">
            <span class="material-symbols-outlined text-4xl text-slate-300">sentiment_satisfied</span>
            <p class="text-sm text-slate-400">Belum ada keluhan.</p>
        </div>
        @endif
    </div>
</div>
</x-app-layout>