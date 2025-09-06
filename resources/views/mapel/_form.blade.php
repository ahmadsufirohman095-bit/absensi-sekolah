@csrf
<div>
    <x-input-label for="kode_mapel" :value="__('Kode Mata Pelajaran')" />
    @if (isset($mataPelajaran) && $mataPelajaran->exists)
        {{-- On Edit Page: Display as text and include a hidden input --}}
        <div class="mt-1 block w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
            <span class="text-gray-700 dark:text-gray-300">{{ $mataPelajaran->kode_mapel }}</span>
        </div>
        <input type="hidden" name="kode_mapel" value="{{ $mataPelajaran->kode_mapel }}">
    @else
        {{-- On Create Page: Display as editable input --}}
        <x-text-input id="kode_mapel" class="block mt-1 w-full" type="text" name="kode_mapel" :value="old('kode_mapel', '')" required autofocus />
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            <i class="fas fa-info-circle mr-1"></i>
            Perhatian: (Kode Mata Pelajaran) ini tidak dapat diubah setelah disimpan.
        </p>
    @endif
    <x-input-error :messages="$errors->get('kode_mapel')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="nama_mapel" :value="__('Nama Mata Pelajaran')" />
    <x-text-input id="nama_mapel" class="block mt-1 w-full" type="text" name="nama_mapel" :value="old('nama_mapel', $mataPelajaran->nama_mapel ?? '')" required />
    <x-input-error :messages="$errors->get('nama_mapel')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="deskripsi" :value="__('Deskripsi (Opsional)')" />
    <textarea id="deskripsi" name="deskripsi" class="block mt-1 w-full border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-md shadow-sm">{{ old('deskripsi', $mataPelajaran->deskripsi ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
</div>

<div x-data="tomSelectManager" class="space-y-6">
    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Manajemen Guru Pengajar</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Pilih satu guru yang akan mengajar mata pelajaran ini.</p>
        
        <div wire:ignore>
            <select name="guru_id" id="guru_id" class="tom-select" placeholder="Ketik untuk mencari guru...">
                <option value="">Pilih Guru</option> {{-- Allow no teacher --}}
                @foreach($allGurus as $guru)
                    <option value="{{ $guru->id }}" @selected(isset($mataPelajaran) && $mataPelajaran->gurus->contains($guru->id))>
                        {{ $guru->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <x-input-error :messages="$errors->get('guru_id')" class="mt-2" />
    </div>

    
</div>

<div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
    <a href="{{ route('mata-pelajaran.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
        <i class="fas fa-arrow-left mr-2"></i> Batal
    </a>
    <button type="submit" class="ms-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
        <i class="fas fa-save mr-2"></i> {{ isset($mataPelajaran) ? 'Update Mata Pelajaran' : 'Simpan Mata Pelajaran' }}
    </button>
</div>
