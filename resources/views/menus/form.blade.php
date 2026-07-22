<x-app-layout>
    <div class="fixed inset-0 z-[60] flex justify-end">
        <a href="{{ route('menus.index') }}" aria-label="Tutup"
           class="absolute inset-0 bg-black/50 backdrop-blur-sm"></a>

        <section class="relative flex h-full w-full max-w-lg flex-col bg-white shadow-2xl dark:bg-slate-800">
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="flex h-full flex-col">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <header class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-slate-700">
                    <h2 class="font-heading text-xl font-semibold">{{ $title }}</h2>
                    <a href="{{ route('menus.index') }}"
                       class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 dark:hover:bg-slate-700">
                        <span class="material-symbols-outlined">close</span>
                    </a>
                </header>

                <div class="flex-1 space-y-6 overflow-y-auto p-6">
                    @if ($errors->any())
                        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
                            <p class="mb-1 font-semibold">Periksa kembali isian berikut:</p>
                            <ul class="list-inside list-disc space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label class="mb-2 block text-sm font-semibold">Foto Menu</label>
                        <label for="image"
                               class="group flex cursor-pointer justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 px-4 py-8 transition hover:border-orange-400 hover:bg-orange-50/50 dark:border-slate-600 dark:hover:bg-slate-700/40">
                            <img id="imgPreview"
                                 src="{{ $menu?->image_path ? asset('storage/' . $menu->image_path) : '' }}"
                                 class="{{ $menu?->image_path ? '' : 'hidden' }} h-40 w-full rounded-xl object-cover">
                            <div id="imgPlaceholder" class="{{ $menu?->image_path ? 'hidden' : '' }} text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500 group-hover:bg-orange-100 group-hover:text-orange-600 dark:bg-slate-700">
                                    <span class="material-symbols-outlined">cloud_upload</span>
                                </div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                    <span class="font-semibold text-orange-500">Klik atau tarik gambar</span> ke sini
                                </div>
                                <p class="mt-1 text-xs text-slate-400">PNG, JPG maksimal 5MB</p>
                            </div>
                            <input id="image" name="image" type="file" accept="image/*" class="sr-only" onchange="previewImage(event)">
                        </label>
                        @error('image')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold">Nama Menu <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', $menu?->name) }}"
                               placeholder="Contoh: Nasi Goreng Spesial"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="mb-2 block text-sm font-semibold">Kategori <span class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                            <option value="" disabled @selected(! old('category_id', $menu?->category_id))>Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', $menu?->category_id) === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold">Deskripsi</label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Jelaskan isi atau bahan menu ini..."
                                  class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">{{ old('description', $menu?->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="mb-2 block text-sm font-semibold">Harga <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                            <input id="price" name="price" type="number" min="0" step="100" value="{{ old('price', $menu?->price) }}"
                                   placeholder="0"
                                   class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <p class="text-sm font-semibold">Status Ketersediaan</p>
                            <p class="text-xs text-slate-400">Menu tampil di kasir jika tersedia.</p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="is_available" value="1" class="peer sr-only"
                                   @checked(old('is_available', $menu?->is_available ?? true))>
                            <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-orange-500 peer-checked:after:translate-x-5 dark:bg-slate-600"></div>
                        </label>
                    </div>
                    <div class="border-t border-slate-100 pt-6 dark:border-slate-700">
                    <div class="mb-1 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-500">blender</span>
                        <h3 class="text-sm font-semibold">Resep / Bahan Baku</h3>
                    </div>
                    <p class="mb-4 text-xs text-slate-400">
                        Tentukan bahan baku &amp; jumlah pakai per <strong>1 porsi</strong>. Menu tanpa resep tidak bisa dipesan di POS/kasir.
                    </p>

                    @if ($stocks->isEmpty())
                        <div class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                            Belum ada data bahan baku.
                            <a href="{{ route('stocks.index') }}" class="font-semibold underline">Tambahkan bahan baku dulu</a>
                            agar bisa menyusun resep.
                        </div>
                    @else
                        @php
                            $existing = ($menu && $menu->exists)
                                ? $menu->ingredients->map(fn ($i) => ['stock_id' => (string) $i->stock_id, 'quantity' => (float) $i->quantity])->values()->all()
                                : [];
                            $initialIngredients = old('ingredients', $existing);
                        @endphp

                        <div x-data='{
                                stocks: @json($stocks->map(fn ($s) => ["id" => (string) $s->id, "unit" => $s->unit])->values()),
                                rows: @json(array_values($initialIngredients)),
                                add() { this.rows.push({ stock_id: "", quantity: "" }) },
                                remove(i) { this.rows.splice(i, 1) },
                                unitOf(id) { const s = this.stocks.find(x => x.id == id); return s ? s.unit : "" }
                            }' class="space-y-3">

                            <template x-for="(row, i) in rows" :key="i">
                                <div class="flex items-center gap-2">
                                    <select x-model="row.stock_id" :name="`ingredients[${i}][stock_id]`"
                                            class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                                        <option value="">Pilih bahan</option>
                                        @foreach ($stocks as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>

                                    <div class="relative w-28 shrink-0">
                                        <input type="number" min="0" step="0.01" x-model="row.quantity"
                                            :name="`ingredients[${i}][quantity]`" placeholder="0"
                                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-3 pr-10 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-600 dark:bg-slate-700">
                                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"
                                            x-text="unitOf(row.stock_id)"></span>
                                    </div>

                                    <button type="button" @click="remove(i)"
                                            class="shrink-0 rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/30">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </template>

                            <p x-show="rows.length === 0"
                            class="rounded-xl border border-dashed border-slate-200 px-4 py-4 text-center text-xs text-slate-400 dark:border-slate-600">
                                Belum ada bahan. Klik "Tambah Bahan" untuk menyusun resep.
                            </p>

                            <button type="button" @click="add()"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-orange-300 px-4 py-2.5 text-sm font-semibold text-orange-600 transition hover:bg-orange-50 dark:border-orange-500/50 dark:hover:bg-orange-900/20">
                                <span class="material-symbols-outlined text-base">add</span>
                                Tambah Bahan
                            </button>
                        </div>
                    @endif
                </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4 dark:border-slate-700">
                    <a href="{{ route('menus.index') }}"
                       class="rounded-xl border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">
                        Batal
                    </a>
                    <button type="submit"
                            class="rounded-xl bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imgPreview');
            const placeholder = document.getElementById('imgPlaceholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>
