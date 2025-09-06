<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('nama_sekolah', config('app.name', 'Laravel')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" data-turbo-track="reload">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" data-turbo-track="reload" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased h-full">
        <div class="relative min-h-full flex flex-col justify-center items-center p-4 bg-gray-900">
            <!-- Background Image & Overlay -->
            <div class="absolute inset-0 z-0">
                @php
                    $loginBackground = setting('login_background');
                    $backgroundUrl = asset('images/bg_image.png'); // Gambar default
                    if ($loginBackground && Storage::disk('public')->exists($loginBackground)) {
                        // Jika ada gambar kustom, gunakan itu
                        $backgroundUrl = Storage::url($loginBackground) . '?v=' . Storage::disk('public')->lastModified($loginBackground);
                    }
                @endphp
                <img class="w-full h-full object-cover" src="{{ $backgroundUrl }}" alt="Latar Belakang Sekolah">
                <div class="absolute inset-0 bg-slate-900/70"></div>
            </div>

            <!-- Konten Form (Slot) -->
            <div class="relative z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
