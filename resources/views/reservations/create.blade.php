<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <div class="px-4 py-6 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('reservations.index') }}" class="flex h-9 w-9 items-center justify-center rounded-full border border-[#e0c0b1]/40 text-[#584237] hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <h2 class="font-['Poppins'] text-[28px] font-bold text-[#0b1c30] dark:text-slate-100">Buat Reservasi</h2>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('reservations.store') }}" class="max-w-2xl rounded-2xl border border-[#e0c0b1]/30 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                {{-- Nama --}}
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-[#584237] dark:text-slate-400">Nama Pelanggan <span class="text-[#ba1a1a]">*</span></label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                           class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white px-4 py-3 text-sm text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="mis. Budi Santoso">
                </div>

                {{-- No HP --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#584237] dark:text-slate-400">No. HP (opsional)</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" inputmode="tel"
                           class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white px-4 py-3 text-sm text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="08xxxxxxxxxx">
                </div>

                {{-- Jumlah orang --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#584237] dark:text-slate-400">Jumlah Orang <span class="text-[#ba1a1a]">*</span></label>
                    <input type="number" name="party_size" value="{{ old('party_size', 1) }}" min="1" max="100" required
                           class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white px-4 py-3 text-sm text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                </div>

                {{-- Waktu --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#584237] dark:text-slate-400">Waktu Reservasi <span class="text-[#ba1a1a]">*</span></label>
                    <input type="datetime-local" name="reserved_at" value="{{ old('reserved_at') }}" required
                           class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white px-4 py-3 text-sm text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                </div>

                {{-- Meja --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#584237] dark:text-slate-400">Meja (opsional)</label>
                    <select name="dining_table_id"
                            class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white px-4 py-3 text-sm text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                        <option value="">— Belum ditentukan —</option>
                        @foreach ($tables as $table)
                            <option value="{{ $table->id }}" @selected(old('dining_table_id') == $table->id)>
                                Meja {{ $table->number }} (kapasitas {{ $table->capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Catatan --}}
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-[#584237] dark:text-slate-400">Catatan (opsional)</label>
                    <textarea name="note" rows="3"
                              class="w-full rounded-xl border border-[#e0c0b1]/40 bg-white px-4 py-3 text-sm text-[#0b1c30] focus:border-[#f97316] focus:ring-0 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="mis. dekat jendela, ada anak kecil, dll.">{{ old('note') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('reservations.index') }}" class="rounded-xl border border-[#e0c0b1]/40 px-5 py-2.5 text-sm font-semibold text-[#584237] hover:bg-[#eff4ff] dark:border-slate-700 dark:text-slate-300">Batal</a>
                <button type="submit" class="rounded-xl bg-[#f97316] px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#ea580c]">Simpan Reservasi</button>
            </div>
        </form>
    </div>
</x-app-layout>