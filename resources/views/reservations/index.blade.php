<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <div class="px-4 py-6 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-['Poppins'] text-[28px] font-bold text-[#0b1c30] dark:text-slate-100">Reservasi</h2>
                <p class="mt-0.5 text-sm text-[#584237] dark:text-slate-400">Kelola pemesanan meja pelanggan.</p>
            </div>
            <a href="{{ route('reservations.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#f97316] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#ea580c]">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Buat Reservasi
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Filter status --}}
        @php
            $tabs = [
                ''         => ['label' => 'Semua',    'count' => array_sum($counts)],
                'menunggu' => ['label' => 'Menunggu', 'count' => $counts['menunggu']],
                'hadir'    => ['label' => 'Hadir',    'count' => $counts['hadir']],
                'selesai'  => ['label' => 'Selesai',  'count' => $counts['selesai']],
                'batal'    => ['label' => 'Batal',    'count' => $counts['batal']],
            ];
            $badge = [
                'menunggu' => 'bg-amber-100 text-amber-700',
                'hadir'    => 'bg-blue-100 text-blue-700',
                'selesai'  => 'bg-green-100 text-green-700',
                'batal'    => 'bg-red-100 text-red-700',
            ];
        @endphp
        <div class="mb-5 flex flex-wrap gap-2">
            @foreach ($tabs as $key => $tab)
                <a href="{{ route('reservations.index', $key !== '' ? ['status' => $key] : []) }}"
                   class="inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-sm font-semibold transition-colors
                   {{ (string) $activeStatus === (string) $key ? 'border-[#f97316] bg-[#f97316] text-white' : 'border-[#e0c0b1]/40 text-[#584237] hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300' }}">
                    {{ $tab['label'] }}
                    <span class="rounded-full px-1.5 text-xs {{ (string) $activeStatus === (string) $key ? 'bg-white/25' : 'bg-[#eff4ff] text-[#006398] dark:bg-slate-700 dark:text-slate-200' }}">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Daftar --}}
        @forelse ($reservations as $reservation)
            <a href="{{ route('reservations.show', $reservation) }}"
               class="mb-3 flex flex-col gap-3 rounded-2xl border border-[#e0c0b1]/30 bg-white p-4 shadow-sm transition-colors hover:border-[#f97316]/50 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#eff4ff] text-[#006398] dark:bg-slate-700 dark:text-slate-200">
                        <span class="material-symbols-outlined">event_seat</span>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-x-2">
                            <span class="font-['Poppins'] font-bold text-[#0b1c30] dark:text-slate-100">{{ $reservation->customer_name }}</span>
                            <span class="text-xs text-[#584237] dark:text-slate-400">{{ $reservation->code }}</span>
                        </div>
                        <p class="mt-0.5 text-sm text-[#584237] dark:text-slate-400">
                            {{ $reservation->reserved_at->format('d/m/Y H:i') }}
                            · {{ $reservation->party_size }} orang
                            · {{ $reservation->diningTable ? 'Meja '.$reservation->diningTable->number : 'Meja belum ditentukan' }}
                        </p>
                    </div>
                </div>
                <span class="w-max rounded-full px-3 py-1 text-xs font-semibold {{ $badge[$reservation->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($reservation->status) }}</span>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-[#e0c0b1]/50 bg-white p-10 text-center dark:border-slate-700 dark:bg-slate-800">
                <span class="material-symbols-outlined text-4xl text-[#e0c0b1]">event_busy</span>
                <p class="mt-2 text-sm text-[#584237] dark:text-slate-400">Belum ada reservasi.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $reservations->links() }}
        </div>
    </div>
</x-app-layout>