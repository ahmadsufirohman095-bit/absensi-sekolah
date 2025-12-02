@props(['sidebarOpen', 'hasActiveLink', 'id'])

<div x-data="{ open: (localStorage.getItem('dropdown-' + '{{ $id }}') === 'true' ? true : (localStorage.getItem('dropdown-' + '{{ $id }}') === 'false' ? false : {{ $hasActiveLink ? 'true' : 'false' }})), init() { this.$watch('open', value => localStorage.setItem('dropdown-' + '{{ $id }}', value)); } }" class="relative">
    <div @click="open = !open" class="flex items-center justify-between cursor-pointer py-2 px-4 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">
        {{ $trigger }}
        <span class="flex-shrink-0 ml-auto transition-transform duration-200" :class="{ 'rotate-90': open }" x-cloak>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </span>
    </div>

    <div x-show="open" x-collapse.duration.300ms class="mt-1 space-y-2" :class="{ 'pl-6': sidebarOpen }" x-cloak>
        {{ $content }}
    </div>
</div>
