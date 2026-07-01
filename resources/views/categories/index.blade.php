<x-app-layout>
    <div class="mx-auto w-full max-w-4xl">
        @if (session('success'))
            <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl font-bold text-slate-800 dark:text-slate-100">Kelola Kategori</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelompokkan menu restoran ke dalam kategori.</p>
            </div>
            <button type="button" onclick="showAddRow()"
                    class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600 active:scale-95">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Tambah Kategori
            </button>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
                <ul class="list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 dark:bg-slate-800 dark:ring-slate-700">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-400">
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Jumlah Menu</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-700 dark:text-slate-200">
                    <tr id="addRow" class="hidden border-b border-slate-100 bg-orange-50/40 dark:border-slate-700 dark:bg-slate-700/30">
                        <td class="px-6 py-3" colspan="2">
                            <form id="addForm" method="POST" action="{{ route('categories.store') }}" class="flex items-center gap-2">
                                @csrf
                                <input type="text" name="name" value="{{ old('name') }}" autocomplete="off"
                                       placeholder="Masukkan nama kategori..."
                                       class="w-full max-w-sm rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                            </form>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex justify-end gap-2">
                                <button type="submit" form="addForm" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600">Simpan</button>
                                <button type="button" onclick="hideAddRow()" class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-300 dark:bg-slate-600 dark:text-slate-100">Batal</button>
                            </div>
                        </td>
                    </tr>

                    @forelse ($categories as $category)
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/40">
                            <td class="px-6 py-4">
                                <span id="nameDisplay{{ $category->id }}" class="font-semibold text-slate-800 dark:text-slate-100">{{ $category->name }}</span>
                                <form id="editForm{{ $category->id }}" method="POST" action="{{ route('categories.update', $category) }}" class="hidden">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $category->name }}" autocomplete="off"
                                           class="w-full max-w-sm rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                                </form>
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $category->menus_count }} Menu</td>
                            <td class="px-6 py-4">
                                <div id="actions{{ $category->id }}" class="flex justify-end gap-1">
                                    <button type="button" onclick="startEdit({{ $category->id }})" title="Edit"
                                            class="rounded-lg p-2 text-orange-600 transition hover:bg-orange-100 dark:hover:bg-orange-900/30">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="rounded-lg p-2 text-red-600 transition hover:bg-red-100 dark:hover:bg-red-900/30">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                                <div id="editActions{{ $category->id }}" class="hidden justify-end gap-2">
                                    <button type="submit" form="editForm{{ $category->id }}" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600">Simpan</button>
                                    <button type="button" onclick="cancelEdit({{ $category->id }})" class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-300 dark:bg-slate-600 dark:text-slate-100">Batal</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-700">
                                    <span class="material-symbols-outlined">category</span>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada kategori. Klik <span class="font-semibold text-orange-500">Tambah Kategori</span> untuk membuat yang pertama.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function showAddRow() {
            const row = document.getElementById('addRow');
            row.classList.remove('hidden');
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) emptyRow.classList.add('hidden');
            const input = row.querySelector('input[name="name"]');
            if (input) input.focus();
        }
        function hideAddRow() {
            document.getElementById('addRow').classList.add('hidden');
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) emptyRow.classList.remove('hidden');
        }
        function startEdit(id) {
            document.getElementById('nameDisplay' + id).classList.add('hidden');
            document.getElementById('editForm' + id).classList.remove('hidden');
            document.getElementById('actions' + id).classList.add('hidden');
            const ea = document.getElementById('editActions' + id);
            ea.classList.remove('hidden');
            ea.classList.add('flex');
            const input = document.getElementById('editForm' + id).querySelector('input[name="name"]');
            if (input) input.focus();
        }
        function cancelEdit(id) {
            document.getElementById('nameDisplay' + id).classList.remove('hidden');
            document.getElementById('editForm' + id).classList.add('hidden');
            document.getElementById('actions' + id).classList.remove('hidden');
            const ea = document.getElementById('editActions' + id);
            ea.classList.add('hidden');
            ea.classList.remove('flex');
        }
    </script>
</x-app-layout>
