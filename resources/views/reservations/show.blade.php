<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    @php
        $badge = [
            'menunggu' => 'bg-amber-100 text-amber-700',
            'hadir'    => 'bg-blue-100 text-blue-700',
            'selesai'  => 'bg-green-100 text-green-700',
            'batal'    => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="px-4 py-6 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('reservations.index') }}" class="flex h-9 w-9 items-center justify-center rounded-full border border-[#e0c0b1]/40 text-[#584237] hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <h2 class="font-['Poppins'] text-[28px] font-bold text-[#0b1c30] dark:text-slate-100">Detail Reservasi</h2>
                <p class="mt-0.5 text-sm text-[#584237] dark:text-slate-400">{{ $reservation->code }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- Detail --}}
            <div class="lg:col-span-7">
                <div class="rounded-2xl border border-[#e0c0b1]/30 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="mb-4 flex items-center justify-between border-b border-[#e0c0b1]/30 pb-4 dark:border-slate-700">
                        <h3 class="font-['Poppins'] text-xl font-bold text-[#0b1c30] dark:text-slate-100">{{ $reservation->customer_name }}</h3>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badge[$reservation->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($reservation->status) }}</span>
                    </div>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-[#584237] dark:text-slate-400">No. HP</dt><dd class="text-right font-medium text-[#0b1c30] dark:text-slate-100">{{ $reservation->customer_phone ?: '-' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-[#584237] dark:text-slate-400">Waktu</dt><dd class="text-right font-medium text-[#0b1c30] dark:text-slate-100">{{ $reservation->reserved_at->format('d/m/Y H:i') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-[#584237] dark:text-slate-400">Jumlah Orang</dt><dd class="text-right font-medium text-[#0b1c30] dark:text-slate-100">{{ $reservation->party_size }} orang</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-[#584237] dark:text-slate-400">Meja</dt><dd class="text-right font-medium text-[#0b1c30] dark:text-slate-100">{{ $reservation->diningTable ? 'Meja '.$reservation->diningTable->number : 'Belum ditentukan' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-[#584237] dark:text-slate-400">Dicatat oleh</dt><dd class="text-right font-medium text-[#0b1c30] dark:text-slate-100">{{ optional($reservation->user)->name ?? '-' }}</dd></div>
                        @if ($reservation->note)
                            <div class="border-t border-[#e0c0b1]/30 pt-3 dark:border-slate-700">
                                <dt class="mb-1 text-[#584237] dark:text-slate-400">Catatan</dt>
                                <dd class="text-[#0b1c30] dark:text-slate-100">{{ $reservation->note }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Aksi status --}}
            <div class="lg:col-span-5">
                <div class="rounded-2xl border border-[#e0c0b1]/30 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <h3 class="mb-4 font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Ubah Status</h3>
                    <form method="POST" action="{{ route('reservations.updateStatus', $reservation) }}" class="flex flex-col gap-3">
                        @csrf
                        @if ($reservation->status === 'menunggu')
                            <button type="submit" name="status" value="hadir" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#f97316] py-3 text-sm font-semibold text-white transition-colors hover:bg-[#ea580c]">
                                <span class="material-symbols-outlined text-[20px]">how_to_reg</span> Tandai Hadir
                            </button>
                            <button type="submit" name="status" value="batal" class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-300 py-3 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50">
                                <span class="material-symbols-outlined text-[20px]">cancel</span> Batalkan
                            </button>
                        @elseif ($reservation->status === 'hadir')
                            <button type="submit" name="status" value="selesai" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#f97316] py-3 text-sm font-semibold text-white transition-colors hover:bg-[#ea580c]">
                                <span class="material-symbols-outlined text-[20px]">task_alt</span> Tandai Selesai
                            </button>
                            <button type="submit" name="status" value="batal" class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-300 py-3 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50">
                                <span class="material-symbols-outlined text-[20px]">cancel</span> Batalkan
                            </button>
                        @else
                            <p class="text-sm text-[#584237] dark:text-slate-400">Reservasi ini sudah <strong>{{ $reservation->status }}</strong>.</p>
                            <button type="submit" name="status" value="menunggu" class="flex w-full items-center justify-center gap-2 rounded-xl border border-[#e0c0b1]/40 py-3 text-sm font-semibold text-[#584237] transition-colors hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300">
                                <span class="material-symbols-outlined text-[20px]">undo</span> Buka Lagi (Menunggu)
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>