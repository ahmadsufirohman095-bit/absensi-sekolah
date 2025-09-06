<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Laporan Absensi Saya') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ startDate: '{{ request('start_date', '') }}', endDate: '{{ request('end_date', '') }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Filter Tanggal -->
                    <div x-data="{ showFilters: true }" x-init="flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd F Y', theme: document.documentElement.classList.contains('dark') ? 'dark' : 'default' });" class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg mb-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Filter & Ekspor</h4>
                            <button @click="showFilters = !showFilters" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 focus:outline-none">
                                <span x-show="!showFilters"><i class="fa-solid fa-chevron-down"></i> Tampilkan</span>
                                <span x-show="showFilters" x-cloak><i class="fa-solid fa-chevron-up"></i> Sembunyikan</span>
                            </button>
                        </div>
                        <div x-show="showFilters" x-collapse>
                            <form method="GET" action="{{ route('siswa.laporan_absensi') }}" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                                        <x-text-input id="start_date" name="start_date" type="text" class="mt-1 block w-full flatpickr-date" x-model="startDate" />
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Akhir</label>
                                        <x-text-input id="end_date" name="end_date" type="text" class="mt-1 block w-full flatpickr-date" x-model="endDate" />
                                    </div>
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <a :href="`{{ route('siswa.laporan_absensi.export') }}?start_date=${startDate}&end_date=${endDate}`" 
                                       data-turbo="false"
                                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-500 dark:hover:bg-green-600">
                                        <i class="fas fa-file-excel mr-2"></i>
                                        Ekspor Excel
                                    </a>
                                    <a href="{{ route('siswa.laporan_absensi') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-500 dark:hover:bg-gray-600">
                                        <i class="fas fa-sync-alt mr-2"></i> Reset
                                    </a>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                        <i class="fas fa-filter mr-2"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Ringkasan Absensi -->
                    <h3 class="text-lg font-semibold mb-4">Ringkasan Kehadiran</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8 text-center">
                        <div class="p-4 bg-green-600 text-white rounded-lg flex flex-col items-center justify-center">
                            <i class="fa-solid fa-check-circle text-3xl mb-2"></i>
                            <p class="text-sm">Hadir</p>
                            <p class="text-2xl font-bold">{{ $summary['hadir'] ?? 0 }}</p>
                        </div>
                        <div class="p-4 bg-yellow-600 text-white rounded-lg flex flex-col items-center justify-center">
                            <i class="fa-solid fa-hourglass-half text-3xl mb-2"></i>
                            <p class="text-sm">Terlambat</p>
                            <p class="text-2xl font-bold">{{ $summary['terlambat'] ?? 0 }}</p>
                        </div>
                        <div class="p-4 bg-orange-600 text-white rounded-lg flex flex-col items-center justify-center">
                            <i class="fa-solid fa-hospital text-3xl mb-2"></i>
                            <p class="text-sm">Sakit</p>
                            <p class="text-2xl font-bold">{{ $summary['sakit'] ?? 0 }}</p>
                        </div>
                        <div class="p-4 bg-blue-600 text-white rounded-lg flex flex-col items-center justify-center">
                            <i class="fa-solid fa-user-tag text-3xl mb-2"></i>
                            <p class="text-sm">Izin</p>
                            <p class="text-2xl font-bold">{{ $summary['izin'] ?? 0 }}</p>
                        </div>
                        <div class="p-4 bg-red-600 text-white rounded-lg flex flex-col items-center justify-center">
                            <i class="fa-solid fa-times-circle text-3xl mb-2"></i>
                            <p class="text-sm">Alpha</p>
                            <p class="text-2xl font-bold">{{ $summary['alpha'] ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- Riwayat Absensi -->
                    <h3 class="text-lg font-semibold mb-4">Riwayat Absensi</h3>
                    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Siswa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kelas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Guru</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Keterangan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipe</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($absensiSiswa as $absensi)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($absensi->tanggal_absensi)->translatedFormat('d F Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $absensi->waktu_masuk ? \Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $absensi->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $absensi->jadwalAbsensi->kelas->nama_kelas ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $absensi->jadwalAbsensi->mataPelajaran->nama_mapel }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $absensi->jadwalAbsensi->guru->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @switch($absensi->status)
                                                    @case('hadir') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 @break
                                                    @case('terlambat') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 @break
                                                    @case('sakit') bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 @break
                                                    @case('izin') bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100 @break
                                                    @case('alpha') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 @break
                                                @endswitch">
                                                {{ ucfirst($absensi->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $absensi->keterangan ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $absensi->attendance_type ?? 'N/A')) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center justify-center py-10">
                                                <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                                                <p class="font-semibold">Tidak ada data absensi untuk ditampilkan.</p>
                                                <p class="text-sm">Coba ubah rentang tanggal atau periksa kembali nanti.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $absensiSiswa->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
