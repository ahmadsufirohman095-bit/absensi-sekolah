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
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <?php echo e(__('Kelola Mapel')); ?>

            </h2>
            <div class="flex flex-wrap items-center justify-start md:justify-end gap-2">
                <a href="<?php echo e(route('mata-pelajaran.create')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-indigo-700">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Mapel Baru
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    
                    <div class="mb-4">
                        <form action="<?php echo e(route('mata-pelajaran.index')); ?>" method="GET">
                            <div class="flex items-center">
                                <input type="text" name="search" placeholder="Cari nama atau kode mapel..."
                                       class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                       value="<?php echo e($search ?? ''); ?>">
                                <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 hidden sm:table-header-group">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        <a href="<?php echo e(route('mata-pelajaran.index', array_merge(request()->query(), ['sort_by' => 'kode_mapel', 'sort_direction' => ($sortBy === 'kode_mapel' && $sortDirection === 'asc') ? 'desc' : 'asc']))); ?>" class="flex items-center">
                                            Kode Mapel
                                            <?php if($sortBy === 'kode_mapel'): ?>
                                                <?php if($sortDirection === 'asc'): ?>
                                                    <svg class="w-3 h-3 ml-1" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                <?php else: ?>
                                                    <svg class="w-3 h-3 ml-1" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <a href="<?php echo e(route('mata-pelajaran.index', array_merge(request()->query(), ['sort_by' => 'nama_mapel', 'sort_direction' => ($sortBy === 'nama_mapel' && $sortDirection === 'asc') ? 'desc' : 'asc']))); ?>" class="flex items-center">
                                            Nama Mapel
                                            <?php if($sortBy === 'nama_mapel'): ?>
                                                <?php if($sortDirection === 'asc'): ?>
                                                    <svg class="w-3 h-3 ml-1" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                <?php else: ?>
                                                    <svg class="w-3 h-3 ml-1" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3">Guru Pengampu</th>
                                    
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block sm:table-row-group">
                                <?php $__empty_1 = true; $__currentLoopData = $mapel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 block sm:table-row mb-4 sm:mb-0 rounded-lg shadow-md sm:shadow-none">
                                    <td class="px-6 py-4 font-mono text-gray-700 dark:text-gray-300 block sm:table-cell">
                                        <span class="font-bold sm:hidden">Kode Mapel: </span>
                                        <?php echo e($item->kode_mapel); ?>

                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap block sm:table-cell">
                                        <span class="font-bold sm:hidden">Nama Mapel: </span>
                                        <a href="<?php echo e(route('mata-pelajaran.show', $item->id)); ?>" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"><?php echo e($item->nama_mapel); ?></a>
                                        <div class="text-xs text-gray-500"><?php echo e(Str::limit($item->deskripsi, 50)); ?></div>
                                    </td>
                                    <td class="px-6 py-4 block sm:table-cell">
                                        <span class="font-bold sm:hidden block mb-1">Guru Pengampu: </span>
                                        <?php if($item->gurus->isNotEmpty()): ?>
                                            <?php echo e($item->gurus->first()->name); ?>

                                        <?php else: ?>
                                            <span class="text-gray-400 italic">- Belum diatur -</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-right block sm:table-cell" x-data="{ confirmDelete: false }">
                                        <div x-show="!confirmDelete" class="flex items-center justify-end space-x-3">
                                            <a href="<?php echo e(route('mata-pelajaran.edit', $item)); ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline inline-flex items-center"><i class="fas fa-edit mr-1"></i> Edit</a>
                                            <button type="button" @click="confirmDelete = true"
                                                    class="font-medium text-red-600 dark:text-red-500 hover:underline inline-flex items-center"
                                                    :disabled="<?php echo e($item->gurus_count > 0 || $item->kelas_count > 0); ?>"
                                                    :class="{ 'opacity-50 cursor-not-allowed': <?php echo e($item->gurus_count > 0 || $item->kelas_count > 0); ?> }"
                                                    title="<?php echo e(($item->gurus_count > 0 || $item->kelas_count > 0) ? 'Tidak dapat menghapus: Mata pelajaran ini masih terkait dengan guru atau kelas.' : 'Hapus Mata Pelajaran'); ?>">
                                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                                            </button>
                                        </div>
                                        <div x-show="confirmDelete" x-cloak class="flex items-center justify-end space-x-2">
                                            <span class="text-sm text-gray-600">Yakin?</span>
                                            <button @click="confirmDelete = false" class="px-2 py-1 text-xs rounded-md bg-gray-200 hover:bg-gray-300">Batal</button>
                                            <form action="<?php echo e(route('mata-pelajaran.destroy', $item)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="px-2 py-1 text-xs rounded-md text-white bg-red-600 hover:bg-red-700">Ya</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr class="block sm:table-row">
                                    <td colspan="4" class="text-center p-4 block sm:table-cell">
                                        <?php if(!empty($search)): ?>
                                            Tidak ada mata pelajaran yang cocok dengan pencarian "<?php echo e($search); ?>".
                                        <?php else: ?>
                                            Belum ada data mata pelajaran.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <?php echo e($mapel->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
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
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/mapel/index.blade.php ENDPATH**/ ?>