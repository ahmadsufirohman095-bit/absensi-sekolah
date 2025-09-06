<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Jadwal Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="jadwalCreateManager">
                    <h3 class="text-lg font-medium mb-6">Form Tambah Jadwal Absensi</h3>

                    <form action="{{ route('jadwal.store') }}" method="POST">
                        @csrf
                        <div id="schedule-items-container" class="space-y-8" x-ref="container">
                            {{-- Item Jadwal Pertama --}}
                            @include('jadwal.partials.schedule-item', ['index' => 0])
                        </div>

                        <div class="flex items-center justify-between mt-8">
                            <button type="button" x-on:click="addScheduleItem"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Jadwal Lain
                            </button>
                            <div class="flex items-center">
                                <a href="{{ route('jadwal.index') }}"
                                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md">
                                    Batal
                                </a>
                                <x-primary-button class="ml-4">
                                    {{ __('Simpan Semua Jadwal') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Template untuk item jadwal baru (disembunyikan) --}}
    <template id="schedule-item-template" x-ref="template">
        @include('jadwal.partials.schedule-item', ['index' => '__INDEX__'])
    </template>

    @push('scripts')
    <style>
        .dark .ts-control {
            background-color: #1f2937 !important; /* bg-gray-800 */
            border-color: #4b5563 !important; /* border-gray-600 */
        }
        .dark .ts-control input,
        .dark .ts-control .item {
            color: #d1d5db !important; /* text-gray-300 */
        }
        .dark .ts-control .item {
            background-color: #374151 !important; /* bg-gray-700 */
        }
        .dark .ts-dropdown {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
        }
        .dark .ts-dropdown .option {
            color: #d1d5db !important;
        }
        .dark .ts-dropdown .option:hover,
        .dark .ts-dropdown .option.active {
            background-color: #4b5563 !important;
        }
    </style>
    @endpush
</x-app-layout>
