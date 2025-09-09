<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <?php echo e(__('Kelola Jadwal Pelajaran')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div x-data="jadwalPage({
                allJadwal: <?php echo e(json_encode($allJadwal)); ?>,
                timeSlots: <?php echo e(json_encode($timeSlots)); ?>,
                hariOrder: <?php echo e(json_encode($hariOrder)); ?>,
                kelasId: <?php echo e($kelasId ?? 'null'); ?>,
                currentFilters: <?php echo e(json_encode($currentFilters)); ?>

            })" class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg" x-cloak>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Header & Actions -->
                    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                                Jadwal Pelajaran
                            </h3>
                            <?php if(isset($kelasName)): ?>
                                <p class="text-lg text-gray-600 dark:text-gray-400">Kelas <?php echo e($kelasName); ?></p>
                            <?php else: ?>
                                <p class="text-md text-gray-500 dark:text-gray-400">Menampilkan semua jadwal</p>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="<?php echo e(route('jadwal.create', ['kelas_id' => $kelasId])); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 shadow-sm">
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
                                x-show="selectedJadwal.length > 0"
                                x-transition
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 shadow-sm disabled:opacity-50"
                                :disabled="isDeleting">
                                <span x-show="!isDeleting"><i class="fa-solid fa-trash-can mr-2"></i>Hapus (<span x-text="selectedJadwal.length"></span>)</span>
                                <span x-show="isDeleting">Menghapus...</span>
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg mb-6 border border-gray-200 dark:border-gray-700">
                        <form action="<?php echo e(route('jadwal.index')); ?>" method="GET">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div class="lg:col-span-2">
                                    <label for="search" class="sr-only">Search</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-search text-gray-400"></i>
                                        </div>
                                        <input type="text" name="search" id="search" x-model="searchTerm" placeholder="Cari (Mapel, Guru...)"
                                            class="block w-full pl-10 pr-4 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                                    </div>
                                </div>
                                <div>
                                    <label for="kelas_id" class="sr-only">Kelas</label>
                                    <select name="kelas_id" id="kelas_id" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-800 dark:text-white">
                                        <option value="">Semua Kelas</option>
                                        <?php $__currentLoopData = $allKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($kelas->id); ?>" <?php if(request('kelas_id') == $kelas->id): ?> selected <?php endif; ?>><?php echo e($kelas->nama_kelas); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="lg:col-span-2 flex items-center space-x-2">
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fa-solid fa-filter mr-2"></i> Filter
                                    </button>
                                    <a href="<?php echo e(route('jadwal.index')); ?>" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
                                                            <div @click="selectedJadwal.includes(jadwal.id.toString()) ? selectedJadwal = selectedJadwal.filter(id => id !== jadwal.id.toString()) : selectedJadwal.push(jadwal.id.toString())"
                                                                :class="{'ring-2 ring-indigo-500 ring-offset-2 ring-offset-white dark:ring-offset-gray-800 shadow-lg': selectedJadwal.includes(jadwal.id.toString()), 'hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-500 cursor-pointer': true}"
                                                                class="bg-gray-100 dark:bg-gray-700/60 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-600/50 relative group transition-all duration-200 ease-in-out">
                                                                
                                                                <div class="flex items-start">
                                                                    <input type="checkbox" :value="jadwal.id.toString()" x-model="selectedJadwal"
                                                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 mt-1 h-4 w-4 cursor-pointer">
                                                                    
                                                                    <div class="ml-3 flex-1">
                                                                        <p class="font-bold text-gray-800 dark:text-gray-100" x-text="jadwal.mata_pelajaran.nama_mapel"></p>
                                                                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 space-y-1.5">
                                                                            <p class="flex items-center"><i class="fa-solid fa-clock w-4 mr-1.5"></i> <span x-text="new Date(jadwal.jam_mulai).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':') + ' - ' + new Date(jadwal.jam_selesai).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':')"></span></p>
                                                                            <p class="flex items-center"><i class="fa-solid fa-chalkboard-user w-4 mr-1.5"></i> <span x-text="jadwal.guru.name || '-'"></span></p>
                                                                            <?php if(!isset($kelasName)): ?>
                                                                            <p class="flex items-center"><i class="fa-solid fa-school w-4 mr-1.5"></i> <span x-text="'Kelas: ' + jadwal.kelas.nama_kelas"></span></p>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-200 dark:bg-gray-800/80 backdrop-blur-sm p-1 rounded-md z-10">
                                                                    <a :href="'/jadwal/' + jadwal.id + '/edit'" @click.stop class="p-1.5 h-7 w-7 flex items-center justify-center text-indigo-600 hover:bg-indigo-100 dark:text-indigo-400 dark:hover:bg-gray-600 rounded-md"><i class="fa-solid fa-pencil"></i></a>
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
                                                    <h3 class="mt-4 text-lg font-medium text-gray-800 dark:text-gray-200">Tidak Ada Jadwal Ditemukan</h3>
                                                    <p class="mt-1 text-sm text-gray-500">Tidak ada jadwal yang cocok dengan filter yang diterapkan.</p>
                                                    <div class="mt-6">
                                                        <a href="<?php echo e(route('jadwal.index')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 shadow-sm">
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
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Impor Jadwal dari Excel</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Unggah file Excel untuk mengimpor beberapa jadwal sekaligus. Pastikan file Anda sesuai dengan format template.
                        </p>
                        
                        <div class="text-sm mb-4">
                            <a href="<?php echo e(route('jadwal.importTemplate')); ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline" data-turbo="false">
                                <i class="fa-solid fa-download mr-1"></i>
                                Unduh Template Excel
                            </a>
                        </div>

                        <form action="<?php echo e(route('jadwal.import')); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
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
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Konfirmasi Hapus Jadwal</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Anda yakin ingin menghapus jadwal ini secara permanen? Tindakan ini tidak dapat diurungkan.</p>
                        <div class="flex justify-end space-x-3">
                            <button @click="showSingleDeleteConfirm = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                            <form :action="'/jadwal/' + deleteJadwalId" method="POST" class="inline-block">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Ya, Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div x-show="showBulkDeleteConfirm" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-cloak>
                    <div @click.away="showBulkDeleteConfirm = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Konfirmasi Hapus Massal</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Anda yakin ingin menghapus <span x-text="selectedJadwal.length"></span> jadwal terpilih? Tindakan ini tidak dapat diurungkan.</p>
                        <div class="flex justify-end space-x-3">
                            <button @click="showBulkDeleteConfirm = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Batal</button>
                            <button @click="bulkDelete" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Ya, Hapus Semua</button>
                        </div>
                    </div>
                </div>
                <div x-show="showExportModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-cloak>
                    <div @click.away="showExportModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Ekspor Jadwal ke Excel</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Pilih filter untuk data yang ingin Anda ekspor.</p>
                        
                        <form action="<?php echo e(route('jadwal.export')); ?>" method="GET" data-turbo="false">
                            <div class="space-y-4">
                                <div>
                                    <label for="export_kelas_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kelas</label>
                                    <select name="kelas_id" id="export_kelas_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-900 dark:text-white">
                                        <option value="">Semua Kelas</option>
                                        <?php $__currentLoopData = $allKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($kelas->id); ?>"><?php echo e($kelas->nama_kelas); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="export_hari" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hari</label>
                                    <select name="hari" id="export_hari" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-900 dark:text-white">
                                        <option value="">Semua Hari</option>
                                        <?php $__currentLoopData = $hariOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hari): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($hari); ?>"><?php echo e($hari); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

    <?php $__env->startPush('scripts'); ?>
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
        function jadwalPage(config) {
            return {
                searchTerm: new URLSearchParams(window.location.search).get('search') || '',
                selectedJadwal: [],
                selectAll: false,
                isDeleting: false,
                showBulkDeleteConfirm: false,
                showImportModal: false,
                showExportModal: false,
                exporting: false,
                showSingleDeleteConfirm: false,
                deleteJadwalId: null,
                allJadwal: config.allJadwal || [],
                timeSlots: config.timeSlots || [],
                hariOrder: config.hariOrder || [],
                kelasId: config.kelasId,
                jadwalMap: {},

                init() {
                    this.buildJadwalMap();

                    this.$watch('selectAll', (value) => {
                        if (value) {
                            this.selectedJadwal = this.allVisibleJadwalIds;
                        } else {
                            if (this.selectedJadwal.length === this.allVisibleJadwalIds.length) {
                                this.selectedJadwal = [];
                            }
                        }
                    });

                    this.$watch('selectedJadwal', (newValue) => {
                        if (this.allVisibleJadwalIds.length > 0 && newValue.length === this.allVisibleJadwalIds.length) {
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

                get allVisibleJadwalIds() {
                    const term = this.searchTerm.toLowerCase().trim();
                    if (!term) {
                        return this.allJadwal.map(j => j.id.toString());
                    }
                    return this.allJadwal.filter(jadwal => {
                        const mapel = jadwal.mata_pelajaran.nama_mapel.toLowerCase();
                        const guru = jadwal.guru.name.toLowerCase();
                        const kelas = jadwal.kelas.nama_kelas.toLowerCase();
                        return mapel.includes(term) || guru.includes(term) || kelas.includes(term);
                    }).map(j => j.id.toString());
                },

                buildJadwalMap() {
                    let map = {};
                    this.allJadwal.forEach(jadwal => {
                        const formattedJamMulai = new Date(jadwal.jam_mulai).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
                        const formattedJamSelesai = new Date(jadwal.jam_selesai).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
                        const key = `${jadwal.hari}-${formattedJamMulai}-${formattedJamSelesai}`;
                        if (!map[key]) {
                            map[key] = [];
                        }
                        map[key].push(jadwal);
                    });
                    this.jadwalMap = map;
                },

                getJadwalForSlot(hari, jamMulai, jamSelesai) {
                    const key = `${hari}-${jamMulai}-${jamSelesai}`;
                    const jadwalInSlot = this.jadwalMap[key] || [];
                    const term = this.searchTerm.toLowerCase().trim();

                    if (!term) {
                        return jadwalInSlot;
                    }

                    return jadwalInSlot.filter(jadwal => {
                        const mapel = jadwal.mata_pelajaran.nama_mapel.toLowerCase();
                        const guru = jadwal.guru.name.toLowerCase();
                        const kelas = jadwal.kelas.nama_kelas.toLowerCase();
                        return mapel.includes(term) || guru.includes(term) || kelas.includes(term);
                    });
                },

                confirmSingleDelete(id) {
                    this.deleteJadwalId = id;
                    this.showSingleDeleteConfirm = true;
                },

                async bulkDelete() {
                    if (this.selectedJadwal.length === 0) return;
                    this.isDeleting = true;

                    try {
                        const response = await axios.post('<?php echo e(route("jadwal.bulkDestroy")); ?>', {
                            jadwal_ids: this.selectedJadwal
                        });

                        if (response.data.success) {
                            this.allJadwal = this.allJadwal.filter(j => !this.selectedJadwal.includes(j.id.toString()));
                            this.buildJadwalMap(); // Rebuild the map after deletion
                            this.selectedJadwal = [];
                            alert(response.data.message);
                        } else {
                            alert(response.data.message || 'Gagal menghapus jadwal.');
                        }
                    } catch (error) {
                        console.error('Bulk delete error:', error);
                        alert('Terjadi kesalahan saat menghapus jadwal.');
                    } finally {
                        this.isDeleting = false;
                        this.showBulkDeleteConfirm = false;
                    }
                }
            };
        }
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/jadwal/index.blade.php ENDPATH**/ ?>