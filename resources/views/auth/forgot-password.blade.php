<x-guest-layout>
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-orange-500 to-orange-700 p-12 text-white lg:flex">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-24 -left-20 h-80 w-80 rounded-full bg-white/10"></div>
            <div class="relative flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20"><span class="material-symbols-outlined">restaurant</span></span>
                <span class="font-heading text-2xl font-extrabold">SIRESTO</span>
            </div>
            <div class="relative">
                <h1 class="font-heading text-4xl font-bold leading-tight">Lupa password?</h1>
                <p class="mt-4 max-w-sm text-white/80">Tenang, hal ini biasa terjadi. Masukkan email Anda dan kami akan mengirimkan tautan untuk membuat password baru.</p>
            </div>
            <div class="relative text-sm text-white/60">&copy; {{ date('Y') }} SIRESTO &mdash; Sistem Informasi Restoran</div>
        </div>

        <div class="flex items-center justify-center bg-slate-50 p-6 sm:p-12">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-2 lg:hidden">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500 text-white"><span class="material-symbols-outlined">restaurant</span></span>
                    <span class="font-heading text-xl font-extrabold text-slate-800">SIRESTO</span>
                </div>
                <h2 class="font-heading text-2xl font-bold text-slate-800">Atur ulang password</h2>
                <p class="mt-1 text-sm text-slate-500">Masukkan email akun Anda. Kami akan mengirim tautan untuk membuat password baru.</p>

                @if (session('status'))
                    <div class="mt-4 flex items-start gap-2 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                        <span class="material-symbols-outlined text-[20px]">mark_email_read</span>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                               placeholder="nama@restoran.com">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-orange-500 py-3 font-semibold text-white shadow-lg shadow-orange-500/30 transition hover:bg-orange-600">Kirim Tautan Atur Ulang</button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Ingat password Anda?
                    <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:underline">Kembali ke halaman masuk</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>