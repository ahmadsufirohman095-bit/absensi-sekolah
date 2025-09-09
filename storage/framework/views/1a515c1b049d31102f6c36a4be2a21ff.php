<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['sidebarOpen']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['sidebarOpen']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex flex-col h-full">
    <!-- Header Sidebar -->
    <div class="flex items-center justify-between h-20 border-b dark:border-gray-700 flex-shrink-0 px-4"
        :class="{ 'px-4': sidebarOpen, 'px-2': !sidebarOpen }">
        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center space-x-2 overflow-hidden">
            <img class="h-10 w-10 flex-shrink-0" src="<?php echo e(logo_url()); ?>" alt="Logo Sekolah">
            <span class="text-lg font-bold text-gray-800 dark:text-white whitespace-nowrap" x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100">
                <?php echo e(setting('app_name', 'Absensi')); ?>

            </span>
        </a>
    </div>

    <!-- Daftar Menu -->
    <!-- Semua atribut 'data-turbo-frame' dihapus dari link di bawah ini -->
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto" :class="{ 'px-4': sidebarOpen, 'px-2': !sidebarOpen }">
        <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('dashboard'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('dashboard'))]); ?>
             <?php $__env->slot('icon', null, []); ?> 
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
             <?php $__env->endSlot(); ?>
            <?php echo e(__('Dashboard')); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>

        <?php if(auth()->user()->role == 'siswa'): ?>
            <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('siswa.laporan_absensi'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('siswa.laporan_absensi'))]); ?>
                 <?php $__env->slot('icon', null, []); ?> 
                    <i class="fas fa-file-invoice w-6 h-6 flex items-center justify-center"></i>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('Laporan Absensi')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isGuru')): ?>
            <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('guru.jadwal-mengajar.index'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('guru.jadwal-mengajar.index'))]); ?>
                 <?php $__env->slot('icon', null, []); ?> 
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('Jadwal Mengajar')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
        <?php endif; ?>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-absensi')): ?>
            <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('scan.index'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('scan.index'))]); ?>
                 <?php $__env->slot('icon', null, []); ?> 
                    <i class="fas fa-qrcode w-6 h-6 flex items-center justify-center"></i>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('Scan Absensi')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
            
            <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('rekap_absensi.index'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('rekap_absensi.index'))]); ?>
                 <?php $__env->slot('icon', null, []); ?> 
                    <i class="fas fa-file-alt w-6 h-6 flex items-center justify-center"></i>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('Rekap Absensi')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
            
            
            
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isAdmin')): ?>
            <p class="pt-4 text-xs font-semibold text-gray-400 uppercase whitespace-nowrap"
                :class="{ 'px-4': sidebarOpen, 'px-2': !sidebarOpen }" x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100">Manajemen Data</p>
            <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('users.*'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('users.index'))]); ?>
                 <?php $__env->slot('icon', null, []); ?> 
                    <i class="fas fa-users w-6 h-6 flex items-center justify-center"></i>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('Manajemen User')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('kelas.*'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('kelas.index'))]); ?>
                 <?php $__env->slot('icon', null, []); ?> 
                    <i class="fas fa-chalkboard-teacher w-6 h-6 flex items-center justify-center"></i>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('Kelola Kelas')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('mata-pelajaran.*'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('mata-pelajaran.index'))]); ?>
                 <?php $__env->slot('icon', null, []); ?> 
                    <i class="fas fa-book w-6 h-6 flex items-center justify-center"></i>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('Kelola Mapel')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('jadwal.*'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('jadwal.index'))]); ?>
                 <?php $__env->slot('icon', null, []); ?> 
                    <i class="fas fa-calendar-alt w-6 h-6 flex items-center justify-center"></i>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('Kelola Jadwal')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal2e340925a8bf40d3894bf118093fdd54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e340925a8bf40d3894bf118093fdd54 = $attributes; } ?>
<?php $component = App\View\Components\SideNavLink::resolve(['active' => request()->routeIs('pengaturan.index'),'sidebarOpen' => $sidebarOpen] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('side-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SideNavLink::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('pengaturan.index'))]); ?>
                 <?php $__env->slot('icon', null, []); ?> 
                    <i class="fas fa-cog w-6 h-6 flex items-center justify-center"></i>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('Pengaturan')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $attributes = $__attributesOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__attributesOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e340925a8bf40d3894bf118093fdd54)): ?>
<?php $component = $__componentOriginal2e340925a8bf40d3894bf118093fdd54; ?>
<?php unset($__componentOriginal2e340925a8bf40d3894bf118093fdd54); ?>
<?php endif; ?>
        <?php endif; ?>
    </nav>
</div>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>