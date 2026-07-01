<x-app-layout>
    <div class="mx-auto w-full max-w-[1440px]">

        <!-- Header -->
        <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="font-heading text-2xl font-bold text-slate-800 dark:text-slate-100">Kelola Meja</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Pantau dan kelola ketersediaan meja restoran.</p>
            </div>
            <button type="button" onclick="openCreate()"
                class="flex items-center gap-2 rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-orange-600 hover:shadow-md active:scale-95">
                <span class="material-symbols-outlined">add</span>
                Tambah Meja
            </button>
        </div>

        <!-- Flash -->
        @if (session('success'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">
                <span class="material-symbols-outlined text-[20px]">error</span>
                {{ session('error') }}
            </div>
        @endif

        <!-- Grid Kartu Meja -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($tables as $table)
                @php
                    $badge = match ($table->status) {
                        'tersedia' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                        'terisi'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
                        'reserved' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                        default    => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                    };
                @endphp
                <div class="group relative flex flex-col rounded-2xl border border-transparent bg-white p-5 shadow-sm transition-shadow duration-300 hover:border-slate-200 hover:shadow-md dark:bg-slate-800 dark:hover:border-slate-700">
                    <div class="mb-4 flex items-start justify-between">
                        <div class="rounded-lg bg-orange-50 p-2 text-orange-500 dark:bg-slate-700">
                            <span class="material-symbols-outlined">table_restaurant</span>
                        </div>
                        <div class="flex gap-1">
                            <button type="button"
                                data-id="{{ $table->id }}"
                                data-number="{{ $table->number }}"
                                data-capacity="{{ $table->capacity }}"
                                data-status="{{ $table->status }}"
                                onclick="openEdit(this)"
                                class="rounded p-1 text-slate-400 transition-colors hover:bg-slate-100 hover:text-blue-600 dark:hover:bg-slate-700">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            @if ($table->status === 'tersedia')
                                <form method="POST" action="{{ route('tables.destroy', $table) }}"
                                    onsubmit="return confirm('Hapus meja {{ $table->number }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded p-1 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-slate-700">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <h3 class="mb-1 font-heading text-xl font-bold text-slate-800 dark:text-slate-100">{{ $table->number }}</h3>
                    <p class="mb-4 flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400">
                        <span class="material-symbols-outlined text-[16px]">group</span>
                        Kapasitas: {{ $table->capacity }} Orang
                    </p>
                    <div class="mt-auto">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">
                            {{ ucfirst($table->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center dark:border-slate-700 dark:bg-slate-800">
                    <span class="material-symbols-outlined mb-2 block text-4xl text-slate-300">table_restaurant</span>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada meja. Klik "Tambah Meja" untuk menambahkan.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Tambah/Edit Meja -->
    <div id="tableModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-800">
            <div class="mb-5 flex items-center justify-between">
                <h2 id="modalTitle" class="font-heading text-xl font-bold text-slate-800 dark:text-slate-100">Tambah Meja</h2>
                <button type="button" onclick="closeModal()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-300">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="tableForm" method="POST" action="{{ route('tables.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="table_id" id="fTableId" value="">

                <div>
                    <label for="fNumber" class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Nomor Meja</label>
                    <input type="text" name="number" id="fNumber" placeholder="Contoh: M-01"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                </div>

                <div>
                    <label for="fCapacity" class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Kapasitas (orang)</label>
                    <input type="number" name="capacity" id="fCapacity" min="1" max="100" placeholder="Contoh: 4"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                </div>

                <div>
                    <label for="fStatus" class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                    <select name="status" id="fStatus"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                        <option value="tersedia">Tersedia</option>
                        <option value="terisi">Terisi</option>
                        <option value="reserved">Reserved</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal()"
                        class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 active:scale-95">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const tableModal = document.getElementById('tableModal');
        const tableForm = document.getElementById('tableForm');
        const storeUrl = "{{ route('tables.store') }}";
        const updateBase = "{{ url('/meja') }}";

        function showModal() {
            tableModal.classList.remove('hidden');
            tableModal.classList.add('flex');
        }
        function closeModal() {
            tableModal.classList.add('hidden');
            tableModal.classList.remove('flex');
        }
        function openCreate() {
            document.getElementById('modalTitle').textContent = 'Tambah Meja';
            tableForm.action = storeUrl;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('fTableId').value = '';
            document.getElementById('fNumber').value = '';
            document.getElementById('fCapacity').value = '';
            document.getElementById('fStatus').value = 'tersedia';
            showModal();
        }
        function openEdit(btn) {
            document.getElementById('modalTitle').textContent = 'Edit Meja';
            tableForm.action = updateBase + '/' + btn.dataset.id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('fTableId').value = btn.dataset.id;
            document.getElementById('fNumber').value = btn.dataset.number;
            document.getElementById('fCapacity').value = btn.dataset.capacity;
            document.getElementById('fStatus').value = btn.dataset.status;
            showModal();
        }
        tableModal.addEventListener('click', function (e) {
            if (e.target === tableModal) closeModal();
        });

        @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            var oldId = @json(old('table_id'));
            if (oldId) {
                document.getElementById('modalTitle').textContent = 'Edit Meja';
                tableForm.action = updateBase + '/' + oldId;
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('fTableId').value = oldId;
                showModal();
            } else {
                openCreate();
            }
            document.getElementById('fNumber').value = @json(old('number') ?? '');
            document.getElementById('fCapacity').value = @json(old('capacity') ?? '');
            document.getElementById('fStatus').value = @json(old('status') ?? 'tersedia');
        });
        @endif
    </script>
</x-app-layout>
