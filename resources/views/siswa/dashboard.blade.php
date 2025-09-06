<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dasbor Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{}" x-init="initStudentDashboardCharts()">
                    <h3 class="text-2xl font-bold mb-4">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="mb-6">Ini adalah ringkasan dan riwayat absensi Anda.</p>

                    <!-- Bagian Grafik -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-md">
                            <h4 class="text-lg font-semibold mb-2 text-center">Rekap Absensi Bulan Ini</h4>
                            <canvas id="rekapBulanIniChart"></canvas>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-md">
                            <h4 class="text-lg font-semibold mb-2 text-center">Rekap Absensi per Mata Pelajaran</h4>
                            <canvas id="rekapMapelChart"></canvas>
                        </div>
                    </div>

                    <!-- Bagian Riwayat Absensi -->
                    <div>
                        <h4 class="text-xl font-semibold mb-4">Riwayat Absensi Terbaru</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Tanggal</th>
                                        <th class="px-4 py-2 text-left">Waktu Masuk</th>
                                        <th class="px-4 py-2 text-left">Mata Pelajaran</th>
                                        <th class="px-4 py-2 text-left">Guru</th>
                                        <th class="px-4 py-2 text-left">Status</th>
                                        <th class="px-4 py-2 text-left">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($riwayatTerbaru as $absensi)
                                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($absensi->tanggal_absensi)->translatedFormat('d F Y') }}</td>
                                            <td class="px-4 py-2">{{ $absensi->waktu_masuk ? \Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') : '-' }}</td>
                                            <td class="px-4 py-2">{{ $absensi->jadwalAbsensi->mataPelajaran->nama_mapel ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $absensi->jadwalAbsensi->guru->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @switch($absensi->status)
                                                        @case('hadir') bg-green-100 text-green-800 @break
                                                        @case('terlambat') bg-yellow-100 text-yellow-800 @break
                                                        @case('sakit') bg-blue-100 text-blue-800 @break
                                                        @case('izin') bg-indigo-100 text-indigo-800 @break
                                                        @case('alpha') bg-red-100 text-red-800 @break
                                                    @endswitch">
                                                    {{ ucfirst($absensi->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2">{{ $absensi->keterangan ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">Tidak ada riwayat absensi ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{ route('siswa.laporan_absensi') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                Lihat Semua Laporan <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initStudentDashboardCharts() {
            fetch('{{ route("siswa.dashboard.chart-data") }}')
                .then(response => response.json())
                .then(data => {
                    // Pie Chart - Rekap Bulan Ini
                    if (data.rekapBulanIni && data.rekapBulanIni.data.length > 0) {
                        const rekapBulanIniCtx = document.getElementById('rekapBulanIniChart').getContext('2d');
                        new Chart(rekapBulanIniCtx, {
                            type: 'pie',
                            data: {
                                labels: data.rekapBulanIni.labels,
                                datasets: [{
                                    label: 'Rekap Bulan Ini',
                                    data: data.rekapBulanIni.data,
                                    backgroundColor: [
                                        'rgba(75, 192, 192, 0.7)', // Hadir
                                        'rgba(255, 205, 86, 0.7)', // Terlambat
                                        'rgba(54, 162, 235, 0.7)', // Izin
                                        'rgba(153, 102, 255, 0.7)', // Sakit
                                        'rgba(255, 99, 132, 0.7)',  // Alpha
                                    ],
                                    borderColor: 'rgba(255, 255, 255, 0.5)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    }
                                }
                            }
                        });
                    }

                    // Bar Chart - Rekap per Mata Pelajaran
                    if (data.rekapPerMapel && data.rekapPerMapel.data.length > 0) {
                        const rekapMapelCtx = document.getElementById('rekapMapelChart').getContext('2d');
                        new Chart(rekapMapelCtx, {
                            type: 'bar',
                            data: {
                                labels: data.rekapPerMapel.labels,
                                datasets: [{
                                    label: 'Total Kehadiran per Mapel',
                                    data: data.rekapPerMapel.data,
                                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                                    borderColor: 'rgba(153, 102, 255, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                }
                            }
                        });
                    }
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }
    </script>
    @endpush
</x-app-layout>
