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
            <?php echo e(__("Generate QR Code untuk Semua User")); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg shadow-sm text-center flex flex-col items-center">
                                <div class="mb-4 w-32 h-32 flex items-center justify-center overflow-hidden rounded-full border-2 border-gray-300 dark:border-gray-600 shadow-inner bg-gray-200 dark:bg-gray-600">
                                    <img src="<?php echo e($user->foto_url); ?>" alt="Profile Photo of <?php echo e($user->name); ?>" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1 line-clamp-1"><?php echo e($user->name); ?></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1"><?php echo e($user->identifier); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-300 mb-4">(<?php echo e(ucfirst($user->role)); ?>)</p>

                                <div class="mt-auto w-full">
                                    <div class="mb-4 flex justify-center">
                                        <?php if(!empty($user->qr_code_svg)): ?>
                                            <div class="p-3 bg-white rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm inline-block">
                                                <div class="w-28 h-28 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:block">
                                                    <?php echo $user->qr_code_svg; ?>

                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <a href="<?php echo e(route("users.qr-code.download", $user)); ?>" target="_blank" rel="noopener noreferrer" class="w-full inline-block px-4 py-2.5 bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-indigo-700 transition-colors shadow-md active:transform active:scale-95">
                                        Unduh PNG
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="col-span-full text-center text-gray-500 py-10">Tidak ada user ditemukan.</p>
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
<?php endif; ?><?php /**PATH /home/ruanmei/Dokumen/xampp/htdocs/absensi-sekolah/resources/views/users/qr-generator.blade.php ENDPATH**/ ?>