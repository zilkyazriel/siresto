<x-app-layout>
<div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8 py-6">
    <a href="{{ route('complaints.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-orange-600">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke Daftar
    </a>

    @if (session('success'))
    <div class="mb-6 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        <span class="material-symbols-outlined text-[20px]">check_circle</span>{{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    @php
        $badge = [
            'baru' => ['Baru', 'bg-amber-100 text-amber-700'],
            'diproses' => ['Diproses', 'bg-blue-100 text-blue-700'],
            'selesai' => ['Selesai', 'bg-green-100 text-green-700'],
        ];
        [$stLabel, $stClass] = $badge[$complaint->status] ?? [$complaint->status, 'bg-slate-100 text-slate-600'];
    @endphp

    <div class="mb-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                <h1 class="font-['Poppins'] text-2xl font-bold text-[#0b1c30] dark:text-slate-100">{{ $complaint->code }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Dicatat {{ $complaint->created_at->format('d M Y, H:i') }} oleh {{ $complaint->user->name ?? '-' }}</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $stClass }}">{{ $stLabel }}</span>
        </div>

        <dl class="space-y-4">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Pelanggan</dt>
                <dd class="text-sm text-slate-700 dark:text-slate-200">{{ $complaint->customer_name ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Isi Keluhan</dt>
                <dd class="whitespace-pre-line text-sm text-slate-700 dark:text-slate-200">{{ $complaint->content }}</dd>
            </div>
            @if ($complaint->resolution)
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tindak Lanjut</dt>
                <dd class="whitespace-pre-line text-sm text-slate-700 dark:text-slate-200">{{ $complaint->resolution }}</dd>
            </div>
            @endif
            @if ($complaint->resolved_at)
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Diselesaikan</dt>
                <dd class="text-sm text-slate-700 dark:text-slate-200">{{ $complaint->resolved_at->format('d M Y, H:i') }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <h3 class="mb-4 font-['Poppins'] text-lg font-bold text-[#0b1c30] dark:text-slate-100">Perbarui Status</h3>
        <form method="POST" action="{{ route('complaints.updateStatus', $complaint->id) }}">
            @csrf
            <div class="mb-4">
                <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700 sm:max-w-xs">
                    <option value="baru" @selected($complaint->status === 'baru')>Baru</option>
                    <option value="diproses" @selected($complaint->status === 'diproses')>Diproses</option>
                    <option value="selesai" @selected($complaint->status === 'selesai')>Selesai</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Catatan Tindak Lanjut <span class="font-normal text-slate-400">(opsional)</span></label>
                <textarea name="resolution" rows="4" maxlength="2000" placeholder="mis. Sudah diganti dengan pesanan baru & minta maaf ke pelanggan." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">{{ old('resolution', $complaint->resolution) }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="flex items-center gap-2 rounded-xl bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 active:scale-95">
                    <span class="material-symbols-outlined">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>