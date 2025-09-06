<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Catat Izin / Sakit Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <form method="POST" action="{{ route('izinsakit.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">

                        {{-- Menampilkan pesan error validasi atau session --}}
                        <x-flash-message type="success" />
                        <x-flash-message type="error" />
                        @if ($errors->any())
                            <div
                                class="p-4 mb-4 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-lg shadow-md">
                                <p class="font-bold">Terjadi kesalahan:</p>
                                <ul class="list-disc list-inside mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Input Pencarian Siswa menggunakan TomSelect --}}
                        <div>
                            <x-input-label for="user_id" :value="__('Pilih Siswa (Ketik Nama atau NIS)')" />
                            {{-- Tambahkan kelas 'tom-select' agar otomatis diinisialisasi --}}
                            <select name="user_id" id="user_id" class="tom-select mt-1" required
                                placeholder="Cari siswa...">
                                <option value="">-- Pilih Siswa --</option>
                                @foreach ($siswaList as $siswa)
                                    <option value="{{ $siswa->id }}" @selected(old('user_id') == $siswa->id)>
                                        {{ $siswa->name }} ({{ $siswa->identifier }} -
                                        {{ $siswa->siswaProfile->kelas->nama_kelas ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Form Detail Izin/Sakit --}}
                        <div>
                            <x-input-label for="tanggal" :value="__('Tanggal Izin/Sakit')" />
                            <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal"
                                :value="old('tanggal', now()->format('Y-m-d'))" required />
                        </div>
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select name="status" id="status"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                                required>
                                <option value="sakit" @selected(old('status', 'sakit') == 'sakit')>Sakit</option>
                                <option value="izin" @selected(old('status') == 'izin')>Izin</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="keterangan" :value="__('Keterangan (Opsional)')" />
                            <textarea id="keterangan" name="keterangan"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('keterangan') }}</textarea>
                        </div>
                        <div>
                            <x-input-label for="bukti_absensi" :value="__('Unggah Bukti (Opsional, maks 2MB)')" />
                            <input type="file" name="bukti_absensi" id="bukti_absensi"
                                class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div
                        class="flex items-center justify-end p-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('rekap_absensi.index') }}"
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 mr-4">
                            Batal
                        </a>
                        <x-primary-button>
                            {{ __('Simpan Catatan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
