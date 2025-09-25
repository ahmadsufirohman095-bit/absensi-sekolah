<x-guest-layout>
    <!-- Form Container dengan Efek Kaca -->
    <div class="w-full max-w-md p-8 space-y-8 bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl shadow-2xl">
        <!-- Logo & Judul Aplikasi -->
        <div class="text-center text-white">
            <a href="/" class="flex justify-center mb-4">
                <img class="w-24 h-24" src="{{ setting('login_logo') && Storage::disk('public')->exists(setting('login_logo')) ? Storage::url(setting('login_logo')) . '?v=' . Storage::disk('public')->lastModified(setting('login_logo')) : asset('images/icon_mts_al_muttaqin.png') }}" alt="Logo Sekolah">
            </a>
            <h2 class="text-3xl font-bold tracking-tight">
                {!! nl2br(e(setting('login_title', 'Sistem Absensi'))) !!}
            </h2>
            <p class="mt-2 text-sm text-slate-300">
                {!! nl2br(e(setting('login_subtitle', 'Silakan masuk untuk melanjutkan'))) !!}
            </p>
        </div>

        <!-- Form Login -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- ID Pengguna -->
            <div>
                <x-input-label for="identifier" value="ID Pengguna (NIS / NIP)" class="text-slate-300" />
                <x-text-input id="identifier" name="identifier" type="text" class="mt-1 block w-full bg-slate-200/20 border-slate-600 text-white focus:border-indigo-400 focus:ring-indigo-400" :value="old('identifier')" required autofocus placeholder="Masukkan NIS atau NIP Anda" autocomplete="username" />
                <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" value="Password" class="text-slate-300" />
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-indigo-400 hover:text-indigo-300" href="{{ route('password.request') }}">
                            {{ __('Lupa password?') }}
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full pr-10 bg-slate-200/20 border-slate-600 text-white focus:border-indigo-400 focus:ring-indigo-400" required autocomplete="current-password" placeholder="Masukkan password Anda" />
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 cursor-pointer z-10" onclick="togglePasswordVisibility('password')">
                        <svg id="eye-open-password" class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg id="eye-closed-password" class="h-6 w-6 text-gray-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.981 12C5.41 10.02 8.02 8.5 12 8.5c3.98 0 6.59 1.52 8.019 3.5-.07.207-.07.431 0 .639C18.59 13.98 15.98 15.5 12 15.5c-3.98 0-6.59-1.52-8.019-3.5-.07-.207-.07-.431 0-.639z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                @if ($errors->has('email'))
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                @endif
            </div>

            <!-- Captcha -->
            <div class="text-center">
                <x-input-label for="captcha" class="text-slate-300 text-lg font-semibold mb-2">
                    Berapa {{ $num1 }} {{ $operator }} {{ $num2 }}?
                </x-input-label>
                <x-text-input id="captcha" name="captcha" type="text" class="mt-1 block w-full bg-slate-200/20 border-slate-600 text-white focus:border-indigo-400 focus:ring-indigo-400 text-center text-xl py-2" required placeholder="Masukkan hasil perhitungan" />
                <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-500 text-indigo-600 shadow-sm focus:ring-indigo-600 bg-slate-900/50" name="remember">
                    <span class="ms-2 text-sm text-slate-300">{{ __('Ingat saya') }}</span>
                </label>
            </div>

            <!-- Tombol Login -->
            <div>
                <x-primary-button class="w-full flex justify-center py-3 text-sm">
                    {{ __('Masuk') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    </x-guest-layout>
