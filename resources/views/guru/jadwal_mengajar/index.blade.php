<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Jadwal Mengajar & Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ globalMessage: '', globalMessageType: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">
                        Kelola Absensi Pertemuan Mengajar
                    </h3>

                    <div x-show="globalMessage" :class="{ 
                            'bg-green-100 border-green-400 text-green-700 dark:bg-green-900/20 dark:border-green-700 dark:text-green-200': globalMessageType === 'success', 
                            'bg-red-100 border-red-400 text-red-700 dark:bg-red-900/20 dark:border-red-700 dark:text-red-200': globalMessageType === 'error' 
                        }"
                        class="border px-4 py-3 rounded relative mb-4" role="alert" x-cloak>
                        <span class="block sm:inline" x-text="globalMessage"></span>
                    </div>

                    <div class="mb-6">
                        <form id="dateFilterForm" method="GET" action="{{ route('guru.jadwal-mengajar.index') }}" class="flex items-center space-x-4">
                            <label for="filter_date" class="text-gray-700 dark:text-gray-300 font-medium">Pilih Tanggal:</label>
                            <input type="date" id="filter_date" name="date" value="{{ $selectedDate }}"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Tampilkan
                            </button>
                            <a href="{{ route('guru.jadwal-mengajar.export', ['date' => $selectedDate]) }}" data-turbo="false" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="fa-solid fa-file-excel mr-2"></i> Ekspor Jadwal
                            </a>
                        </form>
                    </div>

                    @if ($groupedJadwal->isEmpty())
                        <div class="text-center p-8 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-800 dark:text-gray-200">Tidak Ada Jadwal Mengajar</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Anda belum memiliki jadwal mengajar yang terdaftar.</p>
                        </div>
                    @else
                        <div class="space-y-8">
                            @foreach ($groupedJadwal as $hari => $jadwalHariIni)
                                <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                                    <h4 class="text-xl font-bold text-indigo-700 dark:text-indigo-400 mb-4">{{ $hari }}</h4>
                                    <div class="space-y-4">
                                        @foreach ($jadwalHariIni as $jadwal)
                                            <div x-data="{ 
                                                    showStudents: false, 
                                                    selectedStatus: '{{ $siswa->absensi_status ?? '' }}',
                                                    waktuMasuk: '{{ $siswa->absensi_waktu_masuk ?? '' }}',
                                                    updateWaktuMasuk() {
                                                        if (this.selectedStatus === 'hadir' || this.selectedStatus === 'terlambat') {
                                                            // Set current time if status is hadir or terlambat and waktuMasuk is empty
                                                            if (!this.waktuMasuk) {
                                                                const now = new Date();
                                                                this.waktuMasuk = now.toTimeString().slice(0, 5);
                                                            }
                                                        } else {
                                                            this.waktuMasuk = ''; // Clear waktuMasuk for other statuses
                                                        }
                                                    }
                                                }"
                                                x-bind:class="{
                                                    'bg-gray-100 dark:bg-gray-700/30': '{{ $jadwal->status_pertemuan }}' == 'berlalu',
                                                    'bg-yellow-50 dark:bg-yellow-900/30': '{{ $jadwal->status_pertemuan }}' == 'berlangsung',
                                                    'bg-white dark:bg-gray-800': '{{ $jadwal->status_pertemuan }}' == 'mendatang'
                                                }"
                                                class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm">
                                                <div class="flex justify-between items-center p-4 cursor-pointer" @click="showStudents = !showStudents">
                                                    <div class="flex items-center space-x-3">
                                                        <i class="fa-solid" :class="showStudents ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                        <div>
                                                            <p class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ $jadwal->mataPelajaran->nama_mapel }} - {{ $jadwal->kelas->nama_kelas }}</p>
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium"
                                                                    x-bind:class="{
                                                                        'bg-gray-300 text-gray-800 dark:bg-gray-600 dark:text-gray-200': '{{ $jadwal->status_pertemuan }}' == 'berlalu',
                                                                        'bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200': '{{ $jadwal->status_pertemuan }}' == 'berlangsung',
                                                                        'bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-200': '{{ $jadwal->status_pertemuan }}' == 'mendatang'
                                                                    }">
                                                                    {{ ucfirst($jadwal->status_pertemuan) }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        @if($jadwal->status_pertemuan == 'berlangsung')
                                                            <a href="{{ route('scan.index', ['jadwal_id' => $jadwal->id]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                                <i class="fa-solid fa-qrcode mr-1"></i> Mulai Absensi QR
                                                            </a>
                                                        @endif
                                                        <button type="button" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                                            <i class="fa-solid" :class="showStudents ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div x-show="showStudents" x-collapse class="p-4 border-t border-gray-200 dark:border-gray-700">
                                                    <h5 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">Daftar Siswa Kelas {{ $jadwal->kelas->nama_kelas }}</h5>
                                                    
                                                    <div class="mb-4 flex flex-wrap gap-2 text-sm text-gray-700 dark:text-gray-300">
                                                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 flex items-center"><i class="fa-solid fa-check-circle mr-1"></i> Hadir: {{ $jadwal->absensi_summary['hadir'] ?? 0 }}</span>
                                                        <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 flex items-center"><i class="fa-solid fa-hourglass-half mr-1"></i> Terlambat: {{ $jadwal->absensi_summary['terlambat'] ?? 0 }}</span>
                                                        <span class="px-2 py-1 rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 flex items-center"><i class="fa-solid fa-hospital mr-1"></i> Sakit: {{ $jadwal->absensi_summary['sakit'] ?? 0 }}</span>
                                                        <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 flex items-center"><i class="fa-solid fa-envelope-open-text mr-1"></i> Izin: {{ $jadwal->absensi_summary['izin'] ?? 0 }}</span>
                                                        <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 flex items-center"><i class="fa-solid fa-times-circle mr-1"></i> Alpha: {{ $jadwal->absensi_summary['alpha'] ?? 0 }}</span>
                                                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 flex items-center"><i class="fa-solid fa-question-circle mr-1"></i> Belum Tercatat: {{ $jadwal->absensi_summary[null] ?? 0 }}</span>
                                                        <a href="{{ route('rekap_absensi.index', [
                                                            'start_date' => $selectedDate,
                                                            'end_date' => $selectedDate,
                                                            'kelas_id' => $jadwal->kelas_id,
                                                            'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                                                            'guru_id' => Auth::user()->id // Filter by current guru
                                                        ]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                            <i class="fa-solid fa-chart-bar mr-1"></i> Lihat Rekap
                                                        </a>
                                                    </div>

                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                            <thead class="bg-gray-100 dark:bg-gray-700">
                                                                <tr>
                                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">NIS</th>
                                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nama Siswa</th>
                                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status Saat Ini</th>
                                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Keterangan</th>
                                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                                                @forelse ($jadwal->siswa_dengan_absensi as $siswa)
                                                                    <tr id="student-{{ $jadwal->id }}-{{ $siswa->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                                                        x-data="{ 
                                                                            loading: false,
                                                                            selectedStatus: '',
                                                                            waktuMasuk: '',
                                                                            keterangan: '',
                                                                            init() {
                                                                                this.selectedStatus = '{{ $siswa->absensi_status ?? '' }}';
                                                                                this.waktuMasuk = '{{ $siswa->absensi_waktu_masuk ? \Carbon\Carbon::parse($siswa->absensi_waktu_masuk)->format('H:i') : '' }}';
                                                                                this.keterangan = '{{ $siswa->absensi_keterangan ?? '' }}';
                                                                            },
                                                                            updateWaktuMasuk() {
                                                                                if (this.selectedStatus === 'hadir' || this.selectedStatus === 'terlambat') {
                                                                                    if (!this.waktuMasuk) {
                                                                                        const now = new Date();
                                                                                        this.waktuMasuk = now.toTimeString().slice(0, 5);
                                                                                    }
                                                                                } else {
                                                                                    this.waktuMasuk = '';
                                                                                }
                                                                            }
                                                                        }">
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $siswa->siswaProfile->nis ?? 'N/A' }}</td>
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $siswa->name }}</td>
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                                            <span class="status-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full items-center"
                                                                                x-bind:class="{
                                                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': selectedStatus == 'hadir',
                                                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': selectedStatus == 'terlambat',
                                                                                    'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200': selectedStatus == 'sakit',
                                                                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': selectedStatus == 'izin',
                                                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': selectedStatus == 'alpha',
                                                                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': selectedStatus == '' || selectedStatus == null
                                                                                }">
                                                                                <i class="fa-solid mr-1"
                                                                                    x-bind:class="{
                                                                                        'fa-check-circle text-green-500': selectedStatus == 'hadir',
                                                                                        'fa-hourglass-half text-yellow-500': selectedStatus == 'terlambat',
                                                                                        'fa-hospital text-orange-500': selectedStatus == 'sakit',
                                                                                        'fa-envelope-open-text text-blue-500': selectedStatus == 'izin',
                                                                                        'fa-times-circle text-red-500': selectedStatus == 'alpha',
                                                                                        'fa-question-circle text-gray-500': selectedStatus == '' || selectedStatus == null
                                                                                    }"></i>
                                                                                <span class="current-status" x-text="selectedStatus ? selectedStatus.charAt(0).toUpperCase() + selectedStatus.slice(1) : 'Belum Tercatat'"></span>
                                                                            </span>
                                                                        </td>
                                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300 current-keterangan" x-text="keterangan || '-'"></td>
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                                            <form @submit.prevent="
                                                                                loading = true;
                                                                                globalMessage = ''; // Clear global message before new submission
                                                                                globalMessageType = '';
                                                                                const formData = new FormData($event.target);
                                                                                const data = Object.fromEntries(formData.entries());
                                                                                axios.post('{{ route('guru.jadwal-mengajar.storeAttendance', $jadwal->id) }}', data)
                                                                                    .then(response => {
                                                                                        globalMessage = response.data.message;
                                                                                        globalMessageType = 'success';
                                                                                        // Update UI for the specific student
                                                                                        selectedStatus = data.status;
                                                                                        keterangan = data.keterangan;
                                                                                        waktuMasuk = data.waktu_masuk;
                                                                                    })
                                                                                    .catch(error => {
                                                                                        globalMessage = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan absensi.';
                                                                                        globalMessageType = 'error';
                                                                                        console.error('Error saving attendance:', error);
                                                                                    })
                                                                                    .finally(() => {
                                                                                        loading = false;
                                                                                        setTimeout(() => globalMessage = '', 5000); // Hide message after 5 seconds
                                                                                    });
                                                                            " class="flex items-center space-x-2">
                                                                                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                                                                <input type="hidden" name="tanggal_absensi" value="{{ $selectedDate }}">
                                                                                <input type="hidden" name="jadwal_absensi_id" value="{{ $jadwal->id }}">
                                                                                <select name="status" x-model="selectedStatus" @change="updateWaktuMasuk" class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                                                    <option value="">Pilih Status</option>
                                                                                    <option value="hadir">Hadir</option>
                                                                                    <option value="terlambat">Terlambat</option>
                                                                                    <option value="sakit">Sakit</option>
                                                                                    <option value="izin">Izin</option>
                                                                                    <option value="alpha">Alpha</option>
                                                                                </select>
                                                                                <input type="time" name="waktu_masuk" x-model="waktuMasuk" placeholder="Waktu Masuk (opsional)" class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                                                <input type="text" name="keterangan" x-model="keterangan" placeholder="Keterangan (opsional)" class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                                                <button type="submit" :disabled="loading" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                                                                    <i class="fa-solid fa-spinner fa-spin mr-2" x-show="loading" x-cloak></i>
                                                                                    Simpan
                                                                                </button>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Tidak ada siswa terdaftar di kelas ini.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
