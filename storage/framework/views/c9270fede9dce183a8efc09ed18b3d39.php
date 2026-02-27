<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(setting('nama_sekolah', config('app.name', 'Laravel'))); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" data-turbo-track="reload">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" data-turbo-track="reload" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans text-gray-900 antialiased h-full">
        <div class="relative min-h-full flex flex-col justify-center items-center p-4 bg-gray-900">
            <!-- Background Image & Overlay -->
            <div class="absolute inset-0 z-0">
                <?php
                    $loginBackground = setting('login_background');
                    $backgroundUrl = asset('images/bg_image.png'); // Gambar default
                    if ($loginBackground && Storage::disk('public')->exists($loginBackground)) {
                        // Jika ada gambar kustom, gunakan itu
                        $backgroundUrl = Storage::url($loginBackground) . '?v=' . Storage::disk('public')->lastModified($loginBackground);
                    }
                ?>
                <img class="w-full h-full object-cover" src="<?php echo e($backgroundUrl); ?>" alt="Latar Belakang Sekolah">
                <div class="absolute inset-0 bg-slate-900/70"></div>
            </div>

            <!-- Konten Form (Slot) -->
            <div class="relative z-10">
                <?php echo e($slot); ?>

            </div>
        </div>
    </body>
</html>
<?php /**PATH /var/www/resources/views/layouts/guest.blade.php ENDPATH**/ ?>