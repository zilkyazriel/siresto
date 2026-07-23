<x-app-layout>
<div class="mx-auto w-full max-w-2xl px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <a href="{{ route('complaints.index') }}" class="mb-2 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-orange-600">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
        <h1 class="font-['Poppins'] text-2xl font-bold text-[#0b1c30] dark:text-slate-100">Catat Keluhan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Rekam keluhan pelanggan untuk ditindaklanjuti.</p>
    </div>

    @if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('complaints.store') }}" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        @csrf
        <div class="mb-4">
            <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Nama Pelanggan <span class="font-normal text-slate-400">(opsional)</span></label>
            <input type="text" name="customer_name" value="{{ old('customer_name') }}" maxlength="255" placeholder="mis. Ibu Sari (meja 4)" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
        </div>
        <div class="mb-6">
            <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-300">Isi Keluhan</label>
            <textarea name="content" rows="5" maxlength="2000" required placeholder="Tuliskan keluhan pelanggan..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">{{ old('content') }}</textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('complaints.index') }}" class="rounded-xl px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</a>
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 active:scale-95">
                <span class="material-symbols-outlined">save</span> Simpan
            </button>
        </div>
    </form>
</div>
</x-app-layout>