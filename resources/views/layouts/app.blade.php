<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-prefetch" content="true">

    <title>{{ setting('app_name', config('app.name', 'Absensi')) }}</title>

        @php
        $faviconPath = setting('favicon', 'favicon.ico');
    @endphp
    <link rel="icon" href="{{ asset($faviconPath . '?v=' . time()) }}">

    <link rel="preconnect" href="https://fonts.bunny.net" data-turbo-track="reload">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" data-turbo-track="reload" />

    {{-- 
            PERUBAHAN DI SINI:
            Link Font Awesome diperbarui dari versi 6.0.0-beta3 ke versi 6.5.2 yang lebih baru dan stabil. 
            Ini akan mengatasi masalah "glyph not found".
        --}}

    

    <style data-turbo-track="reload">
        [x-cloak] {
            display: none !important;
        }
    </style>

    <style data-turbo-track="reload">
        [x-cloak] + .flex-1 {
            transition: margin-left 0.3s ease-in-out;
        }
    </style>

    <script data-turbo-track="reload">
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (theme === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/print.css') }}" media="print" data-turbo-track="reload" />

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js" data-turbo-track="reload"></script>
</head>

<body class="font-sans antialiased">
    <div x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' || (localStorage.getItem('sidebarOpen') === null && window.innerWidth >= 1024) }"
        x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value));"
        class="relative min-h-screen bg-gray-100 dark:bg-gray-900 flex" x-cloak>

        <div @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden" x-show="sidebarOpen" x-transition></div>

        <aside class="fixed inset-y-0 left-0 z-30 transform transition-all duration-300 ease-in-out bg-white dark:bg-gray-800 shadow-lg lg:rounded-br-lg"
            :class="{
                'translate-x-0 w-64': sidebarOpen,
                '-translate-x-full w-20 lg:translate-x-0': !sidebarOpen
            }" data-turbo-permanent="sidebar-navigation" x-cloak>

            @include('layouts.navigation', ['sidebarOpen' => 'sidebarOpen'])
        </aside>

        <div class="flex-1 flex flex-col transition-all duration-300 ease-in-out overflow-hidden"
            :class="{ 'lg:ml-64': sidebarOpen, 'lg:ml-20': !sidebarOpen }">

            <header
                class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 border-b dark:border-gray-700">
                <div class="flex-1">
                    @if (isset($header))
                        <h2 class="ml-4 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            {{ $header }}
                        </h2>
                    @endif
                </div>

                {{-- Dropdown Akun Pengguna --}}
                <div class="flex items-center sm:ms-6">
                    {{-- Tombol Toggle Tema --}}
                    <div x-data="{ darkMode: isDarkMode() }" x-init="$watch('darkMode', value => setTheme(value))" class="flex items-center me-4">
                        <button @click="darkMode = !darkMode"
                            class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition duration-150 ease-in-out"
                            title="Toggle Tema">
                            <template x-if="darkMode">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.325 6.757l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </template>
                            <template x-if="!darkMode">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9 9 0 008.354-5.646z" />
                                </svg>
                            </template>
                        </button>
                    </div>

                    {{-- Notifikasi Dropdown --}}
                    <div class="relative me-4">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition duration-150 ease-in-out">
                                    <i class="fas fa-bell h-6 w-6 inline-flex items-center justify-center"></i>
                                    @if (Auth::user()->unreadNotifications->count() > 0)
                                        <span class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                                            {{ Auth::user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @forelse (Auth::user()->unreadNotifications->take(5) as $notification)
                                    <x-dropdown-link :href="route('notifications.index')" class="{{ $notification->read_at ? '' : 'font-bold' }}">
                                        {{ $notification->data['message'] }}
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                    </x-dropdown-link>
                                @empty
                                    <div class="block px-4 py-2 text-xs text-gray-400">Tidak ada notifikasi baru.</div>
                                @endforelse
                                <x-dropdown-link :href="route('notifications.index')">
                                    Lihat Semua Notifikasi
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <img class="h-8 w-8 rounded-full object-cover me-2" src="{{ Auth::user()->foto_url }}"
                                    alt="{{ Auth::user()->name }}">
                                <div class="hidden md:block">{{ Auth::user()->name }}</div>
                                <div class="ms-1 hidden md:block">
                                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profil Saya') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-full" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Sukses!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <svg @click="show = false" class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                            </span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-full" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <svg @click="show = false" class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                            </span>
                        </div>
                    @endif
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    
    
    @stack('scripts')
</body>

</html>
