<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Jadwal Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="tomSelectManager">
                    <x-flash-message type="success" />
                    <x-flash-message type="error" />
                    <h3 class="text-lg font-medium mb-6">Form Edit Jadwal Absensi</h3>

                    <form action="{{ route('jadwal.update', $jadwal->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Input Kelas --}}
                            <div>
                                <x-input-label for="kelas_id" :value="__('Kelas')" />
                                <x-select-input id="kelas_id" name="kelas_id" class="mt-1 block w-full tom-select" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}"
                                            {{ old('kelas_id', $jadwal->kelas_id) == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('kelas_id')" class="mt-2" />
                            </div>

                            {{-- Input Mata Pelajaran --}}
                            <div>
                                <x-input-label for="mata_pelajaran_id" :value="__('Mata Pelajaran')" />
                                <x-select-input id="mata_pelajaran_id" name="mata_pelajaran_id" class="mt-1 block w-full tom-select" required>
                                    <option value="">Pilih Mata Pelajaran</option>
                                    @foreach ($mataPelajaran as $mp)
                                        <option value="{{ $mp->id }}"
                                            {{ old('mata_pelajaran_id', $jadwal->mata_pelajaran_id) == $mp->id ? 'selected' : '' }}>
                                            {{ $mp->nama_mapel }} ({{ $mp->kode_mapel }})
                                        </option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('mata_pelajaran_id')" class="mt-2" />
                            </div>

                            {{-- Input Guru Pengampu --}}
                            <div>
                                <x-input-label for="guru_id" :value="__('Guru Pengampu')" />
                                <x-select-input id="guru_id" name="guru_id" class="mt-1 block w-full tom-select" required>
                                    <option value="">Pilih Guru</option>
                                    @foreach ($gurus as $guru)
                                        <option value="{{ $guru->id }}"
                                            {{ old('guru_id', $jadwal->guru_id) == $guru->id ? 'selected' : '' }}>
                                            {{ $guru->name }} (NIP: {{ $guru->identifier }})
                                        </option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('guru_id')" class="mt-2" />
                            </div>

                            {{-- Input Hari --}}
                            <div>
                                <x-input-label for="hari" :value="__('Hari')" />
                                <x-select-input id="hari" name="hari" class="mt-1 block w-full tom-select" required>
                                    <option value="">Pilih Hari</option>
                                    @foreach ($hariOptions as $hari)
                                        <option value="{{ $hari }}"
                                            {{ old('hari', $jadwal->hari) == $hari ? 'selected' : '' }}>
                                            {{ $hari }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('hari')" class="mt-2" />
                            </div>

                            {{-- Input Jam Mulai --}}
                            <div>
                                <x-input-label for="jam_mulai" :value="__('Jam Mulai')" />
                                <x-text-input id="jam_mulai" class="block mt-1 w-full flatpickr-time" type="text"
                                    name="jam_mulai" :value="old('jam_mulai', \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i'))" required />
                                <x-input-error :messages="$errors->get('jam_mulai')" class="mt-2" />
                            </div>

                            {{-- Input Jam Selesai --}}
                            <div>
                                <x-input-label for="jam_selesai" :value="__('Jam Selesai')" />
                                <x-text-input id="jam_selesai" class="block mt-1 w-full flatpickr-time" type="text"
                                    name="jam_selesai" :value="old('jam_selesai', \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i'))" required />
                                <x-input-error :messages="$errors->get('jam_selesai')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('jadwal.index') }}"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Batal
                            </a>

                            <x-primary-button class="ml-4">
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .dark .ts-control {
            background-color: #1f2937 !important; /* bg-gray-800 */
            border-color: #4b5563 !important; /* border-gray-600 */
        }
        .dark .ts-control input,
        .dark .ts-control .item {
            color: #d1d5db !important; /* text-gray-300 */
        }
        .dark .ts-control .item {
            background-color: #374151 !important; /* bg-gray-700 */
        }
        .dark .ts-dropdown {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
        }
        .dark .ts-dropdown .option {
            color: #d1d5db !important;
        }
        .dark .ts-dropdown .option:hover,
        .dark .ts-dropdown .option.active {
            background-color: #4b5563 !important;
        }
    </style>
    @endpush
</x-app-layout>

