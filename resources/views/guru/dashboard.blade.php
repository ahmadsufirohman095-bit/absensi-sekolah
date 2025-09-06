<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __($kelasDiampu ? 'Dashboard Wali Kelas' : 'Dashboard Guru') }}
            </h2>
            <div class="flex items-center space-x-2">
                <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white rounded-xl shadow-lg p-8 mb-8 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-repeat bg-center opacity-10" style="background-image: url('{{ asset('images/pattern.svg') }}');"></div>
                <div class="relative z-10">
                    <h3 class="text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    @if($kelasDiampu)
                        <p class="text-indigo-100">Anda adalah wali kelas untuk kelas <span class="font-semibold text-white">{{ $kelasDiampu->nama_kelas }}</span>. Berikut ringkasan aktivitas kelas Anda.</p>
                    @else
                        <p class="text-indigo-100">Berikut adalah ringkasan aktivitas dan jadwal mengajar Anda.</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 @if($kelasDiampu) lg:grid-cols-3 @endif gap-8">
                
                <!-- Kolom Utama -->
                <div class="{{ $kelasDiampu ? 'lg:col-span-2' : 'lg:col-span-3' }} space-y-8">
                    <!-- Jadwal Mengajar Hari Ini -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-5 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Jadwal Mengajar Hari Ini
                            </h4>
                            <div class="space-y-4">
                                @forelse($jadwalMengajar as $jadwal)
                                    <x-guru.jadwal-card :jadwal="$jadwal" />
                                @empty
                                    <div class="text-center p-8 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                          <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200">Tidak Ada Jadwal Mengajar</h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Nikmati waktu istirahat Anda.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Chart Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center"><svg class="h-6 w-6 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg> Ringkasan Mengajar</h3>
                            <div class="h-60"><canvas id="teachingSummaryChart"></canvas></div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center"><svg class="h-6 w-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg> Absensi Bulanan</h3>
                            <div class="h-60"><canvas id="monthlyAbsenceChartGuru"></canvas></div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Samping (Hanya untuk Wali Kelas) -->
                @if($kelasDiampu)
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Rekap Kehadiran Kelas
                        </h3>
                        <div class="h-64"><canvas id="rekapKehadiranChart"></canvas></div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Siswa Belum Hadir
                                </h4>
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">{{ $siswaAbsen->count() }}</span>
                            </div>
                            <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                                @forelse($siswaAbsen as $siswa)
                                    <x-guru.siswa-absen-item :siswa="$siswa" />
                                @empty
                                    <div class="text-center p-6 bg-green-50 dark:bg-green-900/30 rounded-lg">
                                        <svg class="mx-auto h-10 w-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="mt-2 font-semibold text-green-700 dark:text-green-300">Luar biasa!</p>
                                        <p class="text-sm text-green-600 dark:text-green-400">Semua siswa di kelas Anda sudah hadir.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('turbo:load', function () {
        const chartCanvasIds = ['teachingSummaryChart', 'rekapKehadiranChart', 'monthlyAbsenceChartGuru'];

        const destroyExistingChart = (canvasId) => {
            const chartInstance = Chart.getChart(canvasId);
            if (chartInstance) {
                chartInstance.destroy();
            }
        };

        chartCanvasIds.forEach(destroyExistingChart);

        const createNewChart = (canvasId, type, chartData, options) => {
            const ctx = document.getElementById(canvasId)?.getContext('2d');
            if (ctx) {
                new Chart(ctx, { type, data: chartData, options });
            }
        };

        fetch('{{ route('guru.dashboard.chart-data') }}')
            .then(response => response.ok ? response.json() : Promise.reject('Network response was not ok'))
            .then(data => {
                if (data.error) {
                    console.error('Error from server:', data.error, data.details);
                    return;
                }

                const chartFont = { family: 'Inter, sans-serif' };
                const defaultOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { font: chartFont, color: document.body.classList.contains('dark') ? '#cbd5e1' : '#4b5563' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { font: chartFont, color: document.body.classList.contains('dark') ? '#9ca3af' : '#6b7280' },
                            grid: { color: document.body.classList.contains('dark') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            ticks: { font: chartFont, color: document.body.classList.contains('dark') ? '#9ca3af' : '#6b7280' },
                            grid: { display: false }
                        }
                    }
                };

                // --- Chart: Ringkasan Mengajar ---
                createNewChart('teachingSummaryChart', 'bar', {
                    labels: data.teachingSummary.labels,
                    datasets: [{
                        label: 'Jumlah Jam',
                        data: data.teachingSummary.data,
                        backgroundColor: 'rgba(167, 139, 250, 0.5)',
                        borderColor: 'rgba(139, 92, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 4,
                    }]
                }, { ...defaultOptions, plugins: { ...defaultOptions.plugins, legend: { display: false } } });

                // --- Chart: Rekap Kehadiran (Wali Kelas) ---
                if (data.rekapKehadiran) {
                    createNewChart('rekapKehadiranChart', 'doughnut', {
                        labels: data.rekapKehadiran.labels,
                        datasets: [{
                            data: data.rekapKehadiran.data,
                            backgroundColor: ['rgba(52, 211, 153, 0.5)', 'rgba(248, 113, 113, 0.5)', 'rgba(251, 191, 36, 0.5)'],
                            borderColor: ['#10B981', '#EF4444', '#F59E0B'],
                            borderWidth: 2,
                        }]
                    }, { ...defaultOptions, plugins: { ...defaultOptions.plugins, legend: { position: 'bottom' } }, scales: {} });
                }

                // --- Chart: Absensi Bulanan ---
                createNewChart('monthlyAbsenceChartGuru', 'line', {
                    labels: data.monthlyAbsence.labels,
                    datasets: [{
                        label: 'Total Absensi',
                        data: data.monthlyAbsence.data,
                        backgroundColor: 'rgba(99, 102, 241, 0.2)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                        pointRadius: 4,
                        tension: 0.4,
                        fill: true,
                    }]
                }, { ...defaultOptions, plugins: { ...defaultOptions.plugins, legend: { display: false } } });
            })
            .catch(error => {
                console.error('Chart rendering failed:', error);
            });
    });
    </script>
    @endpush
</x-app-layout>
