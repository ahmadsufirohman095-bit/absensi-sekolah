<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Absensi Manual Pegawai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-6">Form Tambah Absensi Pegawai</h3>

                    <form action="{{ route('rekap_absensi_pegawai.store') }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="space-y-6" x-data="tomSelectManager">
                            
                            <!-- Pegawai Selection -->
                            <div>
                                <x-input-label for="user_id" :value="__('Nama Pegawai')" />
                                <select id="user_id" name="user_id" class="mt-1 block w-full" required>
                                    <option value="">Pilih Pegawai</option>
                                    @foreach($allUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} (Role: {{ ucfirst($user->role) }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>

                            <!-- Jadwal Absensi Pegawai Selection -->
                            <div>
                                <x-input-label for="jadwal_absensi_pegawai_id" :value="__('Jadwal Absensi Pegawai (Opsional)')" />
                                <select id="jadwal_absensi_pegawai_id" name="jadwal_absensi_pegawai_id" class="mt-1 block w-full">
                                    <option value="">Tidak Terkait Jadwal</option>
                                    @foreach($allJadwalPegawai as $jadwalPegawai)
                                        <option value="{{ $jadwalPegawai->id }}" {{ old('jadwal_absensi_pegawai_id') == $jadwalPegawai->id ? 'selected' : '' }}>
                                            {{ $jadwalPegawai->user?->name }} ({{ $jadwalPegawai->hari }}, {{ $jadwalPegawai->jam_mulai->format('H:i') }} - {{ $jadwalPegawai->jam_selesai->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('jadwal_absensi_pegawai_id')" class="mt-2" />
                            </div>

                            <!-- Tanggal Absensi -->
                            <div>
                                <x-input-label for="tanggal_absensi" :value="__('Tanggal Absensi')" />
                                <x-text-input id="tanggal_absensi" name="tanggal_absensi" type="text" class="mt-1 block w-full flatpickr-date" value="{{ old('tanggal_absensi', date('Y-m-d')) }}" required />
                                <x-input-error :messages="$errors->get('tanggal_absensi')" class="mt-2" />
                            </div>

                            <!-- Status Kehadiran -->
                            <div>
                                <x-input-label for="status" :value="__('Status Kehadiran')" />
                                <x-select-input id="status" name="status" class="mt-1 block w-full" required>
                                    @foreach($statusOptions as $status)
                                        <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Waktu Absensi -->
                            <div>
                                <x-input-label for="waktu_masuk" :value="__('Waktu Absensi (Opsional)')" />
                                <x-text-input id="waktu_masuk" name="waktu_masuk" type="text" class="mt-1 block w-full flatpickr-time" value="{{ old('waktu_masuk') }}" />
                                <x-input-error :messages="$errors->get('waktu_masuk')" class="mt-2" />
                            </div>

                            <!-- Keterangan -->
                            <div>
                                <x-input-label for="keterangan" :value="__('Keterangan (Opsional)')" />
                                <x-text-input id="keterangan" name="keterangan" type="text" class="mt-1 block w-full" value="{{ old('keterangan') }}" />
                                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('rekap_absensi_pegawai.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Batal
                            </a>

                            <x-primary-button class="ml-4">
                                {{ __('Simpan Absensi Pegawai') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('turbo:load', function() {
            // Inisialisasi Flatpickr untuk tanggal
            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d",
                allowInput: true,
            });

            // Inisialisasi Flatpickr untuk waktu
            flatpickr(".flatpickr-time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
        });
    </script>
    @endpush
</x-app-layout>
