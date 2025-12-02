<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kelola Jadwal Absensi Pegawai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div x-data="jadwalAbsensiPegawaiPage({
                allJadwalAbsensiPegawai: {{ json_encode($allJadwalAbsensiPegawai) }},
                timeSlots: {{ json_encode($timeSlots) }},
                hariOrder: {{ json_encode($hariOrder) }},
                userId: {{ $userId ?? 'null' }},
                currentFilters: {{ json_encode($currentFilters) }}
            })" class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg" x-cloak>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Header & Actions -->
                    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                                Jadwal Absensi Pegawai
                            </h3>
                            @isset($userName)
                                <p class="text-lg text-gray-600 dark:text-gray-400">Untuk: {{ $userName }}</p>
                            @else
                                <p class="text-md text-gray-500 dark:text-gray-400">Menampilkan semua jadwal absensi pegawai</p>
                            @endisset
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('jadwal-absensi-pegawai.create', ['user_id' => $userId]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 shadow-sm">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Tambah Jadwal
                            </a>
                            <button @click="showImportModal = true" type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 shadow-sm">
                                <i class="fa-solid fa-file-import mr-2"></i>
                                Impor Jadwal
                            </button>
                            <button @click="showExportModal = true" type="button" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 shadow-sm">
                                <i class="fa-solid fa-file-excel mr-2"></i>
                                Ekspor Excel
                            </button>
                            <button @click="showBulkDeleteConfirm = true" type="button"
                                x-show="selectedJadwalAbsensiPegawai.length > 0"
                                x-transition
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm disabled:opacity-50"
                                :disabled="isDeleting">
                                <span x-show="!isDeleting"><i class="fa-solid fa-trash-can mr-2"></i>Hapus (<span x-text="selectedJadwalAbsensiPegawai.length"></span>)</span>
                                <span x-show="isDeleting">Menghapus...</span>
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg mb-6 border border-gray-200 dark:border-gray-700">
                        <form action="{{ route('jadwal-absensi-pegawai.index') }}" method="GET">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div class="lg:col-span-2">
                                    <label for="search" class="sr-only">Search</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-search text-gray-400"></i>
                                        </div>
                                        <input type="text" name="search" id="search" x-model="searchTerm" placeholder="Cari (Nama Pegawai...)"
                                            class="block w-full pl-10 pr-4 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                                    </div>
                                </div>
                                <div>
                                    <label for="user_id" class="sr-only">Pegawai</label>
                                    <select name="user_id" id="user_id" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-800 dark:text-white">
                                        <option value="">Semua Pegawai</option>
                                        @foreach($allUsers as $user)
                                            <option value="{{ $user->id }}" @if(request('user_id') == $user->id) selected @endif>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="lg:col-span-2 flex items-center space-x-2">
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fa-solid fa-filter mr-2"></i> Filter
                                    </button>
                                    <a href="{{ route('jadwal-absensi-pegawai.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Timetable Grid -->
                    <div class="overflow-auto h-[70vh] bg-white dark:bg-gray-800 rounded-lg shadow no-scrollbar">
                        <div class="relative overflow-auto h-full cursor-grab no-scrollbar" x-ref="slider">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700/50" style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-40 bg-gray-100 dark:bg-gray-800" style="position: sticky; left: 0; z-index: 5;">
                                            <div class="flex items-center">
                                                <input type="checkbox" x-model="selectAll" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-3">
                                                <i class="fa-solid fa-clock mr-2"></i>
                                                Jam
                                            </div>
                                        </th>
                                        <template x-for="hari in hariOrder" :key="hari">
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider" x-text="hari"></th>
                                        </template>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-600">
                                    <template x-for="slot in timeSlots" :key="slot.jam_mulai + '-' + slot.jam_selesai">
                                        <tr class="transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-900/20">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center bg-gray-50 dark:bg-gray-700/50" style="position: sticky; left: 0; z-index: 5;">
                                                <div class="flex flex-col items-center justify-center h-full">
                                                    <span class="text-lg" x-text="slot.jam_mulai.substring(0, 5)"></span>
                                                    <span class="text-gray-500 dark:text-gray-400 text-xs">s/d</span>
                                                    <span class="text-lg" x-text="slot.jam_selesai.substring(0, 5)"></span>
                                                </div>
                                            </td>
                                            <template x-for="hari in hariOrder" :key="hari">
                                                <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400 align-top min-w-[200px]">
                                                    <div class="space-y-2 py-1">
                                                        <template x-for="jadwal in getJadwalForSlot(hari, slot.jam_mulai, slot.jam_selesai)" :key="jadwal.id">
                                                            <div @click="selectedJadwalAbsensiPegawai.includes(jadwal.id.toString()) ? selectedJadwalAbsensiPegawai = selectedJadwalAbsensiPegawai.filter(id => id !== jadwal.id.toString()) : selectedJadwalAbsensiPegawai.push(jadwal.id.toString())"
                                                                :class="{'ring-2 ring-indigo-500 ring-offset-2 ring-offset-white dark:ring-offset-gray-800 shadow-lg': selectedJadwalAbsensiPegawai.includes(jadwal.id.toString()), 'hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-500 cursor-pointer': true}"
                                                                class="bg-gray-100 dark:bg-gray-700/60 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-600/50 relative group transition-all duration-200 ease-in-out">
                                                                
                                                                <div class="flex items-start">
                                                                    <input type="checkbox" :value="jadwal.id.toString()" x-model="selectedJadwalAbsensiPegawai"
                                                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 mt-1 h-4 w-4 cursor-pointer">
                                                                    
                                                                    <div class="ml-3 flex-1">
                                                                        <p class="font-bold text-gray-800 dark:text-gray-100" x-text="jadwal.user.name || 'Tidak Diketahui'"></p>
                                                                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 space-y-1.5">
                                                                            <p class="flex items-center"><i class="fa-solid fa-clock w-4 mr-1.5"></i> <span x-text="new Date(jadwal.jam_mulai).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':') + ' - ' + new Date(jadwal.jam_selesai).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':')"></span></p>
                                                                            <p class="flex items-center" x-show="jadwal.keterangan"><i class="fa-solid fa-info-circle w-4 mr-1.5"></i> <span x-text="jadwal.keterangan"></span></p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-200 dark:bg-gray-800/80 backdrop-blur-sm p-1 rounded-md z-10">
                                                                    <a :href="'/jadwal-absensi-pegawai/' + jadwal.id + '/edit'" @click.stop class="p-1.5 h-7 w-7 flex items-center justify-center text-indigo-600 hover:bg-indigo-100 dark:text-indigo-400 dark:hover:bg-gray-600 rounded-md"><i class="fa-solid fa-pencil"></i></a>
                                                                    <button @click.prevent.stop="confirmSingleDelete(jadwal.id)" type="button" class="p-1.5 h-7 w-7 flex items-center justify-center text-red-600 hover:bg-red-100 dark:text-red-500 dark:hover:bg-gray-600 rounded-md"><i class="fa-solid fa-trash-can"></i></button>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </td>
                                            </template>
                                        </tr>
                                    </template>
                                    
                                    <!-- Empty States -->
                                    <template x-if="timeSlots.length === 0">
                                        <tr>
                                            <td :colspan="hariOrder.length + 1" class="text-center py-24 bg-white dark:bg-gray-800">
                                                <div class="text-gray-400 dark:text-gray-500">
                                                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <h3 class="mt-4 text-lg font-medium text-gray-800 dark:text-gray-200">Tidak Ada Jadwal Absensi Pegawai Ditemukan</h3>
                                                    <p class="mt-1 text-sm text-gray-500">Tidak ada jadwal absensi pegawai yang cocok dengan filter yang diterapkan.</p>
                                                    <div class="mt-6">
                                                        <a href="{{ route('jadwal-absensi-pegawai.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 shadow-sm">
                                                            <i class="fa-solid fa-rotate-left mr-2"></i>
                                                            Reset Filter
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modals -->
                <div x-show="showImportModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-cloak>
                    <div @click.away="showImportModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Impor Jadwal Absensi Pegawai dari Excel</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Unggah file Excel untuk mengimpor beberapa jadwal absensi sekaligus. Pastikan file Anda sesuai dengan format template.
                        </p>
                        
                        <div class="text-sm mb-4">
                            <a href="{{ route('jadwal-absensi-pegawai.importTemplate') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline" data-turbo="false">
                                <i class="fa-solid fa-download mr-1"></i>
                                Unduh Template Excel
                            </a>
                        </div>

                        <form action="{{ route('jadwal-absensi-pegawai.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div>
                                <label for="import_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">File Excel (.xlsx)</label>
                                <input type="file" name="import_file" id="import_file" required accept=".xlsx" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                            </div>

                            <div class="flex justify-end space-x-3 mt-8">
                                <button @click="showImportModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 shadow-sm">
                                    <i class="fa-solid fa-file-import mr-2"></i>
                                    Impor Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div x-show="showSingleDeleteConfirm" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-cloak>
                    <div @click.away="showSingleDeleteConfirm = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Konfirmasi Hapus Jadwal Absensi Pegawai</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Anda yakin ingin menghapus jadwal absensi pegawai ini secara permanen? Tindakan ini tidak dapat diurungkan.</p>
                        <div class="flex justify-end space-x-3">
                            <button @click="showSingleDeleteConfirm = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                            <form :action="'/jadwal-absensi-pegawai/' + deleteJadwalAbsensiPegawaiId" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Ya, Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div x-show="showBulkDeleteConfirm" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-cloak>
                    <div @click.away="showBulkDeleteConfirm = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Konfirmasi Hapus Massal Jadwal Absensi Pegawai</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Anda yakin ingin menghapus <span x-text="selectedJadwalAbsensiPegawai.length"></span> jadwal absensi terpilih? Tindakan ini tidak dapat diurungkan.</p>
                        <div class="flex justify-end space-x-3">
                            <button @click="showBulkDeleteConfirm = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                            <button @click="bulkDelete" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Ya, Hapus Semua</button>
                        </div>
                    </div>
                </div>
                <div x-show="showExportModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-cloak>
                    <div @click.away="showExportModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Ekspor Jadwal Absensi Pegawai ke Excel</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Pilih filter untuk data yang ingin Anda ekspor.</p>
                        
                        <form action="{{ route('jadwal-absensi-pegawai.export') }}" method="GET" data-turbo="false">
                            <div class="space-y-4">
                                <div>
                                    <label for="export_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pegawai</label>
                                    <select name="user_id" id="export_user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-900 dark:text-white">
                                        <option value="">Semua Pegawai</option>
                                        @foreach($allUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="export_hari" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hari</label>
                                    <select name="hari" id="export_hari" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-900 dark:text-white">
                                        <option value="">Semua Hari</option>
                                        @foreach($hariOptions as $hari)
                                            <option value="{{ $hari }}">{{ $hari }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 mt-8">
                                <button @click="showExportModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                                <button type="submit" 
                                        @click="exporting = true; setTimeout(() => { exporting = false; showExportModal = false; }, 3000)"
                                        :class="{ 'opacity-50 cursor-not-allowed': exporting }"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 shadow-sm">
                                    <i class="fa-solid fa-spinner fa-spin mr-2" x-show="exporting" x-cloak></i>
                                    <i class="fa-solid fa-file-excel mr-2" x-show="!exporting"></i>
                                    <span x-text="exporting ? 'Mengekspor...' : 'Ekspor Sekarang'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .cursor-grab {
            cursor: grab;
        }
        .cursor-grabbing {
            cursor: grabbing;
        }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
    <script>
        function jadwalAbsensiPegawaiPage(config) {
            return {
                searchTerm: new URLSearchParams(window.location.search).get('search') || '',
                selectedJadwalAbsensiPegawai: [],
                selectAll: false,
                isDeleting: false,
                showBulkDeleteConfirm: false,
                showImportModal: false,
                showExportModal: false,
                exporting: false,
                showSingleDeleteConfirm: false,
                deleteJadwalAbsensiPegawaiId: null,
                allJadwalAbsensiPegawai: config.allJadwalAbsensiPegawai || [],
                timeSlots: config.timeSlots || [],
                hariOrder: config.hariOrder || [],
                userId: config.userId,
                jadwalAbsensiPegawaiMap: {},

                init() {
                    this.buildJadwalAbsensiPegawaiMap();

                    this.$watch('selectAll', (value) => {
                        if (value) {
                            this.selectedJadwalAbsensiPegawai = this.allVisibleJadwalAbsensiPegawaiIds;
                        } else {
                            if (this.selectedJadwalAbsensiPegawai.length === this.allVisibleJadwalAbsensiPegawaiIds.length) {
                                this.selectedJadwalAbsensiPegawai = [];
                            }
                        }
                    });

                    this.$watch('selectedJadwalAbsensiPegawai', (newValue) => {
                        if (this.allVisibleJadwalAbsensiPegawaiIds.length > 0 && newValue.length === this.allVisibleJadwalAbsensiPegawaiIds.length) {
                            this.selectAll = true;
                        } else {
                            this.selectAll = false;
                        }
                    });

                    const slider = this.$refs.slider;
                    if (!slider) return;

                    let isDown = false;
                    let startX, startY, scrollLeft, scrollTop;

                    slider.addEventListener('mousedown', (e) => {
                        const interactiveEls = 'a, button, input';
                        if (e.target.closest(interactiveEls)) {
                            return;
                        }
                        isDown = true;
                        slider.classList.add('cursor-grabbing');
                        startX = e.pageX - slider.offsetLeft;
                        startY = e.pageY - slider.offsetTop;
                        scrollLeft = slider.scrollLeft;
                        scrollTop = slider.scrollTop;
                        e.preventDefault();
                    });
                    slider.addEventListener('mouseleave', () => {
                        isDown = false;
                        slider.classList.remove('cursor-grabbing');
                    });
                    slider.addEventListener('mouseup', () => {
                        isDown = false;
                        slider.classList.remove('cursor-grabbing');
                    });
                    slider.addEventListener('mousemove', (e) => {
                        if (!isDown) return;
                        e.preventDefault();
                        const x = e.pageX - slider.offsetLeft;
                        const y = e.pageY - slider.offsetTop;
                        const walkX = (x - startX) * 2.5;
                        const walkY = (y - startY) * 2.5;
                        slider.scrollLeft = scrollLeft - walkX;
                        slider.scrollTop = scrollTop - walkY;
                    });
                },

                get allVisibleJadwalAbsensiPegawaiIds() {
                    const term = this.searchTerm.toLowerCase().trim();
                    if (!term) {
                        return this.allJadwalAbsensiPegawai.map(j => j.id.toString());
                    }
                    return this.allJadwalAbsensiPegawai.filter(jadwal => {
                        const user = jadwal.user.name.toLowerCase();
                        return user.includes(term);
                    }).map(j => j.id.toString());
                },

                buildJadwalAbsensiPegawaiMap() {
                    let map = {};
                    this.allJadwalAbsensiPegawai.forEach(jadwal => {
                        const formattedJamMulai = new Date(jadwal.jam_mulai).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
                        const formattedJamSelesai = new Date(jadwal.jam_selesai).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
                        const key = `${jadwal.hari}-${formattedJamMulai}-${formattedJamSelesai}`;
                        if (!map[key]) {
                            map[key] = [];
                        }
                        map[key].push(jadwal);
                    });
                    this.jadwalAbsensiPegawaiMap = map;
                },

                getJadwalForSlot(hari, jamMulai, jamSelesai) {
                    const key = `${hari}-${jamMulai}-${jamSelesai}`;
                    const jadwalInSlot = this.jadwalAbsensiPegawaiMap[key] || [];
                    const term = this.searchTerm.toLowerCase().trim();

                    if (!term) {
                        return jadwalInSlot;
                    }

                    return jadwalInSlot.filter(jadwal => {
                        const user = jadwal.user.name.toLowerCase();
                        return user.includes(term);
                    });
                },

                confirmSingleDelete(id) {
                    this.deleteJadwalAbsensiPegawaiId = id;
                    this.showSingleDeleteConfirm = true;
                },

                async bulkDelete() {
                    if (this.selectedJadwalAbsensiPegawai.length === 0) return;
                    this.isDeleting = true;

                    try {
                        const response = await axios.post('{{ route("jadwal-absensi-pegawai.bulkDestroy") }}', {
                            jadwal_absensi_pegawai_ids: this.selectedJadwalAbsensiPegawai
                        });

                        if (response.data.success) {
                            this.allJadwalAbsensiPegawai = this.allJadwalAbsensiPegawai.filter(j => !this.selectedJadwalAbsensiPegawai.includes(j.id.toString()));
                            this.buildJadwalAbsensiPegawaiMap(); // Rebuild the map after deletion
                            this.selectedJadwalAbsensiPegawai = [];
                            alert(response.data.message);
                        } else {
                            alert(response.data.message || 'Gagal menghapus jadwal absensi Pegawai.');
                        }
                    } catch (error) {
                        console.error('Bulk delete error:', error);
                        alert('Terjadi kesalahan saat menghapus jadwal absensi Pegawai.');
                    } finally {
                        this.isDeleting = false;
                        this.showBulkDeleteConfirm = false;
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
