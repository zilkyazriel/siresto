<x-app-layout>
    <div class="mx-auto w-full max-w-[1440px]" style="font-family: 'Inter', sans-serif;">

        <!-- Header -->
        <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h1 class="text-[32px] font-bold leading-tight text-[#0b1c30] dark:text-slate-100" style="font-family: 'Poppins', sans-serif;">Kelola Staf</h1>
                <p class="mt-1 text-base text-[#584237] dark:text-slate-400">Kelola data dan peran staf restoran Anda.</p>
            </div>
            <button type="button" onclick="openCreate()"
                class="flex items-center justify-center gap-2 rounded-xl bg-[#f97316] px-6 py-3 text-base font-semibold text-white shadow-[0px_4px_20px_rgba(249,115,22,0.25)] transition-all hover:bg-[#ea6a0c] active:scale-95">
                <span class="material-symbols-outlined">add</span>
                Tambah Staf
            </button>
        </div>

        <!-- Flash -->
        @if (session('success'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-[#dcfce7] px-4 py-3 text-sm font-medium text-[#166534] dark:bg-green-900/30 dark:text-green-300">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-[#ffdad6] px-4 py-3 text-sm font-medium text-[#93000a] dark:bg-red-900/30 dark:text-red-300">
                <span class="material-symbols-outlined text-[20px]">error</span>
                {{ session('error') }}
            </div>
        @endif

        <!-- Kartu Tabel Staf -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-[0px_4px_20px_rgba(100,116,139,0.08)] dark:bg-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-[#e0c0b1] bg-[#eff4ff] dark:border-slate-700 dark:bg-slate-900/40">
                            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-[0.05em] text-[#584237] dark:text-slate-400">Nama</th>
                            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-[0.05em] text-[#584237] dark:text-slate-400">Email</th>
                            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-[0.05em] text-[#584237] dark:text-slate-400">Peran</th>
                            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-[0.05em] text-[#584237] dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-[0.05em] text-[#584237] dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e0c0b1] dark:divide-slate-700">
                        @forelse ($staff as $person)
                            @php
                                $roleBadge = match ($person->role) {
                                    'pemilik' => 'bg-[#f3e8ff] text-[#6b21a8]',
                                    'pelayan' => 'bg-[#dcfce7] text-[#166534]',
                                    'koki'    => 'bg-[#dbeafe] text-[#1e40af]',
                                    'kasir'   => 'bg-[#ffedd5] text-[#9a3412]',
                                    'gudang'  => 'bg-[#fef3c7] text-[#92400e]',
                                    default   => 'bg-[#e5eeff] text-[#584237]',
                                };
                            @endphp
                            <tr class="transition-colors hover:bg-[#eff4ff] dark:hover:bg-slate-700/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#d3e4fe] text-sm font-semibold text-[#584237] shadow-sm">
                                            {{ strtoupper(substr($person->name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-[#0b1c30] dark:text-slate-100">{{ $person->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#584237] dark:text-slate-400">{{ $person->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $roleBadge }}">
                                        {{ ucfirst($person->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($person->is_active)
                                        <span class="inline-flex items-center gap-1.5 text-sm text-[#584237] dark:text-slate-300">
                                            <span class="h-2 w-2 rounded-full bg-green-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-400">
                                            <span class="h-2 w-2 rounded-full bg-slate-400"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-1">
                                        <button type="button"
                                            data-id="{{ $person->id }}"
                                            data-name="{{ $person->name }}"
                                            data-email="{{ $person->email }}"
                                            data-role="{{ $person->role }}"
                                            data-status="{{ $person->is_active ? 1 : 0 }}"
                                            onclick="openEdit(this)"
                                            class="rounded-lg p-2 text-[#584237] transition-colors hover:bg-[#d3e4fe] hover:text-[#9d4300] dark:text-slate-400 dark:hover:bg-slate-700">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                        @if ($person->id !== auth()->id())
                                            <form method="POST" action="{{ route('staff.destroy', $person) }}"
                                                onsubmit="return confirm('Hapus staf {{ $person->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg p-2 text-[#584237] transition-colors hover:bg-[#ffdad6] hover:text-[#ba1a1a] dark:text-slate-400 dark:hover:bg-slate-700">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-[#584237] dark:text-slate-400">
                                    Belum ada staf. Klik "Tambah Staf" untuk menambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($staff->hasPages())
                <div class="border-t border-[#e0c0b1] px-6 py-4 dark:border-slate-700">
                    {{ $staff->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Slide-over Tambah/Edit Staf -->
    <div id="staffOverlay" class="fixed inset-0 z-50 hidden bg-[#213145]/50 backdrop-blur-sm">
        <div id="staffPanel" class="absolute inset-y-0 right-0 flex w-full max-w-md translate-x-full flex-col bg-white shadow-[0px_20px_50px_rgba(15,23,42,0.15)] transition-transform duration-300 ease-in-out dark:bg-slate-800">
            <div class="flex items-center justify-between border-b border-[#e0c0b1] px-6 py-5 dark:border-slate-700">
                <h2 id="panelTitle" class="text-2xl font-semibold text-[#0b1c30] dark:text-slate-100" style="font-family: 'Poppins', sans-serif;">Tambah Staf Baru</h2>
                <button type="button" onclick="closePanel()" class="rounded-full p-2 text-[#584237] transition-colors hover:bg-[#d3e4fe] hover:text-[#ba1a1a] dark:text-slate-400 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="staffForm" method="POST" action="{{ route('staff.store') }}" class="flex flex-1 flex-col overflow-hidden">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="user_id" id="fUserId" value="">

                <div class="flex-1 space-y-5 overflow-y-auto p-6">
                    @if ($errors->any())
                        <div class="rounded-xl bg-[#ffdad6] p-3 text-sm text-[#93000a]">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="fName" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">Nama Lengkap</label>
                        <input type="text" name="name" id="fName" placeholder="Masukkan nama lengkap"
                            class="w-full rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 text-base text-[#0b1c30] outline-none transition-all focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="fEmail" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">Email</label>
                        <input type="email" name="email" id="fEmail" placeholder="contoh@siresto.com"
                            class="w-full rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 text-base text-[#0b1c30] outline-none transition-all focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="fRole" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">Peran</label>
                        <div class="relative">
                            <select name="role" id="fRole"
                                class="w-full appearance-none bg-none rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 text-base text-[#0b1c30] outline-none transition-all focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                                <option value="" disabled selected>Pilih Peran</option>
                                <option value="pemilik">Pemilik</option>
                                <option value="pelayan">Pelayan</option>
                                <option value="koki">Koki</option>
                                <option value="kasir">Kasir</option>
                                <option value="gudang">Gudang</option>
                            </select>
                            <span class="material-symbols-outlined pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#584237]">expand_more</span>
                        </div>
                    </div>
                    <div id="statusField" class="hidden">
                        <label for="fStatus" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">Status</label>
                        <div class="relative">
                            <select name="is_active" id="fStatus"
                                class="w-full appearance-none bg-none rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 text-base text-[#0b1c30] outline-none transition-all focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            <span class="material-symbols-outlined pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#584237]">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label for="fPassword" id="passwordLabel" class="mb-2 block text-sm font-semibold text-[#0b1c30] dark:text-slate-300">Password Default</label>
                        <div class="relative">
                            <input type="password" name="password" id="fPassword" placeholder="Minimal 8 karakter"
                                class="w-full rounded-xl border border-[#e0c0b1] bg-white px-4 py-3 pr-12 text-base text-[#0b1c30] outline-none transition-all focus:border-[#f97316] focus:ring-2 focus:ring-[#f97316]/20 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#584237] transition-colors hover:text-[#9d4300]">
                                <span class="material-symbols-outlined" id="passwordIcon">visibility</span>
                            </button>
                        </div>
                        <p id="passwordHint" class="mt-2 hidden text-xs text-[#584237] dark:text-slate-400">Kosongkan jika tidak ingin mengubah password.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-[#e0c0b1] px-6 py-5 dark:border-slate-700">
                    <button type="button" onclick="closePanel()"
                        class="rounded-xl bg-[#d3e4fe] px-6 py-3 text-base font-semibold text-[#0b1c30] transition-colors hover:bg-[#c0d5f5] dark:bg-slate-700 dark:text-slate-200">Batal</button>
                    <button type="submit"
                        class="rounded-xl bg-[#f97316] px-6 py-3 text-base font-semibold text-white shadow-[0px_4px_20px_rgba(249,115,22,0.25)] transition-all hover:bg-[#ea6a0c] active:scale-95">Simpan Staf</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const staffOverlay = document.getElementById('staffOverlay');
        const staffPanel = document.getElementById('staffPanel');
        const staffForm = document.getElementById('staffForm');
        const storeUrl = "{{ route('staff.store') }}";
        const updateBase = "{{ url('/staf') }}";

        function openPanel() {
            staffOverlay.classList.remove('hidden');
            void staffPanel.offsetWidth;
            staffPanel.classList.remove('translate-x-full');
        }
        function closePanel() {
            staffPanel.classList.add('translate-x-full');
            setTimeout(function () { staffOverlay.classList.add('hidden'); }, 300);
        }
        function togglePassword() {
            const input = document.getElementById('fPassword');
            const icon = document.getElementById('passwordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
        function openCreate() {
            document.getElementById('panelTitle').textContent = 'Tambah Staf Baru';
            staffForm.action = storeUrl;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('fUserId').value = '';
            document.getElementById('fName').value = '';
            document.getElementById('fEmail').value = '';
            document.getElementById('fRole').value = '';
            document.getElementById('fStatus').value = '1';
            document.getElementById('statusField').classList.add('hidden');
            document.getElementById('fPassword').value = '';
            document.getElementById('passwordLabel').textContent = 'Password Default';
            document.getElementById('passwordHint').classList.add('hidden');
            openPanel();
        }
        function openEdit(btn) {
            document.getElementById('panelTitle').textContent = 'Edit Staf';
            staffForm.action = updateBase + '/' + btn.dataset.id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('fUserId').value = btn.dataset.id;
            document.getElementById('fName').value = btn.dataset.name;
            document.getElementById('fEmail').value = btn.dataset.email;
            document.getElementById('fRole').value = btn.dataset.role;
            document.getElementById('fStatus').value = btn.dataset.status;
            document.getElementById('statusField').classList.remove('hidden');
            document.getElementById('fPassword').value = '';
            document.getElementById('passwordLabel').textContent = 'Password Baru';
            document.getElementById('passwordHint').classList.remove('hidden');
            openPanel();
        }
        staffOverlay.addEventListener('click', function (e) {
            if (e.target === staffOverlay) closePanel();
        });

        @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            var oldId = @json(old('user_id'));
            if (oldId) {
                document.getElementById('panelTitle').textContent = 'Edit Staf';
                staffForm.action = updateBase + '/' + oldId;
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('fUserId').value = oldId;
                document.getElementById('statusField').classList.remove('hidden');
                document.getElementById('passwordLabel').textContent = 'Password Baru';
                document.getElementById('passwordHint').classList.remove('hidden');
            } else {
                document.getElementById('statusField').classList.add('hidden');
            }
            document.getElementById('fName').value = @json(old('name') ?? '');
            document.getElementById('fEmail').value = @json(old('email') ?? '');
            document.getElementById('fRole').value = @json(old('role') ?? '');
            document.getElementById('fStatus').value = @json(old('is_active') ?? '1');
            openPanel();
        });
        @endif
    </script>
</x-app-layout>
