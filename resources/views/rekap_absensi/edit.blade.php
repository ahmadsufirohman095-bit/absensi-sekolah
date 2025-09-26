<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Data Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div x-data="rekapAbsensiEditManager()" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-6">Form Edit Absensi</h3>

                    <form action="{{ route('rekap_absensi.update', $absensi->id) }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="student_name" :value="__('Nama Siswa')" />
                                <x-text-input id="student_name" type="text" class="mt-1 block w-full" value="{{ $absensi->user->name ?? 'User Dihapus' }}" disabled />
                            </div>

                            <div>
                                <x-input-label for="tanggal_absensi" :value="__('Tanggal Absensi')" />
                                <x-text-input id="tanggal_absensi" type="text" class="mt-1 block w-full" value="{{ $absensi->tanggal_absensi->format('d M Y') }}" disabled />
                            </div>

                            <div x-data="tomSelectManager">
                                <x-input-label for="status" :value="__('Status Kehadiran')" />
                                <x-select-input id="status" name="status" class="mt-1 block w-full" required>
                                    @foreach($statusOptions as $status)
                                        <option value="{{ $status }}" {{ old('status', $absensi->status) == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="waktu_masuk" :value="__('Waktu Absensi')" />
                                <x-text-input id="waktu_masuk" name="waktu_masuk" type="text" class="mt-1 block w-full flatpickr-time" value="{{ old('waktu_masuk', $absensi->waktu_masuk ? $absensi->waktu_masuk->format('H:i') : '') }}" />
                                <x-input-error :messages="$errors->get('waktu_masuk')" class="mt-2" />
                            </div>

                            <div x-data="tomSelectManager">
                                <x-input-label for="attendance_type" :value="__('Tipe Absensi')" />
                                <x-select-input id="attendance_type" name="attendance_type" class="mt-1 block w-full" required>
                                    @foreach($attendanceTypeOptions as $type)
                                        <option value="{{ $type }}" {{ old('attendance_type', $absensi->attendance_type) == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('attendance_type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="keterangan" :value="__('Keterangan')" />
                                <x-text-input id="keterangan" name="keterangan" type="text" class="mt-1 block w-full" value="{{ old('keterangan', $absensi->keterangan) }}" />
                                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('rekap_absensi.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Batal
                            </a>

                            <x-primary-button class="ml-4">
                                {{ __('Update Data Absensi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function rekapAbsensiEditManager() {
            return {
                flatpickrInstance: null,

                init() {
                    // Initialize Flatpickr for time input
                    this.flatpickrInstance = flatpickr(this.$el.querySelector('.flatpickr-time'), {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",
                        time_24hr: true
                    });
                },

                destroy() {
                    // Destroy the Flatpickr instance
                    if (this.flatpickrInstance) {
                        this.flatpickrInstance.destroy();
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
