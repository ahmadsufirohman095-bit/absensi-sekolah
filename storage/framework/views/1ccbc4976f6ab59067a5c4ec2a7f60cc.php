<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type', 'message']));

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

foreach (array_filter((['type', 'message']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = [
        'success' => 'bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/20 dark:border-green-600 dark:text-green-400',
        'error' => 'bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/20 dark:border-red-600 dark:text-red-400',
    ];
?>

<?php if(session($type)): ?>
    <div <?php echo e($attributes->merge(['class' => 'p-4 mb-4 ' . ($classes[$type] ?? '')])); ?> role="alert">
        <p class="font-bold"><?php echo e(ucfirst($type)); ?></p>
        <p><?php echo e(session($type)); ?></p>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/components/flash-message.blade.php ENDPATH**/ ?>