@csrf

{{-- Logika ini akan berjalan saat mode EDIT --}}
@if (isset($kela))
    <div x-data="kelasEditor({
        initialSiswa: {{ $siswaDiKelas->map->only(['id', 'name', 'identifier', 'foto_url'])->toJson() }},
        siswaTanpaKelas: {{ $siswaTanpaKelas->map->only(['id', 'name', 'identifier'])->toJson() }},
        initialMapel: {{ $kela->mataPelajarans->map->only(['id', 'nama_mapel'])->toJson() }},
        allMapel: {{ $allMataPelajarans->map->only(['id', 'nama_mapel'])->toJson() }}
    })">

        {{-- Hidden Inputs untuk menyimpan ID yang akan diubah --}}
        <input type="hidden" name="add_siswa_ids" :value="JSON.stringify(addedSiswaIds)">
        <input type="hidden" name="remove_siswa_ids" :value="JSON.stringify(removedSiswaIds)">
        <input type="hidden" name="mata_pelajaran_ids" :value="JSON.stringify(currentMapel.map(m => m.id))">

        {{-- Form Utama (Nama Kelas & Wali Kelas) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="nama_kelas" :value="__('Nama Kelas')" />
                <x-text-input id="nama_kelas" class="block mt-1 w-full" type="text" name="nama_kelas" :value="old('nama_kelas', $kela->nama_kelas ?? '')" required autofocus />
                <x-input-error :messages="$errors->get('nama_kelas')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="wali_kelas_id" :value="__('Wali Kelas')" />
                <select name="wali_kelas_id" id="wali_kelas_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">-- Tidak ada --</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" @selected(old('wali_kelas_id', $kela->wali_kelas_id ?? '') == $guru->id)>
                            {{ $guru->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('wali_kelas_id')" class="mt-2" />
            </div>
        </div>

        {{-- Bagian Manajemen Siswa --}}
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    Manajemen Siswa (<span x-text="currentSiswa.length"></span>)
                </h3>
                <button @click="isModalOpen = true" type="button" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-indigo-700">
                    + Tambah Siswa
                </button>
            </div>

            <div class="max-h-96 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 space-y-2">
                <p x-show="currentSiswa.length === 0 && removedSiswaForDisplay.length === 0" class="text-gray-500 dark:text-gray-400 text-sm text-center p-4">
                    Belum ada siswa di kelas ini.
                </p>
                <template x-for="siswa in currentSiswa" :key="siswa.id">
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                        <div class="flex items-center">
                            <img class="h-8 w-8 rounded-full object-cover mr-3" :src="siswa.foto_url" :alt="siswa.name">
                            <div>
                                <span x-text="siswa.name" class="font-medium"></span>
                                <span x-text="'(' + siswa.identifier + ')'" class="text-sm text-gray-500"></span>
                            </div>
                        </div>
                        <button @click="stageForRemoval(siswa.id)" type="button" class="text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400">
                            Keluarkan
                        </button>
                    </div>
                </template>
                <template x-for="siswa in removedSiswaForDisplay" :key="siswa.id">
                    <div class="flex items-center justify-between p-2 bg-red-100 dark:bg-red-900/30 rounded-md opacity-60">
                        <div class="flex items-center">
                            <img class="h-8 w-8 rounded-full object-cover mr-3" :src="siswa.foto_url" :alt="siswa.name">
                            <div>
                                <span x-text="siswa.name" class="font-medium line-through"></span>
                                <span x-text="'(' + siswa.identifier + ')'" class="text-sm text-gray-500 line-through"></span>
                            </div>
                        </div>
                        <button @click="undoRemoval(siswa.id)" type="button" class="text-xs font-semibold text-gray-700 hover:text-black dark:text-gray-300 dark:hover:text-white">
                            Urungkan
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Modal Tambah Siswa --}}
        <div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center" x-cloak>
            <div @click.away="isModalOpen = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-2xl">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tambahkan Siswa ke Kelas</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Pilih siswa yang belum memiliki kelas.</p>
                <div wire:ignore>
                    <select id="add_siswa_select" multiple x-ref="addSiswaSelect" placeholder="Ketik untuk mencari siswa..." class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <template x-for="siswa in availableSiswa" :key="siswa.id">
                            <option :value="siswa.id" x-text="siswa.name + ' (' + siswa.identifier + ')'"></option>
                        </template>
                    </select>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button @click="isModalOpen = false" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Batal</button>
                    <button @click="stageForAddition()" type="button" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Tambahkan</button>
                </div>
            </div>
        </div>

        
    </div>

{{-- Logika ini akan berjalan saat mode TAMBAH BARU --}}
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="nama_kelas" :value="__('Nama Kelas')" />
            <x-text-input id="nama_kelas" class="block mt-1 w-full" type="text" name="nama_kelas" :value="old('nama_kelas')" required autofocus />
            <x-input-error :messages="$errors->get('nama_kelas')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="wali_kelas_id" :value="__('Wali Kelas')" />
            <select name="wali_kelas_id" id="wali_kelas_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">-- Tidak ada --</option>
                @foreach($gurus as $guru)
                    <option value="{{ $guru->id }}" @selected(old('wali_kelas_id') == $guru->id)>
                        {{ $guru->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('wali_kelas_id')" class="mt-2" />
        </div>
    </div>
@endif

{{-- Tombol Simpan (berlaku untuk kedua mode) --}}
<div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
    <a href="{{ route('kelas.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
        Batal
    </a>
    <button type="submit" class="ms-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300">
        {{ isset($kela) ? 'Update Kelas' : 'Simpan Kelas' }}
    </button>
</div>

{{-- Skrip hanya akan di-push ke layout jika dalam mode EDIT --}}


