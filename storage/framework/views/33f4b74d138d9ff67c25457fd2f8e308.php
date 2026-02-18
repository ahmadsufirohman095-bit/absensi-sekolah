<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['sidebarOpen', 'hasActiveLink', 'id']));

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

foreach (array_filter((['sidebarOpen', 'hasActiveLink', 'id']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="{ open: (localStorage.getItem('dropdown-' + '<?php echo e($id); ?>') === 'true' ? true : (localStorage.getItem('dropdown-' + '<?php echo e($id); ?>') === 'false' ? false : <?php echo e($hasActiveLink ? 'true' : 'false'); ?>)), init() { this.$watch('open', value => localStorage.setItem('dropdown-' + '<?php echo e($id); ?>', value)); } }" class="relative">
    <div @click="open = !open" class="flex items-center justify-between cursor-pointer py-2 px-4 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">
        <?php echo e($trigger); ?>

        <span class="flex-shrink-0 ml-auto transition-transform duration-200" :class="{ 'rotate-90': open }" x-cloak>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </span>
    </div>

    <div x-show="open" x-collapse.duration.300ms class="mt-1 space-y-2" :class="{ 'pl-6': sidebarOpen }" x-cloak>
        <?php echo e($content); ?>

    </div>
</div>
<?php /**PATH /home/ruanmei/Dokumen/xampp/htdocs/absensi-sekolah/resources/views/components/side-nav-dropdown.blade.php ENDPATH**/ ?>