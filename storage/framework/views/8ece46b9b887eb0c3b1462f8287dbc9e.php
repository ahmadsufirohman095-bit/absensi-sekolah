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
            <?php echo e(__('Jadwal Mengajar')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Jadwal Mengajar Anda</h3>
                        <div class="flex items-center space-x-2">
                            <a href="<?php echo e(route('guru.jadwal-mengajar.semua')); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-calendar-alt mr-2"></i> Semua Jadwal
                            </a>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <?php if($jadwalMengajar->count() > 0): ?>
                            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md">
                                <h4 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-3 pb-2 border-b border-gray-300 dark:border-gray-600">Jadwal Hari Ini (<?php echo e(ucfirst(now()->translatedFormat('l'))); ?>)</h4>
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
                                            <?php $__currentLoopData = $jadwalMengajar->sortBy('jam_mulai'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"><?php echo e($jadwal->mataPelajaran->nama_mapel); ?></td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300"><?php echo e($jadwal->kelas->nama_kelas); ?></td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300"><?php echo e(\Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i')); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-700 dark:text-gray-300">Tidak ada jadwal mengajar untuk hari ini.</p>
                        <?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/guru/jadwal_mengajar/index.blade.php ENDPATH**/ ?>