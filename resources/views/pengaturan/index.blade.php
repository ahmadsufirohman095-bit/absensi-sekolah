<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                        @if (session('success'))
                            <div
                                class="p-4 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div
                                class="p-4 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-lg mb-4">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Kustomisasi Halaman Login --}}
                        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm" x-data="{ isCollapsed: false }">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-indigo-500 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100">Kustomisasi Halaman Login</h3>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ubah tampilan halaman masuk sesuai dengan identitas sekolah Anda.</p>
                                        </div>
                                    </div>
                                    <button @click="isCollapsed = !isCollapsed" type="button" class="p-2 text-gray-400 hover:text-gray-500">
                                        <svg x-show="!isCollapsed" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                        <svg x-show="isCollapsed" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="p-6" x-show="!isCollapsed" x-transition>
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-6 gap-y-8">
                                    {{-- Kolom Kiri: Pengaturan Teks --}}
                                    <div class="lg:col-span-1 space-y-6">
                                        <div>
                                            <x-input-label for="login_title" :value="__('Judul Halaman')" />
                                                                                        <textarea id="login_title" name="login_title" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Contoh: Sistem Absensi MTs Al-Muttaqin" maxlength="50" x-on:input="document.getElementById('preview_title').innerText = $event.target.value; document.getElementById('title_char_count').textContent = $event.target.value.length + '/' + $event.target.maxLength;" rows="2">{{ old('login_title', $settings['login_title'] ?? '') }}</textarea>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex justify-between">
                                                <span>Teks utama yang akan muncul di bawah logo.</span>
                                                <span id="title_char_count">{{ strlen(old('login_title', $settings['login_title'] ?? '')) }}/50</span>
                                            </div>
                                        </div>
                                        <div>
                                            <x-input-label for="login_subtitle" :value="__('Subjudul Halaman')" />
                                                                                        <textarea id="login_subtitle" name="login_subtitle" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Contoh: Silakan masuk untuk melanjutkan" maxlength="100" x-on:input="document.getElementById('preview_subtitle').innerText = $event.target.value; document.getElementById('subtitle_char_count').textContent = $event.target.value.length + '/' + $event.target.maxLength;" rows="3">{{ old('login_subtitle', $settings['login_subtitle'] ?? '') }}</textarea>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex justify-between">
                                                <span>Teks kecil di bawah judul utama.</span>
                                                <span id="subtitle_char_count">{{ strlen(old('login_subtitle', $settings['login_subtitle'] ?? '')) }}/100</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Kolom Tengah: Pratinjau --}}
                                    <div class="lg:col-span-1 flex items-center justify-center bg-gray-100 dark:bg-gray-900/50 rounded-lg p-4">
                                        <div class="w-full max-w-sm rounded-lg bg-white dark:bg-gray-800 shadow-lg p-8 text-center">
                                            <img id="login_logo_preview_2" src="{{ isset($settings['login_logo']) && Storage::disk('public')->exists($settings['login_logo']) ? Storage::url($settings['login_logo']) . '?v=' . Storage::disk('public')->lastModified($settings['login_logo']) : asset('images/icon_mts_al_muttaqin.png') }}" alt="Logo Preview" class="h-20 w-20 object-cover rounded-full bg-gray-100 dark:bg-gray-700 ring-2 ring-white dark:ring-gray-900 mx-auto mb-4">
                                            <h1 id="preview_title" class="text-xl font-bold text-gray-900 dark:text-white" style="white-space: pre-wrap;">{{ $settings['login_title'] ?? 'Sistem Absensi Sekolah' }}</h1>
                                            <p id="preview_subtitle" class="text-sm text-gray-600 dark:text-gray-400 mt-1" style="white-space: pre-wrap;">{{ $settings['login_subtitle'] ?? 'Silakan masuk untuk melanjutkan' }}</p>

                                            <div class="mt-6 text-left">
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                                    <div class="mt-1 h-10 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                                    <div class="mt-1 h-10 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                                                </div>
                                                <div class="h-10 bg-indigo-600 rounded-md mt-6"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan: Pengaturan Gambar --}}
                                    <div class="lg:col-span-1 space-y-6">
                                        {{-- Pengaturan Logo --}}
                                        <div>
                                            <x-input-label :value="__('Logo Sekolah')" />
                                            <div class="mt-2 flex items-center gap-x-4">
                                                <img id="login_logo_preview" src="{{ isset($settings['login_logo']) && Storage::disk('public')->exists($settings['login_logo']) ? Storage::url($settings['login_logo']) . '?v=' . Storage::disk('public')->lastModified($settings['login_logo']) : asset('images/icon_mts_al_muttaqin.png') }}" alt="Logo Preview" class="h-16 w-16 object-cover rounded-full bg-gray-100 dark:bg-gray-700 ring-2 ring-white dark:ring-gray-900">
                                                <label for="login_logo" class="cursor-pointer rounded-md bg-white dark:bg-gray-800 font-semibold text-indigo-600 dark:text-indigo-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 dark:focus-within:ring-offset-gray-900 hover:text-indigo-500 dark:hover:text-indigo-300">
                                                    <span>Ubah Logo</span>
                                                    <input id="login_logo" name="login_logo" type="file" class="sr-only" onchange="previewImage(event, 'login_logo_preview')">
                                                </label>
                                            </div>
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Gunakan format PNG, JPG, atau SVG. Ukuran maks 2MB.</p>
                                        </div>

                                        {{-- Pengaturan Background --}}
                                        <div>
                                            <x-input-label :value="__('Gambar Latar')" />
                                            <div class="mt-2">
                                                <div class="w-full h-32 rounded-lg bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center overflow-hidden">
                                                    <img id="login_background_preview" src="{{ isset($settings['login_background']) && Storage::disk('public')->exists($settings['login_background']) ? Storage::url($settings['login_background']) . '?v=' . Storage::disk('public')->lastModified($settings['login_background']) : '' }}" alt="Background Preview" class="w-full h-full object-cover {{ isset($settings['login_background']) && Storage::disk('public')->exists($settings['login_background']) ? '' : 'hidden' }}">
                                                    <label for="login_background" class="{{ isset($settings['login_background']) && Storage::disk('public')->exists($settings['login_background']) ? 'hidden' : '' }} cursor-pointer text-center text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                        <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                        <span class="mt-2 block text-sm font-semibold">Unggah gambar</span>
                                                        <input id="login_background" name="login_background" type="file" class="sr-only" onchange="previewImage(event, 'login_background_preview', true)">
                                                    </label>
                                                </div>
                                                <button type="button" id="change_background_btn" class="{{ isset($settings['login_background']) && Storage::disk('public')->exists($settings['login_background']) ? '' : 'hidden' }} mt-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300" onclick="document.getElementById('login_background').click()">Ubah Gambar</button>
                                            </div>
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Gunakan gambar dengan resolusi tinggi. Ukuran maks 5MB.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kustomisasi Kartu Absensi --}}
                        <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <h3 class="text-lg font-medium mb-4">Kustomisasi Kartu Absensi</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">Atur tampilan dan konten kartu absensi siswa sesuai kebutuhan Anda.</p>
                            <a href="{{ route('absensi.cards.customize') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h10a2 2 0 002-2V8m-2 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v3m0 0h.01M14 15l2 2m0 0l2-2m-2 2v-6"></path></svg>
                                Atur Tampilan Kartu
                            </a>
                        </div>

                    </div>

                    <!-- Tombol Aksi -->
                    <div
                        class="flex items-center justify-end p-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                        <x-primary-button>
                            {{ __('Simpan Pengaturan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
        function previewImage(event, previewId, isBackground = false) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById(previewId);
                output.src = reader.result;

                // Also update the second logo preview
                if (previewId === 'login_logo_preview') {
                    const output2 = document.getElementById('login_logo_preview_2');
                    if (output2) {
                        output2.src = reader.result;
                    }
                }

                if (isBackground) {
                    output.classList.remove('hidden');
                    const label = output.parentElement.querySelector('label');
                    if (label) {
                        label.classList.add('hidden');
                    }
                    const changeButton = document.getElementById('change_background_btn');
                    if (changeButton) {
                        changeButton.classList.remove('hidden');
                    }
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</x-app-layout>