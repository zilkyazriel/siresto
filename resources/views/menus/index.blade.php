<x-app-layout>
    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-100 px-4 py-3 text-sm font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-300">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="font-heading text-3xl font-semibold">Kelola Menu</h2>

        <div class="flex w-full items-center gap-4 sm:w-auto">
            <form method="GET" action="{{ route('menus.index') }}" class="relative w-full sm:w-64">
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari menu..."
                       class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-10 pr-4 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-800">
            </form>

            <a href="{{ route('menus.create') }}"
               class="flex items-center gap-2 whitespace-nowrap rounded-xl bg-orange-500 px-6 py-2 font-semibold text-white shadow-sm transition hover:bg-orange-600">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Tambah Menu
            </a>
        </div>
    </div>

    @php $activeCat = request('category'); @endphp
    <div class="mb-6 flex gap-2 overflow-x-auto pb-2">
        <a href="{{ route('menus.index') }}"
           class="whitespace-nowrap rounded-full px-6 py-2 text-sm font-semibold transition {{ ! $activeCat ? 'bg-orange-500 text-white shadow' : 'bg-white text-slate-600 hover:bg-orange-50 dark:bg-slate-800 dark:text-slate-300' }}">
            Semua
        </a>
        @foreach ($categories as $category)
            <a href="{{ route('menus.index', ['category' => $category->name]) }}"
               class="whitespace-nowrap rounded-full px-6 py-2 text-sm font-semibold transition {{ $activeCat === $category->name ? 'bg-orange-500 text-white shadow' : 'bg-white text-slate-600 hover:bg-orange-50 dark:bg-slate-800 dark:text-slate-300' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($menus as $menu)
            <div class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm transition hover:shadow-lg dark:bg-slate-800">
                <div class="relative h-48 w-full overflow-hidden bg-slate-100 dark:bg-slate-700">
                    @if ($menu->image_path)
                        <img src="{{ asset('storage/' . $menu->image_path) }}" alt="{{ $menu->name }}"
                             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-slate-300 dark:text-slate-500">
                            <span class="material-symbols-outlined text-6xl">restaurant</span>
                        </div>
                    @endif

                    <div class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-slate-700 shadow-sm backdrop-blur-sm">
                        {{ $menu->category->name ?? '-' }}
                    </div>

                    @if ($menu->is_available)
                        <div class="absolute right-3 top-3 flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700 shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-green-600"></span> Tersedia
                        </div>
                    @else
                        <div class="absolute right-3 top-3 flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700 shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-red-600"></span> Habis
                        </div>
                    @endif
                </div>

                <div class="flex flex-1 flex-col p-5">
                    <h3 class="mb-1 truncate font-heading text-lg font-semibold">{{ $menu->name }}</h3>
                    <div class="mt-auto flex items-end justify-between pt-4">
                        <span class="font-bold text-orange-500">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('menus.edit', $menu) }}"
                               class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 shadow-sm transition hover:bg-blue-500 hover:text-white dark:bg-slate-700 dark:text-slate-300">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <form method="POST" action="{{ route('menus.destroy', $menu) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 shadow-sm transition hover:bg-red-500 hover:text-white dark:bg-slate-700 dark:text-slate-300">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <a href="{{ route('menus.create') }}"
               class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center text-slate-400 transition hover:border-orange-400 hover:text-orange-500 dark:border-slate-700">
                <span class="material-symbols-outlined mb-2 text-4xl">add_circle</span>
                Belum ada menu. Klik untuk menambahkan item baru.
            </a>
        @endforelse
    </div>
</x-app-layout>
