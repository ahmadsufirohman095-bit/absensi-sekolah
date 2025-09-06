@props(['type', 'message'])

@php
    $classes = [
        'success' => 'bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/20 dark:border-green-600 dark:text-green-400',
        'error' => 'bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/20 dark:border-red-600 dark:text-red-400',
    ];
@endphp

@if (session($type))
    <div {{ $attributes->merge(['class' => 'p-4 mb-4 ' . ($classes[$type] ?? '')]) }} role="alert">
        <p class="font-bold">{{ ucfirst($type) }}</p>
        <p>{{ session($type) }}</p>
    </div>
@endif
