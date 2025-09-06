<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Perbarui informasi profil dan alamat email akun Anda.") }}
        </p>
    </header>

    <!-- Avatar Profil -->
    <div class="mt-6 flex items-center gap-4">
        <img class="h-24 w-24 rounded-full object-cover" src="{{ Auth::user()->foto_url }}" alt="{{ Auth::user()->name }}">
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div x-data="{ role: '{{ $user->role }}' }" x-init="
            $nextTick(() => { 
                if (typeof initializeFlatpickrDMY === 'function') {
                    initializeFlatpickrDMY();
                }
            });
        ">

            <!-- Data User Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="username" :value="__('Username')" />
                    <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username', $user->username)" required />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>
                <div>
                    <label for="identifier" class="block font-medium text-sm text-gray-700 dark:text-gray-300"
                        x-text="role === 'siswa' ? 'NIS' : (role === 'guru' ? 'NIP' : 'Identifier')"></label>
                    <input id="identifier"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-200 dark:text-gray-500 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        type="text"
                        value="{{ $user->identifier }}"
                        readonly>
                </div>
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="role" :value="__('Role')" />
                    <x-text-input id="role" class="block mt-1 w-full bg-gray-200 dark:text-gray-500" type="text" :value="ucfirst($user->role)" readonly />
                </div>
                 <div>
                    <x-input-label for="foto" value="Ganti Foto Profil" />
                    <input type="file" id="foto" name="foto"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 file:hover:bg-gray-300 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm" />
                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
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
                            <x-text-input id="admin_jabatan" name="jabatan" :value="old('jabatan', $user->profile?->jabatan)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="admin_telepon" value="Telepon" />
                            <x-text-input id="admin_telepon" name="telepon" :value="old('telepon', $user->profile?->telepon)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('telepon')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="admin_tempat_lahir" value="Tempat Lahir" />
                            <x-text-input id="admin_tempat_lahir" name="tempat_lahir" :value="old('tempat_lahir', $user->profile?->tempat_lahir)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="admin_jenis_kelamin" value="Jenis Kelamin" />
                            <select name="jenis_kelamin" id="admin_jenis_kelamin" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="laki-laki" @selected(old('jenis_kelamin', $user->profile?->jenis_kelamin) == 'laki-laki')>Laki-laki</option>
                                <option value="perempuan" @selected(old('jenis_kelamin', $user->profile?->jenis_kelamin) == 'perempuan')>Perempuan</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                        </div>
                    </div>
                </template>

                <!-- Field Profil Guru -->
                <template x-if="role === 'guru'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="guru_jabatan" value="Jabatan" />
                            <x-text-input id="guru_jabatan" name="jabatan" :value="old('jabatan', $user->profile?->jabatan)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="guru_telepon" value="Telepon" />
                            <x-text-input id="guru_telepon" name="telepon" :value="old('telepon', $user->profile?->telepon)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('telepon')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="guru_tanggal_lahir" value="Tanggal Lahir" />
                            <x-text-input id="guru_tanggal_lahir" name="tanggal_lahir" :value="old('tanggal_lahir', $user->profile?->tanggal_lahir?->format('Y-m-d') ?? '')" class="block mt-1 w-full flatpickr-dmy" type="text" placeholder="yyyy-mm-dd" />
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="tempat_lahir" value="Tempat Lahir" />
                            <x-text-input id="tempat_lahir" name="tempat_lahir" :value="old('tempat_lahir', $user->profile?->tempat_lahir)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="jenis_kelamin" value="Jenis Kelamin" />
                            <select name="jenis_kelamin" id="jenis_kelamin" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="laki-laki" @selected(old('jenis_kelamin', $user->profile?->jenis_kelamin) == 'laki-laki')>Laki-laki</option>
                                <option value="perempuan" @selected(old('jenis_kelamin', $user->profile?->jenis_kelamin) == 'perempuan')>Perempuan</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="alamat" value="Alamat" />
                            <textarea id="alamat" name="alamat" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('alamat', $user->profile?->alamat) }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>
                    </div>
                </template>

                <!-- Field Profil Siswa -->
                <template x-if="role === 'siswa'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="kelas_id" value="Kelas" />
                             <x-text-input id="kelas_id" class="block mt-1 w-full bg-gray-200 dark:text-gray-500" type="text" :value="$user->profile?->kelas?->nama_kelas" readonly />
                        </div>
                        <div>
                            <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
                            <x-text-input id="tanggal_lahir" name="tanggal_lahir" :value="old('tanggal_lahir', $user->profile?->tanggal_lahir?->format('Y-m-d') ?? '')" class="block mt-1 w-full flatpickr-dmy" type="text" placeholder="yyyy-mm-dd" />
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="tempat_lahir" value="Tempat Lahir" />
                            <x-text-input id="tempat_lahir" name="tempat_lahir" :value="old('tempat_lahir', $user->profile?->tempat_lahir)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="jenis_kelamin" value="Jenis Kelamin" />
                            <select name="jenis_kelamin" id="jenis_kelamin" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="laki-laki" @selected(old('jenis_kelamin', $user->profile?->jenis_kelamin) == 'laki-laki')>Laki-laki</option>
                                <option value="perempuan" @selected(old('jenis_kelamin', $user->profile?->jenis_kelamin) == 'perempuan')>Perempuan</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nama_ayah" value="Nama Ayah" />
                            <x-text-input id="nama_ayah" name="nama_ayah" :value="old('nama_ayah', $user->profile?->nama_ayah)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('nama_ayah')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nama_ibu" value="Nama Ibu" />
                            <x-text-input id="nama_ibu" name="nama_ibu" :value="old('nama_ibu', $user->profile?->nama_ibu)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('nama_ibu')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="telepon_ayah" value="Telepon Ayah" />
                            <x-text-input id="telepon_ayah" name="telepon_ayah" :value="old('telepon_ayah', $user->profile?->telepon_ayah)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('telepon_ayah')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="telepon_ibu" value="Telepon Ibu" />
                            <x-text-input id="telepon_ibu" name="telepon_ibu" :value="old('telepon_ibu', $user->profile?->telepon_ibu)" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('telepon_ibu')" class="mt-2" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="alamat" value="Alamat" />
                            <textarea id="alamat" name="alamat" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('alamat', $user->profile?->alamat) }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center gap-4 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
