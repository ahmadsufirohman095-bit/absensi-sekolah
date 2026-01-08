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
            <?php echo e(__('Notifikasi Anda')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <?php if(session('success')): ?>
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="flex justify-end mb-4">
                        <form action="<?php echo e(route('notifications.markAllAsRead')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-blue-700 transition">
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    </div>

                    <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-4 mb-4 border rounded-lg <?php echo e($notification->read_at ? 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600' : 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800'); ?>">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-lg"><?php echo e($notification->data['message'] ?? 'Pesan Notifikasi'); ?></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Dikirim oleh: <?php echo e($notification->data['action_by'] ?? 'Sistem'); ?> pada <?php echo e($notification->created_at->format('d M Y H:i')); ?>

                                    </p>
                                </div>
                                <?php if (! ($notification->read_at)): ?>
                                    <form action="<?php echo e(route('notifications.markAsRead', $notification->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 transition">
                                            Tandai Dibaca
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-center text-gray-600 dark:text-gray-400">Tidak ada notifikasi.</p>
                    <?php endif; ?>

                    <div class="mt-4">
                        <?php echo e($notifications->links()); ?>

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
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/notifications/index.blade.php ENDPATH**/ ?>