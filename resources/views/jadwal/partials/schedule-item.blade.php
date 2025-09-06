<div class="schedule-item border p-6 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 relative" x-cloak>
    <button type="button" x-on:click="removeScheduleItem($event)"
        class="remove-item-btn absolute top-2 right-2 text-red-500 hover:text-red-700 focus:outline-none"
        title="Hapus Jadwal Ini">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
        </svg>
    </button>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Input Guru Pengampu --}}
        <div>
            <x-input-label for="jadwal_{{ $index }}_guru_id" :value="__('Guru Pengampu')" />
            <select name="jadwal[{{ $index }}][guru_id]" id="jadwal_{{ $index }}_guru_id"
                class="jadwal-tom-select mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                required>
                <option value="">Pilih Guru</option>
                @foreach ($gurus as $guru)
                    <option value="{{ $guru->id }}"
                        {{ old('jadwal.' . $index . '.guru_id') == $guru->id ? 'selected' : '' }}>
                        {{ $guru->name }} (NIP: {{ $guru->identifier }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('jadwal.' . $index . '.guru_id')" class="mt-2" />
        </div>

        {{-- Input Kelas (Multi-select) --}}
        <div>
            <x-input-label for="jadwal_{{ $index }}_kelas_id" :value="__('Kelas (bisa pilih lebih dari satu)')" />
            <select name="jadwal[{{ $index }}][kelas_id][]" id="jadwal_{{ $index }}_kelas_id"
                class="jadwal-tom-select mt-1 block w-full border-gray-300 bg-gray-900 text-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                multiple required>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}"
                        {{ in_array($k->id, old('jadwal.' . $index . '.kelas_id', [])) ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('jadwal.' . $index . '.kelas_id')" class="mt-2" />
        </div>

        {{-- Input Mata Pelajaran (Multi-select) --}}
        <div>
            <x-input-label for="jadwal_{{ $index }}_mata_pelajaran_id" :value="__('Mata Pelajaran (bisa pilih lebih dari satu)')" />
            <select name="jadwal[{{ $index }}][mata_pelajaran_id][]"
                id="jadwal_{{ $index }}_mata_pelajaran_id"
                class="jadwal-tom-select mt-1 block w-full border-gray-300 bg-gray-900 text-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                multiple required>
                @foreach ($mataPelajaran as $mp)
                    <option value="{{ $mp->id }}"
                        {{ in_array($mp->id, old('jadwal.' . $index . '.mata_pelajaran_id', [])) ? 'selected' : '' }}>
                        {{ $mp->nama_mapel }} ({{ $mp->kode_mapel }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('jadwal.' . $index . '.mata_pelajaran_id')" class="mt-2" />
        </div>

        {{-- Input Hari --}}
        <div>
            <x-input-label for="jadwal_{{ $index }}_hari" :value="__('Hari')" />
            <select name="jadwal[{{ $index }}][hari]" id="jadwal_{{ $index }}_hari"
                class="jadwal-tom-select mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                required>
                <option value="">Pilih Hari</option>
                @foreach ($hariOptions as $hari)
                    <option value="{{ $hari }}"
                        {{ old('jadwal.' . $index . '.hari') == $hari ? 'selected' : '' }}>{{ $hari }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('jadwal.' . $index . '.hari')" class="mt-2" />
        </div>

        {{-- Input Jam Mulai --}}
        <div>
            <x-input-label for="jadwal_{{ $index }}_jam_mulai" :value="__('Jam Mulai')" />
            <x-text-input id="jadwal_{{ $index }}_jam_mulai" name="jadwal[{{ $index }}][jam_mulai]" type="text" class="mt-1 block w-full flatpickr-time" :value="old('jadwal.' . $index . '.jam_mulai')" required />
            <x-input-error :messages="$errors->get('jadwal.' . $index . '.jam_mulai')" class="mt-2" />
        </div>

        {{-- Input Jam Selesai --}}
        <div>
            <x-input-label for="jadwal_{{ $index }}_jam_selesai" :value="__('Jam Selesai')" />
            <x-text-input id="jadwal_{{ $index }}_jam_selesai" name="jadwal[{{ $index }}][jam_selesai]" type="text" class="mt-1 block w-full flatpickr-time" :value="old('jadwal.' . $index . '.jam_selesai')" required />
            <x-input-error :messages="$errors->get('jadwal.' . $index . '.jam_selesai')" class="mt-2" />
        </div>
    </div>
</div>
