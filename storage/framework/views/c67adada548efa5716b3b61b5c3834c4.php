<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['siswa']));

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

foreach (array_filter((['siswa']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
    <div class="flex items-center">
        <img class="h-10 w-10 rounded-full object-cover mr-3" src="<?php echo e($siswa->foto_url); ?>" alt="<?php echo e($siswa->name ?? 'Siswa'); ?>">
        <div>
            <p class="font-semibold text-sm text-gray-800 dark:text-gray-200"><?php echo e($siswa->name ?? 'Siswa tidak ditemukan'); ?></p>
            <?php if($siswa->siswaProfile?->nis): ?>
                <p class="text-xs text-gray-500 dark:text-gray-400">NIS: <?php echo e($siswa->siswaProfile->nis); ?></p>
            <?php else: ?>
                <p class="text-xs text-red-500 dark:text-red-400">Data tidak lengkap</p>
            <?php endif; ?>
        </div>
    </div>
    
</div>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/components/guru/siswa-absen-item.blade.php ENDPATH**/ ?>