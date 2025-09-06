<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['active', 'sidebarOpen']));

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

foreach (array_filter((['active', 'sidebarOpen']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$classes = ($active ?? false)
            ? 'flex items-center p-2 text-white bg-indigo-600 rounded-md'
            : 'flex items-center p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md';
?>

<a <?php echo e($attributes->merge(['class' => $classes, 'data-turbo' => 'true'])); ?>>
    <?php if(isset($icon)): ?>
        <span class="flex-shrink-0" :class="{ 'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"><?php echo e($icon); ?></span>
    <?php endif; ?>
    <span class="flex-1 truncate" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"><?php echo e($slot); ?></span>
</a>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/components/side-nav-link.blade.php ENDPATH**/ ?>