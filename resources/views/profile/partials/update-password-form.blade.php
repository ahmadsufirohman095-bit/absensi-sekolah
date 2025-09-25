<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Perbarui Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak untuk tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Password Saat Ini" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" placeholder="Masukkan password Anda saat ini" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Password Baru" />
            <div class="relative">
                <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full pr-10" autocomplete="new-password" placeholder="Masukkan password baru" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 cursor-pointer z-10" onclick="togglePasswordVisibility('update_password_password')">
                    <svg id="eye-open-update_password_password" class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg id="eye-closed-update_password_password" class="h-6 w-6 text-gray-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.981 12C5.41 10.02 8.02 8.5 12 8.5c3.98 0 6.59 1.52 8.019 3.5-.07.207-.07.431 0 .639C18.59 13.98 15.98 15.5 12 15.5c-3.98 0-6.59-1.52-8.019-3.5-.07-.207-.07-.431 0-.639z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2">
                @if ($errors->updatePassword->has('password'))
                    @foreach ($errors->updatePassword->get('password') as $message)
                        @if (str_contains($message, 'min.string'))
                            <p class="text-sm text-red-600 dark:text-red-400">Password minimal 8 karakter.</p>
                        @elseif (str_contains($message, 'password.mixed'))
                            <p class="text-sm text-red-600 dark:text-red-400">Password harus mengandung huruf besar dan kecil.</p>
                        @elseif (str_contains($message, 'password.symbols'))
                            <p class="text-sm text-red-600 dark:text-red-400">Password harus mengandung simbol.</p>
                        @else
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @endif
                    @endforeach
                @endif
            </x-input-error>
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Password Baru" />
            <div class="relative">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full pr-10" autocomplete="new-password" placeholder="Konfirmasi password baru" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 cursor-pointer z-10" onclick="togglePasswordVisibility('update_password_password_confirmation')">
                    <svg id="eye-open-update_password_password_confirmation" class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg id="eye-closed-update_password_password_confirmation" class="h-6 w-6 text-gray-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.981 12C5.41 10.02 8.02 8.5 12 8.5c3.98 0 6.59 1.52 8.019 3.5-.07.207-.07.431 0 .639C18.59 13.98 15.98 15.5 12 15.5c-3.98 0-6.59-1.52-8.019-3.5-.07-.207-.07-.431 0-.639z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>

<script>
    function togglePasswordVisibility(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const eyeOpen = document.getElementById(`eye-open-${fieldId}`);
        const eyeClosed = document.getElementById(`eye-closed-${fieldId}`);

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            passwordField.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>
