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
            <?php echo e(__('Detail Profil Pengguna')); ?>: <?php echo e($user->name); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <turbo-frame id="main-content">
        <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex items-center space-x-6 mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow-lg">
                        <img class="h-28 w-28 rounded-full object-cover border-4 border-indigo-500 shadow-md" src="<?php echo e($user->foto_url); ?>" alt="<?php echo e($user->name); ?>">
                        <div>
                            <h3 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100"><?php echo e($user->name); ?></h3>
                            <p class="text-md text-gray-600 dark:text-gray-400">@ <?php echo e($user->username); ?></p>
                            <p class="text-md text-gray-600 dark:text-gray-400"><?php echo e($user->email); ?></p>
                            <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-2">Peran: <?php echo e(ucfirst($user->role)); ?></p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-5">Informasi Umum</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base">
                            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>ID Pengguna:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->id); ?></span></div>
                            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Identifier:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->identifier); ?></span></div>
                            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Status Akun:</strong> <span class="font-bold <?php echo e($user->is_active ? 'text-green-600' : 'text-red-600'); ?>"><?php echo e($user->is_active ? 'Aktif' : 'Nonaktif'); ?></span></div>
                            
                        </div>
                    </div>

                    <?php if($user->role === 'admin' && $user->adminProfile): ?>
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-5">Detail Profil Admin</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base">
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Jabatan:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->adminProfile->jabatan ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Telepon:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->adminProfile->telepon ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Tanggal Bergabung:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->adminProfile->tanggal_bergabung ? \Carbon\Carbon::parse($user->adminProfile->tanggal_bergabung)->translatedFormat('d F Y') : '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Tempat Lahir:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->adminProfile->tempat_lahir ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Jenis Kelamin:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->adminProfile->jenis_kelamin ? ucfirst($user->adminProfile->jenis_kelamin) : '-'); ?></span></div>
                            </div>
                        </div>
                    <?php elseif($user->role === 'guru' && $user->guruProfile): ?>
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-5">Detail Profil Guru</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base">
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Jabatan:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->guruProfile->jabatan ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Telepon:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->guruProfile->telepon ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Tanggal Lahir:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->guruProfile->tanggal_lahir ? \Carbon\Carbon::parse($user->guruProfile->tanggal_lahir)->translatedFormat('d F Y') : '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Jenis Kelamin:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->guruProfile->jenis_kelamin ? ucfirst($user->guruProfile->jenis_kelamin) : '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Tempat Lahir:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->guruProfile->tempat_lahir ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Alamat:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->guruProfile->alamat ?? '-'); ?></span></div>
                            </div>
                        </div>
                        <?php if($user->mataPelajarans->isNotEmpty()): ?>
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Mata Pelajaran yang Diajar</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400">
                                    <?php $__currentLoopData = $user->mataPelajarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mapel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($mapel->nama_mapel); ?> (<?php echo e($mapel->kode_mapel); ?>)</li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    <?php elseif($user->role === 'siswa' && $user->siswaProfile): ?>
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-5">Detail Profil Siswa</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base">
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>NIS:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->nis ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Kelas:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->kelas->nama_kelas ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Tanggal Lahir:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->tanggal_lahir ? \Carbon\Carbon::parse($user->siswaProfile->tanggal_lahir)->translatedFormat('d F Y') : '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Jenis Kelamin:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->jenis_kelamin ? ucfirst($user->siswaProfile->jenis_kelamin) : '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Tempat Lahir:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->tempat_lahir ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Alamat:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->alamat ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Nama Ayah:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->nama_ayah ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Telepon Ayah:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->telepon_ayah ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Nama Ibu:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->nama_ibu ?? '-'); ?></span></div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md shadow-sm"><strong>Telepon Ibu:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($user->siswaProfile->telepon_ibu ?? '-'); ?></span></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-8 flex justify-between items-center">
                        <a href="<?php echo e(route('users.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            Kembali
                        </a>
                        <a href="<?php echo e(route('users.edit', $user->id)); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Edit Profil
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </turbo-frame>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/users/show.blade.php ENDPATH**/ ?>