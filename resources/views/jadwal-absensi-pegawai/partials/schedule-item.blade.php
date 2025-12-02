<div class="schedule-item border p-6 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 relative" x-cloak>
    <button type="button" x-on:click="removeScheduleItem($el.closest('.schedule-item'))"
        class="remove-item-btn absolute top-2 right-2 text-red-500 hover:text-red-700 focus:outline-none"
        title="Hapus Jadwal Ini">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
        </svg>
    </button>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Input Pegawai --}}
        <div>
            <x-input-label for="jadwal_absensi_Pegawai_{{ $index }}_user_id" :value="__('Pegawai')" />
            <select name="jadwal_absensi_Pegawai[{{ $index }}][user_id]" id="jadwal_absensi_Pegawai_{{ $index }}_user_id"
                class="jadwal-absensi-Pegawai-tom-select mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                required>
                <option value="">Pilih Pegawai</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}"
                        {{ old('jadwal_absensi_Pegawai.' . $index . '.user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->profile_type == 'guru' ? 'Guru' : ($user->profile_type == 'tu' ? 'TU' : 'Lainnya') }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('jadwal_absensi_Pegawai.' . $index . '.user_id')" class="mt-2" />
        </div>

        {{-- Input Hari --}}
        <div>
            <x-input-label for="jadwal_absensi_Pegawai_{{ $index }}_hari" :value="__('Hari')" />
            <select name="jadwal_absensi_Pegawai[{{ $index }}][hari]" id="jadwal_absensi_Pegawai_{{ $index }}_hari"
                class="jadwal-absensi-Pegawai-tom-select mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                required>
                <option value="">Pilih Hari</option>
                @foreach ($hariOptions as $hari)
                    <option value="{{ $hari }}"
                        {{ old('jadwal_absensi_Pegawai.' . $index . '.hari') == $hari ? 'selected' : '' }}>{{ $hari }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('jadwal_absensi_Pegawai.' . $index . '.hari')" class="mt-2" />
        </div>

        {{-- Input Jam Mulai --}}
        <div>
            <x-input-label for="jadwal_absensi_Pegawai_{{ $index }}_jam_mulai" :value="__('Jam Mulai')" />
            <x-text-input id="jadwal_absensi_Pegawai_{{ $index }}_jam_mulai" name="jadwal_absensi_Pegawai[{{ $index }}][jam_mulai]" type="text" class="mt-1 block w-full flatpickr-time" :value="old('jadwal_absensi_Pegawai.' . $index . '.jam_mulai')" required />
            <x-input-error :messages="$errors->get('jadwal_absensi_Pegawai.' . $index . '.jam_mulai')" class="mt-2" />
        </div>

        {{-- Input Jam Selesai --}}
        <div>
            <x-input-label for="jadwal_absensi_Pegawai_{{ $index }}_jam_selesai" :value="__('Jam Selesai')" />
            <x-text-input id="jadwal_absensi_Pegawai_{{ $index }}_jam_selesai" name="jadwal_absensi_Pegawai[{{ $index }}][jam_selesai]" type="text" class="mt-1 block w-full flatpickr-time" :value="old('jadwal_absensi_Pegawai.' . $index . '.jam_selesai')" required />
            <x-input-error :messages="$errors->get('jadwal.' . $index . '.jam_selesai')" class="mt-2" />
        </div>

        {{-- Input Keterangan (Opsional) --}}
        <div class="md:col-span-2">
            <x-input-label for="jadwal_absensi_Pegawai_{{ $index }}_keterangan" :value="__('Keterangan (Opsional)')" />
            <textarea id="jadwal_absensi_Pegawai_{{ $index }}_keterangan" name="jadwal_absensi_Pegawai[{{ $index }}][keterangan]" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('jadwal_absensi_Pegawai.' . $index . '.keterangan') }}</textarea>
            <x-input-error :messages="$errors->get('jadwal_absensi_Pegawai.' . $index . '.keterangan')" class="mt-2" />
        </div>
    </div>
</div>
