<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Semua Jadwal Mengajar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Semua Jadwal Mengajar Anda</h3>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('guru.jadwal-mengajar.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-calendar-day mr-2"></i> Jadwal Hari Ini
                            </a>
                            <a href="{{ route('guru.jadwal-mengajar.export') }}" id="exportExcelBtn" data-turbo="false" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-file-excel mr-2"></i> Ekspor Excel
                            </a>
                        </div>
                    </div>
                    <div class="space-y-6">
                        @forelse ($jadwalPelajaran as $hari => $jadwals)
                            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md">
                                <h4 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-3 pb-2 border-b border-gray-300 dark:border-gray-600">{{ $hari }}</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-fixed">
                                        <thead class="bg-gray-100 dark:bg-gray-800">
                                            <tr>
                                                <th class="w-1/3 px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Mata Pelajaran</th>
                                                <th class="w-1/3 px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Kelas</th>
                                                <th class="w-1/3 px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-800">
                                            @foreach ($jadwals->sortBy('jam_mulai') as $jadwal)
                                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $jadwal->kelas->nama_kelas }}</td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-700 dark:text-gray-300">Anda belum memiliki jadwal mengajar.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
