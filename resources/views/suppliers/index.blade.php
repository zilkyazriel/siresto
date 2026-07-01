<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <div class="mx-auto max-w-[1440px]">
        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-['Poppins'] text-[32px] font-bold leading-tight text-[#0b1c30] dark:text-slate-100">Kelola Supplier</h2>
                <p class="mt-1 text-[#584237] dark:text-slate-400">Kelola daftar pemasok bahan baku dan inventaris restoran Anda.</p>
            </div>
            <button type="button" onclick="openCreate()"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#f97316] px-6 py-3 font-semibold text-white shadow-[0px_4px_20px_rgba(249,115,22,0.25)] transition-all hover:-translate-y-0.5 hover:bg-[#ea6a0c] active:translate-y-0 sm:w-auto">
                <span class="material-symbols-outlined">add</span>
                Tambah Supplier
            </button>
        </div>

        {{-- Flash success --}}
        @if (session('success'))
            <div class="mb-4 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-900 dark:bg-green-900/30 dark:text-green-300">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-900/30 dark:text-red-300">
                <p class="mb-1 flex items-center gap-2 font-semibold"><span class="material-symbols-outlined text-[20px]">error</span> Periksa kembali input Anda:</p>
                <ul class="list-inside list-disc space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Table card --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:bg-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-[#e0c0b1] bg-[#eff4ff] dark:border-slate-700 dark:bg-slate-900/40">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#584237] dark:text-slate-400">Nama Supplier</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#584237] dark:text-slate-400">Kontak (PIC)</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#584237] dark:text-slate-400">No. Telepon</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#584237] dark:text-slate-400">Alamat</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-[#584237] dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e0c0b1]/50 dark:divide-slate-700">
                        @forelse ($suppliers as $supplier)
                            <tr class="transition-colors hover:bg-[#eff4ff] dark:hover:bg-slate-700/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#d3e4fe] text-sm font-bold text-[#584237] dark:bg-slate-700 dark:text-slate-200">{{ $supplier->initials }}</div>
                                        <div>
                                            <p class="font-semibold text-[#0b1c30] dark:text-slate-100">{{ $supplier->name }}</p>
                                            <p class="text-sm text-[#584237] dark:text-slate-400">{{ $supplier->category_label }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[#0b1c30] dark:text-slate-200">{{ $supplier->contact_name }}</td>
                                <td class="px-6 py-4 text-[#0b1c30] dark:text-slate-200">{{ $supplier->phone }}</td>
                                <td class="px-6 py-4">
                                    <p class="max-w-[220px] truncate text-[#0b1c30] dark:text-slate-200">{{ $supplier->address ?: '—' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" title="Edit"
                                            data-url="{{ route('suppliers.update', $supplier) }}"
                                            data-name="{{ $supplier->name }}"
                                            data-contact="{{ $supplier->contact_name }}"
                                            data-phone="{{ $supplier->phone }}"
                                            data-category="{{ $supplier->category }}"
                                            data-address="{{ $supplier->address }}"
                                            onclick="openEditEl(this)"
                                            class="rounded-lg p-2 text-[#006398] transition-colors hover:bg-[#dbeafe] dark:text-sky-400 dark:hover:bg-sky-900/40">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Hapus supplier {{ $supplier->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" class="rounded-lg p-2 text-[#ba1a1a] transition-colors hover:bg-[#ffdad6] dark:text-red-400 dark:hover:bg-red-900/40">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2 text-[#584237] dark:text-slate-400">
                                        <span class="material-symbols-outlined text-4xl opacity-60">inventory_2</span>
                                        <p class="font-medium">Belum ada data supplier.</p>
                                        <p class="text-sm">Klik "Tambah Supplier" untuk menambahkan pemasok pertama.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($suppliers->hasPages())
                <div class="border-t border-[#e0c0b1] px-6 py-4 dark:border-slate-700">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Slide-over panel --}}
    <div id="soBackdrop" onclick="closePanel()" class="fixed inset-0 z-40 hidden bg-[#213145]/50 backdrop-blur-sm"></div>
    <div id="soPanel" class="fixed right-0 top-0 z-50 flex h-screen w-full max-w-md translate-x-full transform flex-col bg-white shadow-2xl transition-transform duration-300 ease-in-out dark:bg-slate-800">
        <div class="flex items-center justify-between border-b border-[#e0c0b1] bg-[#f8f9ff] px-6 py-5 dark:border-slate-700 dark:bg-slate-900/40">
            <h2 id="soTitle" class="font-['Poppins'] text-xl font-semibold text-[#0b1c30] dark:text-slate-100">Tambah Supplier Baru</h2>
            <button type="button" onclick="closePanel()" class="rounded-full p-2 text-[#584237] transition-colors hover:bg-[#e5eeff] dark:text-slate-400 dark:hover:bg-slate-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="soForm" method="POST" class="flex flex-1 flex-col overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="soMethod" value="POST">
            <div class="flex-1 space-y-6 overflow-y-auto p-6">
                <div>
                    <label for="fName" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">Nama Supplier <span class="text-[#ba1a1a]">*</span></label>
                    <input id="fName" name="name" type="text" placeholder="Contoh: PT. Maju Berkah"
                        class="w-full rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 text-base text-[#0b1c30] outline-none transition-all placeholder:text-slate-400 focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                </div>
                <div>
                    <label for="fContact" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">Nama Kontak (PIC) <span class="text-[#ba1a1a]">*</span></label>
                    <input id="fContact" name="contact_name" type="text" placeholder="Contoh: Budi Santoso"
                        class="w-full rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 text-base text-[#0b1c30] outline-none transition-all placeholder:text-slate-400 focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                </div>
                <div>
                    <label for="fPhone" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">No. Telepon <span class="text-[#ba1a1a]">*</span></label>
                    <input id="fPhone" name="phone" type="tel" placeholder="Contoh: 0812-3456-7890"
                        class="w-full rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 text-base text-[#0b1c30] outline-none transition-all placeholder:text-slate-400 focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                </div>
                <div>
                    <label for="fCategory" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">Kategori <span class="text-[#ba1a1a]">*</span></label>
                    <div class="relative">
                        <select id="fCategory" name="category"
                            class="w-full appearance-none bg-none rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 text-base text-[#0b1c30] outline-none transition-all focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                            <option value="" disabled selected>Pilih kategori...</option>
                            <option value="basah">Bahan Basah</option>
                            <option value="kering">Bahan Kering</option>
                            <option value="minuman">Minuman</option>
                            <option value="peralatan">Peralatan</option>
                        </select>
                        <span class="material-symbols-outlined pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#584237] dark:text-slate-400">expand_more</span>
                    </div>
                </div>
                <div>
                    <label for="fAddress" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">Alamat Lengkap</label>
                    <textarea id="fAddress" name="address" rows="4" placeholder="Masukkan alamat lengkap supplier..."
                        class="w-full resize-none rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 text-base text-[#0b1c30] outline-none transition-all placeholder:text-slate-400 focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-4 border-t border-[#e0c0b1] bg-[#f8f9ff] p-6 dark:border-slate-700 dark:bg-slate-900/40">
                <button type="button" onclick="closePanel()" class="rounded-xl bg-[#d3e4fe] px-6 py-3 font-semibold text-[#0b1c30] transition-colors hover:bg-[#c0d5f5] dark:bg-slate-600 dark:text-slate-100 dark:hover:bg-slate-500">Batal</button>
                <button type="submit" class="rounded-xl bg-[#f97316] px-6 py-3 font-semibold text-white shadow-[0px_4px_20px_rgba(249,115,22,0.25)] transition-colors hover:bg-[#ea6a0c]">Simpan Supplier</button>
            </div>
        </form>
    </div>

    <script>
        const soPanel = document.getElementById('soPanel');
        const soBackdrop = document.getElementById('soBackdrop');
        const soForm = document.getElementById('soForm');
        const soMethod = document.getElementById('soMethod');
        const soTitle = document.getElementById('soTitle');
        const storeUrl = "{{ route('suppliers.store') }}";

        function openPanel() {
            soBackdrop.classList.remove('hidden');
            setTimeout(() => soPanel.classList.remove('translate-x-full'), 10);
        }
        function closePanel() {
            soPanel.classList.add('translate-x-full');
            setTimeout(() => soBackdrop.classList.add('hidden'), 300);
        }
        function setVal(id, v) { document.getElementById(id).value = (v ?? ''); }

        function openCreate(old) {
            soTitle.textContent = 'Tambah Supplier Baru';
            soForm.action = storeUrl;
            soMethod.value = 'POST';
            setVal('fName', old && old.name);
            setVal('fContact', old && old.contact_name);
            setVal('fPhone', old && old.phone);
            setVal('fCategory', old && old.category);
            setVal('fAddress', old && old.address);
            openPanel();
        }

        function openEdit(data) {
            soTitle.textContent = 'Edit Supplier';
            soForm.action = data.updateUrl;
            soMethod.value = 'PUT';
            setVal('fName', data.name);
            setVal('fContact', data.contact_name);
            setVal('fPhone', data.phone);
            setVal('fCategory', data.category);
            setVal('fAddress', data.address);
            openPanel();
        }

        function openEditEl(el) {
            openEdit({
                updateUrl: el.dataset.url,
                name: el.dataset.name,
                contact_name: el.dataset.contact,
                phone: el.dataset.phone,
                category: el.dataset.category,
                address: el.dataset.address,
            });
        }

        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', () => openCreate(@json(old())));
        @endif
    </script>
</x-app-layout>
