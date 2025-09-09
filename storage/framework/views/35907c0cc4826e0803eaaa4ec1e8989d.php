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
            <?php echo e(__('Dashboard Admin')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 xl:px-12">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="p-8 text-white">
                    <h3 class="text-3xl font-extrabold mb-2">Selamat Datang, <?php echo e(Auth::user()->name); ?>!</h3>
                    <p class="text-indigo-100">Ini adalah ringkasan aktivitas sistem Anda. Jelajahi data terbaru di bawah ini.</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg flex items-center justify-between transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Pengguna</h3>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1"><?php echo e($totalUsers); ?></p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-full">
                        <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H2v-2a3 3 0 015.356-1.857m7.5 1.857A2.75 2.75 0 0019 16.5a2.75 2.75 0 00-2.75-2.75h-1.5a2.75 2.75 0 00-2.75 2.75c0 1.619 1.102 2.966 2.5 3.249M10 12a2 2 0 100-4 2 2 0 000 4zm-7 8h14a2 2 0 002-2V6a2 2 0 00-2-2H3a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg flex items-center justify-between transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Kelas</h3>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1"><?php echo e($totalKelas); ?></p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-full">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18a2 2 0 002-2V6a2 2 0 00-2-2H3a2 2 0 00-2 2v2a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg flex items-center justify-between transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Mata Pelajaran</h3>
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1"><?php echo e($totalMataPelajaran); ?></p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/50 rounded-full">
                        <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg flex items-center justify-between transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Jadwal Absensi</h3>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1"><?php echo e($totalJadwalAbsensi); ?></p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-full">
                        <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center"><svg class="h-6 w-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M12 20.052v-8.69M9 15h6"></path></svg> Distribusi Pengguna</h3>
                    <canvas id="userDistributionChart"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center"><svg class="h-6 w-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg> Kehadiran Hari Ini <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(Data Hari Ini)</span></h3>
                    <canvas id="attendanceChart"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center"><svg class="h-6 w-6 mr-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg> Absensi Bulanan</h3>
                    <canvas id="monthlyAbsenceChart"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center"><svg class="h-6 w-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg> Siswa per Kelas</h3>
                    <canvas id="studentsPerClassChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    
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
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>