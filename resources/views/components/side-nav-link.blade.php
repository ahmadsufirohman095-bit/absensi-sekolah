@props(['active', 'sidebarOpen'])

@php
$classes = ($active ?? false)
            ? 'flex items-center p-2 text-white bg-indigo-600 rounded-md'
            : 'flex items-center p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'data-turbo' => 'true']) }}>
    @if (isset($icon))
        <span class="flex-shrink-0" :class="{ 'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }">{{ $icon }}</span>
    @endif
    <span class="flex-1 truncate" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">{{ $slot }}</span>
</a>
