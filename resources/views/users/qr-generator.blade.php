<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("Generate QR Code untuk Semua User") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($users as $user)
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg shadow-sm text-center flex flex-col items-center">
                                <div class="mb-4 w-32 h-32 flex items-center justify-center overflow-hidden rounded-full border-2 border-gray-300 dark:border-gray-600 shadow-inner bg-gray-200 dark:bg-gray-600">
                                    <img src="{{ $user->foto_url }}" alt="Profile Photo of {{ $user->name }}" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1 line-clamp-1">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ $user->identifier }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-300 mb-4">({{ ucfirst($user->role) }})</p>

                                <div class="mt-auto w-full">
                                    <div class="mb-4 flex justify-center">
                                        @if (!empty($user->qr_code_svg))
                                            <div class="p-3 bg-white rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm inline-block">
                                                <div class="w-28 h-28 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:block">
                                                    {!! $user->qr_code_svg !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <a href="{{ route("users.qr-code.download", $user) }}" target="_blank" rel="noopener noreferrer" class="w-full inline-block px-4 py-2.5 bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-indigo-700 transition-colors shadow-md active:transform active:scale-95">
                                        Unduh PNG
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-full text-center text-gray-500 py-10">Tidak ada user ditemukan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>