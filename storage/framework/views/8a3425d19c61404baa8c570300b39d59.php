<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['jadwal']));

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

foreach (array_filter((['jadwal']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-indigo-500/50">
    <div class="flex items-center">
        <div class="mr-4">
            <?php if($jadwal->absensi_masuk_tepat_waktu): ?>
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-800/50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            <?php elseif($jadwal->absensi_masuk_terlambat): ?>
                <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-800/50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            <?php else: ?>
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <p class="font-bold text-gray-800 dark:text-gray-100"><?php echo e($jadwal->mataPelajaran->nama_mapel); ?></p>
            <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e($jadwal->kelas->nama_kelas); ?></p>
            <p class="text-sm text-gray-500 dark:text-gray-300 flex items-center mt-1">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path></svg>
                <?php echo e(\Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i')); ?>

            </p>
        </div>
    </div>
    <div>
        <?php if(!$jadwal->absensi_masuk): ?>
            <a href="<?php echo e(route('scan.index', ['jadwal_id' => $jadwal->id])); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Mulai Absensi
            </a>
        <?php else: ?>
            <span class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                Selesai
            </span>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/components/guru/jadwal-card.blade.php ENDPATH**/ ?>