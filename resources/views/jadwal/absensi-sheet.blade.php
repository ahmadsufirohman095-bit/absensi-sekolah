<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Absensi: {{ $jadwal->mataPelajaran->nama_mapel }} - Kelas {{ $jadwal->kelas->nama_kelas }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Tanggal: {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('jadwal.absensi.store', $jadwal->id) }}" method="POST">
                    @csrf
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="flex justify-end mb-6">
                            <x-primary-button type="button" id="tandai-semua-hadir">
                                <i class="fas fa-check-double mr-2"></i>
                                Tandai Semua Hadir
                            </x-primary-button>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-md">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Siswa</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu Absensi</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Kehadiran</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @php
                                        $statuses = ['Hadir', 'Sakit', 'Izin', 'Alpha'];
                                    @endphp
                                    @forelse ($students as $student)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $student->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">NIS: {{ $student->identifier }}</p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                                {{ optional($student->waktu_absensi)->format('H:i') ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center justify-center space-x-4">
                                                    @foreach ($statuses as $status)
                                                        <label class="flex items-center">
                                                            <input type="radio" id="status_{{ $student->id }}_{{ $status }}" name="absensi[{{ $student->id }}][status]" value="{{ strtolower($status) }}" 
                                                                   class="text-indigo-600" 
                                                                   @checked(strtolower($student->status_hari_ini) == strtolower($status))>
                                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ $status }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="text" name="absensi[{{ $student->id }}][keterangan]" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm text-sm" placeholder="Opsional">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                                Tidak ada siswa di kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-primary-button type="submit">
                                Simpan Absensi
                            </x-primary-button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.getElementById('tandai-semua-hadir').addEventListener('click', function() {
            const radios = document.querySelectorAll('input[type="radio"][value="hadir"]');
            radios.forEach(radio => {
                radio.checked = true;
            });
        });
    </script>
    @endpush
</x-app-layout>