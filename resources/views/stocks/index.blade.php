<x-app-layout>
    <div class="mx-auto w-full max-w-[1440px]">
        <!-- Header -->
        <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="font-heading text-2xl font-bold text-slate-800 dark:text-slate-100">Stok Bahan Baku</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Pantau dan kelola ketersediaan bahan dapur secara real-time.</p>
            </div>
            <button type="button" onclick="openCreate()"
                class="flex items-center gap-2 rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-orange-600 hover:shadow-md active:scale-95">
                <span class="material-symbols-outlined">add</span>
                Tambah Bahan
            </button>
        </div>

        <!-- Flash -->
        @if (session('success'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
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

        <!-- Summary Cards -->
        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="flex items-center gap-5 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-50 text-orange-500 dark:bg-slate-700">
                    <span class="material-symbols-outlined text-3xl">inventory_2</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total Bahan</p>
                    <h4 class="font-heading text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $total }} <span class="text-sm font-normal text-slate-400">Items</span></h4>
                </div>
            </div>
            <div class="flex items-center gap-5 rounded-2xl border border-slate-100 border-l-4 border-l-amber-400 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 dark:bg-slate-700">
                    <span class="material-symbols-outlined text-3xl">warning</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Stok Menipis</p>
                    <h4 class="font-heading text-2xl font-bold text-amber-500">{{ $low }} <span class="text-sm font-normal text-slate-400">Items</span></h4>
                </div>
            </div>
            <div class="flex items-center gap-5 rounded-2xl border border-slate-100 border-l-4 border-l-red-500 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-500 dark:bg-slate-700">
                    <span class="material-symbols-outlined text-3xl">error</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Stok Habis</p>
                    <h4 class="font-heading text-2xl font-bold text-red-500">{{ $out }} <span class="text-sm font-normal text-slate-400">Items</span></h4>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex flex-col justify-between gap-4 border-b border-slate-100 px-6 py-4 dark:border-slate-700 md:flex-row md:items-center">
                <h3 class="font-heading text-lg font-bold text-slate-800 dark:text-slate-100">Daftar Inventaris</h3>
                <form method="GET" action="{{ route('stocks.index') }}" class="relative w-full md:max-w-xs">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari bahan baku..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-4 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-900/40 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama Bahan</th>
                            <th class="px-6 py-4 font-semibold">Stok Saat Ini</th>
                            <th class="px-6 py-4 font-semibold">Satuan</th>
                            <th class="px-6 py-4 font-semibold">Stok Minimum</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($stocks as $stock)
                            @php
                                $st = $stock->status;
                                $badge = match ($st) {
                                    'aman' => 'bg-green-100 text-green-700',
                                    'menipis' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-red-100 text-red-700',
                                };
                                $dot = match ($st) {
                                    'aman' => 'bg-green-500',
                                    'menipis' => 'bg-amber-500',
                                    default => 'bg-red-500',
                                };
                                $label = match ($st) {
                                    'aman' => 'Aman',
                                    'menipis' => 'Menipis',
                                    default => 'Habis',
                                };
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl bg-slate-100 text-slate-400 dark:bg-slate-700">
                                            @if ($stock->image_path)
                                                <img src="{{ asset('storage/' . $stock->image_path) }}" alt="{{ $stock->name }}" class="h-full w-full object-cover">
                                            @else
                                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                            @endif
                                        </div>
                                        <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $stock->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 {{ $st === 'habis' ? 'font-bold text-red-600' : 'text-slate-700 dark:text-slate-200' }}">{{ $stock->quantity }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $stock->unit }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $stock->min_quantity }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button"
                                        data-id="{{ $stock->id }}"
                                        data-name="{{ $stock->name }}"
                                        data-quantity="{{ $stock->quantity }}"
                                        data-unit="{{ $stock->unit }}"
                                        data-min="{{ $stock->min_quantity }}"
                                        onclick="openEdit(this)"
                                        class="{{ $st === 'habis' ? 'bg-orange-500 text-white hover:bg-orange-600' : 'border border-orange-500 text-orange-600 hover:bg-orange-500 hover:text-white' }} rounded-lg px-3 py-1.5 text-sm font-semibold transition-all active:scale-95">
                                        {{ $st === 'habis' ? 'Belanja Stok' : 'Sesuaikan Stok' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <span class="material-symbols-outlined mb-2 block text-4xl text-slate-300">inventory_2</span>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $q !== '' ? 'Tidak ada bahan yang cocok dengan pencarian.' : 'Belum ada bahan baku. Klik Tambah Bahan untuk menambahkan.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Bahan -->
    <div id="stockModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-800">
            <div class="mb-5 flex items-center justify-between">
                <h2 id="modalTitle" class="font-heading text-xl font-bold text-slate-800 dark:text-slate-100">Tambah Bahan</h2>
                <button type="button" onclick="closeModal()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="stockForm" method="POST" action="{{ route('stocks.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="stock_id" id="fStockId" value="">
                <div>
                    <label for="fName" class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Nama Bahan</label>
                    <input type="text" name="name" id="fName" placeholder="Contoh: Daging Sapi"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="fQuantity" class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Stok Saat Ini</label>
                        <input type="number" step="0.01" min="0" name="quantity" id="fQuantity" placeholder="0"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                    </div>
                    <div>
                        <label for="fUnit" class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Satuan</label>
                        <select name="unit" id="fUnit"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                            <option value="kg">kg</option>
                            <option value="gram">gram</option>
                            <option value="liter">liter</option>
                            <option value="ml">ml</option>
                            <option value="pcs">pcs</option>
                            <option value="ikat">ikat</option>
                            <option value="botol">botol</option>
                            <option value="pack">pack</option>
                            <option value="sachet">sachet</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="fMin" class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Stok Minimum</label>
                    <input type="number" step="0.01" min="0" name="min_quantity" id="fMin" placeholder="0"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                    <p class="mt-1 text-xs text-slate-400">Status Menipis muncul saat stok kurang dari / sama dengan angka ini.</p>
                </div>
                <div>
                    <label for="fImage" class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Foto (opsional)</label>
                    <input type="file" name="image" id="fImage" accept="image/*"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-500 outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-orange-50 file:px-3 file:py-1 file:text-sm file:font-semibold file:text-orange-600 dark:border-slate-600 dark:bg-slate-700">
                    <p id="imgHint" class="mt-1 hidden text-xs text-slate-400">Biarkan kosong jika tidak ingin mengganti foto.</p>
                </div>
                <div class="flex items-center justify-between gap-2 pt-2">
                    <button type="button" id="btnDelete" onclick="submitDelete()"
                        class="hidden rounded-xl px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
                        Hapus
                    </button>
                    <div class="ml-auto flex gap-2">
                        <button type="button" onclick="closeModal()"
                            class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 active:scale-95">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <form id="deleteForm" method="POST" action="" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        const stockModal = document.getElementById('stockModal');
        const stockForm = document.getElementById('stockForm');
        const deleteForm = document.getElementById('deleteForm');
        const storeUrl = "{{ route('stocks.store') }}";
        const updateBase = "{{ url('/stok') }}";
        function showModal() {
            stockModal.classList.remove('hidden');
            stockModal.classList.add('flex');
        }
        function closeModal() {
            stockModal.classList.add('hidden');
            stockModal.classList.remove('flex');
        }
        function openCreate() {
            document.getElementById('modalTitle').textContent = 'Tambah Bahan';
            stockForm.action = storeUrl;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('fStockId').value = '';
            document.getElementById('fName').value = '';
            document.getElementById('fQuantity').value = '';
            document.getElementById('fUnit').value = 'kg';
            document.getElementById('fMin').value = '';
            document.getElementById('fImage').value = '';
            document.getElementById('imgHint').classList.add('hidden');
            document.getElementById('btnDelete').classList.add('hidden');
            showModal();
        }
        function openEdit(btn) {
            document.getElementById('modalTitle').textContent = 'Sesuaikan Stok';
            stockForm.action = updateBase + '/' + btn.dataset.id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('fStockId').value = btn.dataset.id;
            document.getElementById('fName').value = btn.dataset.name;
            document.getElementById('fQuantity').value = btn.dataset.quantity;
            document.getElementById('fUnit').value = btn.dataset.unit;
            document.getElementById('fMin').value = btn.dataset.min;
            document.getElementById('fImage').value = '';
            document.getElementById('imgHint').classList.remove('hidden');
            document.getElementById('btnDelete').classList.remove('hidden');
            deleteForm.action = updateBase + '/' + btn.dataset.id;
            showModal();
        }
        function submitDelete() {
            if (confirm('Hapus bahan ini?')) {
                deleteForm.submit();
            }
        }
        stockModal.addEventListener('click', function (e) {
            if (e.target === stockModal) closeModal();
        });
        @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            var oldId = @json(old('stock_id'));
            if (oldId) {
                openEditFromOld(oldId);
            } else {
                openCreate();
            }
            document.getElementById('fName').value = @json(old('name') ?? '');
            document.getElementById('fQuantity').value = @json(old('quantity') ?? '');
            document.getElementById('fUnit').value = @json(old('unit') ?? 'kg');
            document.getElementById('fMin').value = @json(old('min_quantity') ?? '');
        });
        function openEditFromOld(id) {
            document.getElementById('modalTitle').textContent = 'Sesuaikan Stok';
            stockForm.action = updateBase + '/' + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('fStockId').value = id;
            document.getElementById('imgHint').classList.remove('hidden');
            document.getElementById('btnDelete').classList.remove('hidden');
            deleteForm.action = updateBase + '/' + id;
            showModal();
        }
        @endif
    </script>
</x-app-layout>
