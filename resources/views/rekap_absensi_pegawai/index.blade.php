<x-app-layout>
    <x-slot name="header">
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Rekap Absensi Pegawai') }}
                </h2>
                
            </div>
            <div class="flex items-center space-x-2">
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'guru' || auth()->user()->role === 'tu' || auth()->user()->role === 'other')
                <a href="{{ route('rekap_absensi_pegawai.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Tambah Absensi Pegawai
                </a>
                @endif
                <button @click="$store.rekapAbsensiPegawai.showExportModal = true" type="button" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 shadow-sm">
                    <i class="fa-solid fa-file-excel mr-2"></i>
                    Ekspor Excel
                </button>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'guru' || auth()->user()->role === 'tu' || auth()->user()->role === 'other')
                <button @click="$store.rekapAbsensiPegawai.showBulkDeleteConfirm = true" type="button"
                    x-show="$store.rekapAbsensiPegawai.selectedAbsensi.length > 0"
                    x-transition
                    x-cloak
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm disabled:opacity-50"
                    :disabled="$store.rekapAbsensiPegawai.isDeleting">
                    <span x-show="!$store.rekapAbsensiPegawai.isDeleting"><i class="fa-solid fa-trash-can mr-2"></i>Hapus (<span x-text="$store.rekapAbsensiPegawai.selectedAbsensi.length"></span>)</span>
                    <span x-show="$store.rekapAbsensiPegawai.isDeleting">Menghapus...</span>
                </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div x-data="{}" x-init="
        const urlParams = new URLSearchParams(window.location.search);
        $store.rekapAbsensiPegawai.initPage({
            bulkDeleteUrl: '{{ route("rekap_absensi_pegawai.bulkDestroy") }}',
            filters: {
                startDate: urlParams.get('start_date') || '',
                endDate: urlParams.get('end_date') || '',
                userId: urlParams.get('user_id') || '',
                pegawaiRole: urlParams.get('pegawai_role') || '',
                attendanceType: urlParams.get('attendance_type') || '',
                search: urlParams.get('search') || '',
            }
        });
    " x-on:destroy="$store.rekapAbsensiPegawai.destroyPage()" class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            
            <!-- Filters -->
            <div x-data="{ showFilters: false }" class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg mb-6 border border-gray-200 dark:border-gray-700" x-cloak>
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Filter Data Absensi Pegawai</h4>
                    <button @click="showFilters = !showFilters" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 focus:outline-none">
                        <span x-show="!showFilters"><i class="fa-solid fa-chevron-down"></i> Tampilkan Filter</span>
                        <span x-show="showFilters"><i class="fa-solid fa-chevron-up"></i> Sembunyikan Filter</span>
                    </button>
                </div>
                <div x-show="showFilters" x-collapse>
                    <form action="{{ route('rekap_absensi_pegawai.index') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari Tanggal</label>
                                <x-text-input id="start_date" name="start_date" type="text" class="mt-1 block w-full flatpickr-date" value="{{ request('start_date') }}" />
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai Tanggal</label>
                                <x-text-input id="end_date" name="end_date" type="text" class="mt-1 block w-full flatpickr-date" value="{{ request('end_date') }}" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pegawai</label>
                                <select name="user_id" id="user_id" class="mt-1 block w-full tom-select-rekap">
                                    <option value="">Semua Pegawai</option>
                                    @foreach($allPegawai as $pegawai)
                                        <option value="{{ $pegawai->id }}" @if(request('user_id') == $pegawai->id) selected @endif>{{ $pegawai->name }} - ({{ ucfirst($pegawai->role) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="pegawai_role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Pegawai</label>
                                <select name="pegawai_role" id="pegawai_role" class="mt-1 block w-full tom-select-rekap">
                                    <option value="">Semua Jenis</option>
                                    <option value="guru" @if(request('pegawai_role') == 'guru') selected @endif>Guru</option>
                                    <option value="tu" @if(request('pegawai_role') == 'tu') selected @endif>TU</option>
                                    <option value="other" @if(request('pegawai_role') == 'other') selected @endif>Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label for="attendance_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Absensi</label>
                                <select name="attendance_type" id="attendance_type" class="mt-1 block w-full tom-select-rekap">
                                    <option value="">Semua Tipe</option>
                                    <option value="manual" @if(request('attendance_type') == 'manual') selected @endif>Manual</option>
                                    <option value="qr_code" @if(request('attendance_type') == 'qr_code') selected @endif>QR Code</option>
                                </select>
                            </div>
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fa-solid fa-filter mr-2"></i> Filter
                                </button>
                                <a href="{{ route('rekap_absensi_pegawai.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Reset
                                </a>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center space-x-2">
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari (Nama Pegawai...)"
                                    class="block w-full pl-10 pr-4 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg mb-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold mb-3">Ringkasan Absensi Pegawai</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 text-center">
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
            </div>

            <!-- Attendance Table -->
            <div id="attendance-table-container" class="overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 min-w-full">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'guru' || auth()->user()->role === 'tu' || auth()->user()->role === 'other')
                            <th scope="col" class="p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-all-items" type="checkbox" @change="$store.rekapAbsensiPegawai.toggleSelectAll($event)" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-all-items" class="sr-only">checkbox</label>
                                </div>
                            </th>
                            @endif
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                            <th scope="col" class="px-6 py-3">Waktu</th>
                            <th scope="col" class="px-6 py-3">Pegawai</th>
                            <th scope="col" class="px-6 py-3">Jenis Pegawai</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Keterangan</th>
                            <th scope="col" class="px-6 py-3">Tipe</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensisPegawai as $absensiPegawai)
                            <tr id="absensi-pegawai-row-{{ $absensiPegawai->id }}" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 odd:bg-gray-50 dark:odd:bg-gray-900">
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'guru' || auth()->user()->role === 'tu' || auth()->user()->role === 'other')
                                <td class="w-4 p-4">
                                    <div class="flex items-center">
                                        <input id="checkbox-item-{{ $absensiPegawai->id }}" type="checkbox" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" value="{{ $absensiPegawai->id }}" x-model="$store.rekapAbsensiPegawai.selectedAbsensi">
                                        <label for="checkbox-item-{{ $absensiPegawai->id }}" class="sr-only">checkbox</label>
                                    </div>
                                </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">{{ $absensiPegawai->tanggal_absensi->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $absensiPegawai->waktu_masuk ? $absensiPegawai->waktu_masuk->format('H:i') : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $absensiPegawai->user->name ?? 'Pegawai Dihapus' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($absensiPegawai->user->role ?? '-') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': '{{ $absensiPegawai->status }}' == 'hadir',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': '{{ $absensiPegawai->status }}' == 'terlambat',
                                            'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200': '{{ $absensiPegawai->status }}' == 'sakit',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': '{{ $absensiPegawai->status }}' == 'izin',
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': '{{ $absensiPegawai->status }}' == 'alpha',
                                        }">
                                        {{ ucfirst($absensiPegawai->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $absensiPegawai->keterangan ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($absensiPegawai->attendance_type ?? 'N/A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'guru' || auth()->user()->role === 'tu' || auth()->user()->role === 'other')
                                    <a href="{{ route('rekap_absensi_pegawai.edit', $absensiPegawai->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600 mr-2">
                                        <i class="fa-solid fa-edit mr-1"></i> Edit
                                    </a>
                                    <button @click.once="$store.rekapAbsensiPegawai.confirmSingleDelete({{ $absensiPegawai->id }})" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-500 dark:hover:bg-red-600">
                                        <i class="fa-solid fa-trash-alt mr-1"></i> Hapus
                                    </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data absensi pegawai ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $absensisPegawai->appends(request()->query())->links('vendor.pagination.compact-tailwind') }}
            </div>
        </div>

        <!-- Modals -->
        <div x-show="$store.rekapAbsensiPegawai.showSingleDeleteConfirm" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-cloak>
            <div @click.away="$store.rekapAbsensiPegawai.showSingleDeleteConfirm = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Konfirmasi Hapus Absensi Pegawai</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Anda yakin ingin menghapus data absensi pegawai ini secara permanen? Tindakan ini tidak dapat diurungkan.</p>
                <div class="flex justify-end space-x-3">
                    <button @click="$store.rekapAbsensiPegawai.showSingleDeleteConfirm = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                    <button @click="$store.rekapAbsensiPegawai.deleteSingleAbsensi()" type="button" 
                            :disabled="$store.rekapAbsensiPegawai.isDeleting"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50">
                        <i class="fa-solid fa-spinner fa-spin mr-2" x-show="$store.rekapAbsensiPegawai.isDeleting" x-cloak></i>
                        <span x-show="!$store.rekapAbsensiPegawai.isDeleting">Ya, Hapus</span>
                        <span x-show="$store.rekapAbsensiPegawai.isDeleting">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'guru' || auth()->user()->role === 'tu' || auth()->user()->role === 'other')
        <div x-show="$store.rekapAbsensiPegawai.showBulkDeleteConfirm" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-cloak>
            <div @click.away="$store.rekapAbsensiPegawai.showBulkDeleteConfirm = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Konfirmasi Hapus Massal Absensi Pegawai</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Anda yakin ingin menghapus <span x-text="$store.rekapAbsensiPegawai.selectedAbsensi.length"></span> data absensi pegawai terpilih? Tindakan ini tidak dapat diurungkan.</p>
                <div class="flex justify-end space-x-3">
                    <button @click="$store.rekapAbsensiPegawai.showBulkDeleteConfirm = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                    <button @click="$store.rekapAbsensiPegawai.bulkDelete()" type="button" 
                            :disabled="$store.rekapAbsensiPegawai.isDeleting"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50">
                        <i class="fa-solid fa-spinner fa-spin mr-2" x-show="$store.rekapAbsensiPegawai.isDeleting" x-cloak></i>
                        <span x-show="!$store.rekapAbsensiPegawai.isDeleting">Ya, Hapus Semua</span>
                        <span x-show="$store.rekapAbsensiPegawai.isDeleting">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
        @endif
        <div x-show="$store.rekapAbsensiPegawai.showExportModal" 
             @keydown.escape.window="$store.rekapAbsensiPegawai.showExportModal = false; $store.rekapAbsensiPegawai.destroyExportModal()" 
             x-init="$watch('$store.rekapAbsensiPegawai.showExportModal', value => { if (value) { $nextTick(() => $store.rekapAbsensiPegawai.initExportModal()); } else { $store.rekapAbsensiPegawai.destroyExportModal(); } })" 
             class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" 
             x-cloak>
            <div @click.away="$store.rekapAbsensiPegawai.showExportModal = false; $store.rekapAbsensiPegawai.destroyExportModal()" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg flex flex-col">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Ekspor Rekap Absensi Pegawai</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pilih kriteria untuk mengekspor data ke Excel.</p>
                </div>
                
                <form action="{{ route('rekap_absensi_pegawai.export') }}" method="GET" data-turbo="false" x-ref="exportForm" class="p-6 space-y-5 overflow-y-auto" style="max-height: 70vh;">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="export_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                            <x-text-input id="export_start_date" name="start_date" type="text" class="w-full flatpickr-date-export" />
                        </div>
                        <div>
                            <label for="export_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                            <x-text-input id="export_end_date" name="end_date" type="text" class="w-full flatpickr-date-export" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="export_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pegawai</label>
                            <select name="user_id" id="export_user_id" class="w-full tom-select-export">
                                <option value="">Semua Pegawai</option>
                                @foreach($allPegawai as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->name }} - ({{ ucfirst($pegawai->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="export_pegawai_role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Pegawai</label>
                            <select name="pegawai_role" id="export_pegawai_role" class="w-full tom-select-export">
                                <option value="">Semua Jenis</option>
                                <option value="guru">Guru</option>
                                <option value="tu">TU</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="export_attendance_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Absensi</label>
                            <select name="attendance_type" id="export_attendance_type" class="w-full tom-select-export">
                                <option value="">Semua Tipe</option>
                                <option value="manual">Manual</option>
                                <option value="qr_code">QR Code</option>
                            </select>
                        </div>
                        <div>
                            <label for="export_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Absensi</label>
                            <select name="status" id="export_status" class="w-full tom-select-export">
                                <option value="">Semua Status</option>
                                <option value="hadir">Hadir</option>
                                <option value="terlambat">Terlambat</option>
                                <option value="sakit">Sakit</option>
                                <option value="izin">Izin</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="p-6 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <button @click="$refs.exportForm.reset(); $store.rekapAbsensiPegawai.resetExportFilters()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 shadow-sm">
                            <i class="fa-solid fa-arrows-rotate mr-2"></i>
                            Reset
                        </button>
                        <div class="flex space-x-3">
                            <button @click="$store.rekapAbsensiPegawai.showExportModal = false; $store.rekapAbsensiPegawai.destroyExportModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm">Batal</button>
                            <button @click="$refs.exportForm.submit()" 
                                    type="button"
                                    x-data="{ exporting: false }"
                                    @click="exporting = true; setTimeout(() => { exporting = false; $store.rekapAbsensiPegawai.showExportModal = false; }, 3000)"
                                    :class="{ 'opacity-50 cursor-not-allowed': exporting }"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fa-solid fa-spinner fa-spin -ml-1 mr-2" x-show="exporting" x-cloak></i>
                                <i class="fa-solid fa-file-excel -ml-1 mr-2" x-show="!exporting"></i>
                                <span x-text="exporting ? 'Mengekspor...' : 'Ekspor Sekarang'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/tableDragScroll.js')
        @vite('resources/js/rekapAbsensiPegawai.js') <!-- Akan kita buat nanti -->
    @endpush
</x-app-layout>
