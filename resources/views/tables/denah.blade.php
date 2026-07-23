<x-app-layout>
<div class="mx-auto w-full max-w-[1440px]">
    <!-- Header -->
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800 dark:text-slate-100">Denah Meja</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Status meja diperbarui otomatis mengikuti pesanan & pembayaran.</p>
        </div>
        <a href="{{ route('tables.index') }}"
           class="flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition-all hover:border-orange-300 hover:text-orange-600 hover:shadow-md active:scale-95 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
            <span class="material-symbols-outlined">settings</span>
            Kelola Meja
        </a>
    </div>

    <!-- Flash -->
    @if (session('success'))
    <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
        <span class="material-symbols-outlined text-[20px]">check_circle</span>{{ session('success') }}
    </div>
    @endif
    @if (session('info'))
    <div class="mb-6 flex items-center gap-2 rounded-xl bg-blue-50 px-4 py-3 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
        <span class="material-symbols-outlined text-[20px]">info</span>{{ session('info') }}
    </div>
    @endif
    @if (session('error'))
    <div class="mb-6 flex items-center gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">
        <span class="material-symbols-outlined text-[20px]">error</span>{{ session('error') }}
    </div>
    @endif

    <!-- Ringkasan -->
    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tersedia</p>
            <p class="mt-1 text-2xl font-bold text-green-600">{{ $counts['tersedia'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Terisi</p>
            <p class="mt-1 text-2xl font-bold text-orange-600">{{ $counts['terisi'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Perlu Dibersihkan</p>
            <p class="mt-1 text-2xl font-bold text-amber-500">{{ $counts['kotor'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Reserved</p>
            <p class="mt-1 text-2xl font-bold text-blue-600">{{ $counts['reserved'] }}</p>
        </div>
    </div>

    <!-- Grid Meja -->
    @php
        $meta = [
            'tersedia' => ['ring' => 'border-green-200', 'dot' => 'bg-green-500', 'badge' => 'bg-green-100 text-green-700', 'icon' => 'check_circle'],
            'terisi'   => ['ring' => 'border-orange-200', 'dot' => 'bg-orange-500', 'badge' => 'bg-orange-100 text-orange-700', 'icon' => 'restaurant'],
            'kotor'    => ['ring' => 'border-amber-200', 'dot' => 'bg-amber-500', 'badge' => 'bg-amber-100 text-amber-700', 'icon' => 'cleaning_services'],
            'reserved' => ['ring' => 'border-blue-200', 'dot' => 'bg-blue-500', 'badge' => 'bg-blue-100 text-blue-700', 'icon' => 'event_seat'],
        ];
    @endphp
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($cards as $c)
        @php $m = $meta[$c['status']] ?? $meta['tersedia']; @endphp
        <div class="flex flex-col rounded-2xl border-2 {{ $m['ring'] }} bg-white p-5 shadow-sm dark:bg-slate-800">
            <div class="mb-3 flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full {{ $m['dot'] }}"></span>
                    <h3 class="font-heading text-xl font-bold text-slate-800 dark:text-slate-100">{{ $c['number'] }}</h3>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $m['badge'] }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $m['icon'] }}</span>{{ $c['status_label'] }}
                </span>
            </div>

            <p class="mb-3 flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-[16px]">group</span>Kapasitas: {{ $c['capacity'] }} Orang
            </p>

            @if ($c['order_code'] && $c['status'] !== 'tersedia')
            <div class="mb-3 rounded-xl bg-slate-50 p-3 text-sm dark:bg-slate-700/40">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $c['order_code'] }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $c['item_count'] }} item</span>
                </div>
                <div class="mt-1 font-bold text-orange-600">{{ $c['total_display'] }}</div>
                <a href="{{ route('orders.show', $c['order_id']) }}" class="mt-1 inline-flex items-center gap-1 text-xs font-medium text-slate-400 hover:text-orange-500">
                    Lihat pesanan <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                </a>
            </div>
            @endif

            <div class="mt-auto">
                @if ($c['status'] === 'kotor')
                <form method="POST" action="{{ route('tables.markClean', $c['id']) }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-orange-600 active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">cleaning_services</span>
                        Tandai Bersih
                    </button>
                </form>
                @elseif ($c['status'] === 'terisi')
                <p class="text-center text-xs text-slate-400">Meja sedang digunakan</p>
                @elseif ($c['status'] === 'reserved')
                <p class="text-center text-xs text-slate-400">Meja direservasi</p>
                @else
                <p class="text-center text-xs font-medium text-green-500">Siap dipakai</p>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center dark:border-slate-700 dark:bg-slate-800">
            <span class="material-symbols-outlined mb-2 block text-4xl text-slate-300">table_restaurant</span>
            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada meja. Tambahkan lewat menu "Kelola Meja".</p>
        </div>
        @endforelse
    </div>
</div>
</x-app-layout>