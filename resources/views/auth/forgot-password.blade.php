<x-guest-layout>
    <div class="w-full max-w-md p-8 space-y-8 bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl shadow-2xl text-white">
        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight">
                {{ __('Lupa Password?') }}
            </h2>
            <p class="mt-2 text-sm text-slate-300">
                {{ __('Tidak masalah. Cukup beri tahu kami alamat email Anda dan kami akan mengirimi Anda email tautan pengaturan ulang kata sandi yang memungkinkan Anda memilih yang baru.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-slate-300" />
                <x-text-input id="email" class="mt-1 block w-full bg-slate-200/20 border-slate-600 text-white focus:border-indigo-400 focus:ring-indigo-400" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" id="email-error" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button id="submit-button">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const errorElement = document.querySelector('#email-error ul li');
                const submitButton = document.getElementById('submit-button');

                // Cek jika pesan error throttling ada
                if (errorElement && errorElement.textContent.includes('detik')) {
                    const originalMessage = errorElement.textContent;
                    let seconds = parseInt(originalMessage.match(/\d+/)[0]);

                    const originalButtonText = submitButton.innerHTML;
                    submitButton.disabled = true;

                    const interval = setInterval(() => {
                        if (seconds > 0) {
                            // Ganti angka di pesan asli, bukan seluruh pesan
                            errorElement.innerHTML = originalMessage.replace(/\d+/, `<strong>${seconds}</strong>`);
                            submitButton.textContent = `Tunggu (${seconds}s)`;
                            seconds--;
                        } else {
                            clearInterval(interval);
                            errorElement.innerHTML = `<span class="text-green-400">Anda dapat mencoba lagi sekarang.</span>`;
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;
                        }
                    }, 1000);
                }
            });
        </script>
    </div>
</x-guest-layout>
