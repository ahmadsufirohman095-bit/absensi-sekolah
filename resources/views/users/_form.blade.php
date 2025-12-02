@csrf
<div x-data="{ role: '{{ old('role', $user->role ?? 'siswa') }}' }" x-init="
    $nextTick(() => { 
        if (typeof initializeFlatpickrDMY === 'function') {
            initializeFlatpickrDMY();
        }
    });
    $watch('role', (value) => {
        $nextTick(() => {
            if (typeof initializeFlatpickrDMY === 'function') {
                initializeFlatpickrDMY();
            }
        });
    })
">

    <!-- Data User Utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name ?? '')" required
                autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username', $user->username ?? '')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>
        <div>
            <label for="identifier" class="block font-medium text-sm text-gray-700 dark:text-gray-300"
                x-text="role === 'siswa' ? 'NIS' : (role === 'guru' ? 'NIP' : 'Identifier')"></label>
            <input id="identifier"
                class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                :type="(role === 'siswa') ? 'number' : 'text'" name="identifier"
                value="{{ old('identifier', $user->identifier ?? '') }}"
                x-bind:placeholder="role === 'siswa' ? 'Masukkan NIS' : (role === 'guru' ? 'Masukkan NIP' : 'Masukkan Identifier')"
                required>
            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email ?? '')"
                required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="role" :value="__('Role')" />
            <select name="role" id="role" x-model="role"
                class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="admin" @if (old('role', $user->role) == 'admin') selected @endif>Admin</option>
                <option value="guru" @if (old('role', $user->role) == 'guru') selected @endif>Guru</option>
                <option value="siswa" @if (old('role', $user->role) == 'siswa') selected @endif>Siswa</option>
                <option value="tu" @if (old('role', $user->role) == 'tu') selected @endif>TU (Tata Usaha)</option>
                <option value="other" @if (old('role', $user->role) == 'other') selected @endif>Lainnya</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>
        <div x-data x-init="initializePasswordStrengthChecker('password', 'password-strength-user-form')">
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <div class="relative">
                    <x-text-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" placeholder="Masukkan password" autocomplete="new-password" />
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 cursor-pointer z-10" onclick="togglePasswordVisibility('password')">
                        <svg id="eye-open-password" class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg id="eye-closed-password" class="h-6 w-6 text-gray-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.981 12C5.41 10.02 8.02 8.5 12 8.5c3.98 0 6.59 1.52 8.019 3.5-.07.207-.07.431 0 .639C18.59 13.98 15.98 15.5 12 15.5c-3.98 0-6.59-1.52-8.019-3.5-.07-.207-.07.431 0 .639z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <div id="password-strength-user-form" class="mt-2 text-sm"></div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                @error('password')
                    @if ($message === 'validation.password.mixed')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-2">Kata sandi harus mengandung setidaknya satu huruf besar dan satu huruf kecil.</p>
                    @endif
                    @if ($message === 'validation.password.symbols')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-2">Kata sandi harus mengandung setidaknya satu simbol.</p>
                    @endif
                    @if ($message === 'validation.password.numbers')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-2">Kata sandi harus mengandung setidaknya satu angka.</p>
                    @endif
                @enderror
                @if ($user->exists)
                    <small class="text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengubah password.</small>
                @endif
            </div>
            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <div class="relative">
                    <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10" type="password" name="password_confirmation" placeholder="Konfirmasi password" autocomplete="new-password" />
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 cursor-pointer z-10" onclick="togglePasswordVisibility('password_confirmation')">
                        <svg id="eye-open-password_confirmation" class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg id="eye-closed-password_confirmation" class="h-6 w-6 text-gray-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.981 12C5.41 10.02 8.02 8.5 12 8.5c3.98 0 6.59 1.52 8.019 3.5-.07.207-.07.431 0 .639C18.59 13.98 15.98 15.5 12 15.5c-3.98 0-6.59-1.52-8.019-3.5-.07-.207-.07.431 0 .639z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="my-6 border-t border-gray-200 dark:border-gray-700"></div>

    <!-- BAGIAN DATA PROFIL SPESIFIK PERAN -->
    <div class="mt-6">
        <!-- Field Profil Admin -->
        <template x-if="role === 'admin'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="admin_jabatan" value="Jabatan" />
                    <x-text-input id="admin_jabatan" name="admin_jabatan" :value="old('admin_jabatan', optional($user->adminProfile)->jabatan)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('admin_jabatan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="admin_telepon" value="Telepon" />
                    <x-text-input id="admin_telepon" name="admin_telepon" :value="old('admin_telepon', optional($user->adminProfile)->telepon)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('admin_telepon')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="admin_tanggal_bergabung" value="Tanggal Bergabung" />
                    <x-text-input id="admin_tanggal_bergabung"
                        :value="optional($user->adminProfile)->tanggal_bergabung ? \Carbon\Carbon::parse($user->adminProfile->tanggal_bergabung)->translatedFormat('d F Y') : 'Otomatis terisi saat dibuat'"
                        class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" type="text" disabled />
                </div>
                <div>
                    <x-input-label for="admin_tanggal_lahir" value="Tanggal Lahir" />
                    <x-text-input id="admin_tanggal_lahir" name="tanggal_lahir"
                        value="{{ old('tanggal_lahir', optional($user->adminProfile)->tanggal_lahir ? $user->adminProfile->tanggal_lahir->format('Y-m-d') : '') }}"
                        class="block mt-1 w-full flatpickr-dmy" type="text" placeholder="yyyy-mm-dd" />
                    <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="admin_tempat_lahir" value="Tempat Lahir" />
                    <x-text-input id="admin_tempat_lahir" name="admin_tempat_lahir" :value="old('admin_tempat_lahir', optional($user->adminProfile)->tempat_lahir)"
                        class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('admin_tempat_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="admin_jenis_kelamin" value="Jenis Kelamin" />
                    <select name="admin_jenis_kelamin" id="admin_jenis_kelamin"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="laki-laki" @if (old('admin_jenis_kelamin', optional($user->adminProfile)->jenis_kelamin) == 'laki-laki') selected @endif>Laki-laki</option>
                        <option value="perempuan" @if (old('admin_jenis_kelamin', optional($user->adminProfile)->jenis_kelamin) == 'perempuan') selected @endif>Perempuan</option>
                    </select>
                    <x-input-error :messages="$errors->get('admin_jenis_kelamin')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="admin_foto" value="Foto Profil" />
                    <input type="file" id="admin_foto" name="admin_foto"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 file:hover:bg-gray-300 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm" />
                    <x-input-error :messages="$errors->get('admin_foto')" class="mt-2" />
                </div>
            </div>
        </template>

        <!-- Field Profil TU -->
        <template x-if="role === 'tu'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="tu_jabatan" value="Jabatan" />
                    <x-text-input id="tu_jabatan" name="tu_jabatan" :value="old('tu_jabatan', optional($user->tuProfile)->jabatan)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('tu_jabatan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tu_telepon" value="Telepon" />
                    <x-text-input id="tu_telepon" name="tu_telepon" :value="old('tu_telepon', optional($user->tuProfile)->telepon)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('tu_telepon')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tu_tanggal_bergabung" value="Tanggal Bergabung" />
                    <x-text-input id="tu_tanggal_bergabung"
                        :value="optional($user->tuProfile)->tanggal_bergabung ? \Carbon\Carbon::parse($user->tuProfile->tanggal_bergabung)->translatedFormat('d F Y') : 'Otomatis terisi saat dibuat'"
                        class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" type="text" disabled />
                </div>
                <div>
                    <x-input-label for="tu_tanggal_lahir" value="Tanggal Lahir" />
                    <x-text-input id="tu_tanggal_lahir" name="tu_tanggal_lahir"
                        value="{{ old('tu_tanggal_lahir', optional($user->tuProfile)->tanggal_lahir ? $user->tuProfile->tanggal_lahir->format('Y-m-d') : '') }}"
                        class="block mt-1 w-full flatpickr-dmy" type="text" placeholder="yyyy-mm-dd" />
                    <x-input-error :messages="$errors->get('tu_tanggal_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tu_tempat_lahir" value="Tempat Lahir" />
                    <x-text-input id="tu_tempat_lahir" name="tu_tempat_lahir" :value="old('tu_tempat_lahir', optional($user->tuProfile)->tempat_lahir)"
                        class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('tu_tempat_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tu_jenis_kelamin" value="Jenis Kelamin" />
                    <select name="tu_jenis_kelamin" id="tu_jenis_kelamin"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="laki-laki" @if (old('tu_jenis_kelamin', optional($user->tuProfile)->jenis_kelamin) == 'laki-laki') selected @endif>Laki-laki</option>
                        <option value="perempuan" @if (old('tu_jenis_kelamin', optional($user->tuProfile)->jenis_kelamin) == 'perempuan') selected @endif>Perempuan</option>
                    </select>
                    <x-input-error :messages="$errors->get('tu_jenis_kelamin')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tu_foto" value="Foto Profil" />
                    <input type="file" id="tu_foto" name="tu_foto"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 file:hover:bg-gray-300 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm" />
                    <x-input-error :messages="$errors->get('tu_foto')" class="mt-2" />
                </div>
            </div>
        </template>

        <!-- Bidang profil untuk peran 'Lainnya' -->
        <template x-if="role === 'other'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <x-input-label for="custom_role_name" :value="__('Nama Role Kustom')" />
                    <x-text-input id="custom_role_name" class="block mt-1 w-full" type="text" name="custom_role_name" :value="old('custom_role_name', $user->custom_role ?? '')" required placeholder="Misal: Bendahara, Kurikulum" />
                    <x-input-error :messages="$errors->get('custom_role_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="other_profile_jabatan" value="Jabatan" />
                    <x-text-input id="other_profile_jabatan" name="other_jabatan" :value="old('other_jabatan', optional($user->otherProfile)->jabatan)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('other_jabatan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="other_profile_telepon" value="Telepon" />
                    <x-text-input id="other_profile_telepon" name="other_telepon" :value="old('other_telepon', optional($user->otherProfile)->telepon)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('other_telepon')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="other_profile_tanggal_bergabung" value="Tanggal Bergabung" />
                    <x-text-input id="other_profile_tanggal_bergabung"
                        :value="optional($user->otherProfile)->tanggal_bergabung ? \Carbon\Carbon::parse($user->otherProfile->tanggal_bergabung)->translatedFormat('d F Y') : 'Otomatis terisi saat dibuat'"
                        class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" type="text" disabled />
                </div>
                <div>
                    <x-input-label for="other_profile_tanggal_lahir" value="Tanggal Lahir" />
                    <x-text-input id="other_profile_tanggal_lahir" name="other_tanggal_lahir"
                        value="{{ old('other_tanggal_lahir', optional($user->otherProfile)->tanggal_lahir ? $user->otherProfile->tanggal_lahir->format('Y-m-d') : '') }}"
                        class="block mt-1 w-full flatpickr-dmy" type="text" placeholder="yyyy-mm-dd" />
                    <x-input-error :messages="$errors->get('other_tanggal_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="other_profile_tempat_lahir" value="Tempat Lahir" />
                    <x-text-input id="other_profile_tempat_lahir" name="other_tempat_lahir" :value="old('other_tempat_lahir', optional($user->otherProfile)->tempat_lahir)"
                        class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('other_tempat_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="other_profile_jenis_kelamin" value="Jenis Kelamin" />
                    <select name="other_jenis_kelamin" id="other_profile_jenis_kelamin"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="laki-laki" @if (old('other_jenis_kelamin', optional($user->otherProfile)->jenis_kelamin) == 'laki-laki') selected @endif>Laki-laki</option>
                        <option value="perempuan" @if (old('other_jenis_kelamin', optional($user->otherProfile)->jenis_kelamin) == 'perempuan') selected @endif>Perempuan</option>
                    </select>
                    <x-input-error :messages="$errors->get('other_jenis_kelamin')" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="other_profile_alamat" value="Alamat" />
                    <textarea id="other_profile_alamat" name="alamat"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('alamat', optional($user->otherProfile)->alamat) }}</textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="other_profile_foto" value="Foto Profil" />
                    <input type="file" id="other_profile_foto" name="other_foto"
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm" />
                    <x-input-error :messages="$errors->get('other_foto')" class="mt-2" />
                </div>
            </div>
        </template>

        <!-- Field Profil Guru -->
        <template x-if="role === 'guru'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="guru_jabatan" value="Jabatan" />
                    <x-text-input id="guru_jabatan" name="guru_jabatan" :value="old('guru_jabatan', optional($user->guruProfile)->jabatan)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('guru_jabatan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="guru_telepon" value="Telepon" />
                    <x-text-input id="guru_telepon" name="guru_telepon" :value="old('guru_telepon', optional($user->guruProfile)->telepon)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('guru_telepon')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="guru_tanggal_lahir" value="Tanggal Lahir" />
                    <x-text-input id="guru_tanggal_lahir" name="guru_tanggal_lahir"
                        value="{{ old('guru_tanggal_lahir', optional($user->guruProfile)->tanggal_lahir ? $user->guruProfile->tanggal_lahir->format('Y-m-d') : '') }}"
                        class="block mt-1 w-full flatpickr-dmy" type="text" placeholder="yyyy-mm-dd" />
                    <x-input-error :messages="$errors->get('guru_tanggal_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="guru_tempat_lahir" value="Tempat Lahir" />
                    <x-text-input id="guru_tempat_lahir" name="guru_tempat_lahir" :value="old('guru_tempat_lahir', optional($user->guruProfile)->tempat_lahir)"
                        class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('guru_tempat_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="guru_jenis_kelamin" value="Jenis Kelamin" />
                    <select name="guru_jenis_kelamin" id="guru_jenis_kelamin"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="laki-laki" @if (old('guru_jenis_kelamin', optional($user->guruProfile)->jenis_kelamin) == 'laki-laki') selected @endif>Laki-laki</option>
                        <option value="perempuan" @if (old('guru_jenis_kelamin', optional($user->guruProfile)->jenis_kelamin) == 'perempuan') selected @endif>Perempuan</option>
                    </select>
                    <x-input-error :messages="$errors->get('guru_jenis_kelamin')" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="alamat" value="Alamat" />
                    <textarea id="alamat" name="alamat"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('alamat', optional($user->guruProfile)->alamat) }}</textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="guru_foto" value="Foto Profil" />
                    <input type="file" id="guru_foto" name="guru_foto"
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm" />
                    <x-input-error :messages="$errors->get('guru_foto')" class="mt-2" />
                </div>
                
            </div>
        </template>

        <!-- Field Profil Siswa -->
        <template x-if="role === 'siswa'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="kelas_id" value="Kelas" />
                    <select name="kelas_id" id="kelas_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Pilih Kelas</option>
                        @foreach ($kelas ?? [] as $k)
                            <option value="{{ $k->id }}" @if (old('kelas_id', optional($user->siswaProfile)->kelas_id) == $k->id) selected @endif>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('kelas_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="siswa_tanggal_lahir" value="Tanggal Lahir" />
                    <x-text-input id="siswa_tanggal_lahir" name="siswa_tanggal_lahir"
                        value="{{ old('siswa_tanggal_lahir', optional($user->siswaProfile)->tanggal_lahir ? $user->siswaProfile->tanggal_lahir->format('Y-m-d') : '') }}"
                        class="block mt-1 w-full flatpickr-dmy" type="text" placeholder="yyyy-mm-dd" />
                    <x-input-error :messages="$errors->get('siswa_tanggal_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="siswa_tempat_lahir" value="Tempat Lahir" />
                    <x-text-input id="siswa_tempat_lahir" name="siswa_tempat_lahir" :value="old('siswa_tempat_lahir', optional($user->siswaProfile)->tempat_lahir)"
                        class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('siswa_tempat_lahir')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="siswa_jenis_kelamin" value="Jenis Kelamin" />
                    <select name="siswa_jenis_kelamin" id="siswa_jenis_kelamin"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="laki-laki" @if (old('siswa_jenis_kelamin', optional($user->siswaProfile)->jenis_kelamin) == 'laki-laki') selected @endif>Laki-laki</option>
                        <option value="perempuan" @if (old('siswa_jenis_kelamin', optional($user->siswaProfile)->jenis_kelamin) == 'perempuan') selected @endif>Perempuan
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('siswa_jenis_kelamin')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="nama_ayah" value="Nama Ayah" />
                    <x-text-input id="nama_ayah" name="nama_ayah" :value="old('nama_ayah', optional($user->siswaProfile)->nama_ayah)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('nama_ayah')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="nama_ibu" value="Nama Ibu" />
                    <x-text-input id="nama_ibu" name="nama_ibu" :value="old('nama_ibu', optional($user->siswaProfile)->nama_ibu)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('nama_ibu')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="telepon_ayah" value="Telepon Ayah" />
                    <x-text-input id="telepon_ayah" name="telepon_ayah" :value="old('telepon_ayah', optional($user->siswaProfile)->telepon_ayah)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('telepon_ayah')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="telepon_ibu" value="Telepon Ibu" />
                    <x-text-input id="telepon_ibu" name="telepon_ibu" :value="old('telepon_ibu', optional($user->siswaProfile)->telepon_ibu)" class="block mt-1 w-full"
                        type="text" />
                    <x-input-error :messages="$errors->get('telepon_ibu')" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="alamat" value="Alamat" />
                    <textarea id="alamat" name="alamat"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('alamat', optional($user->siswaProfile)->alamat) }}</textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="siswa_foto" value="Foto Profil" />
                    <input type="file" id="siswa_foto" name="siswa_foto"
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm" />
                    <x-input-error :messages="$errors->get('siswa_foto')" class="mt-2" />
                </div>
            </div>
        </template>
    </div>
</div>

<div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
    <a href="{{ route('users.index') }}"
        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 me-4">
        Batal
    </a>

    <x-primary-button>
        {{ $user->exists ? 'Update User' : 'Simpan User' }}
    </x-primary-button>
</div>
</div>
