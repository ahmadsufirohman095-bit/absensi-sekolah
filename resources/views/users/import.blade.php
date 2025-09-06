<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Impor User dari Excel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Menampilkan error validasi dari proses impor -->
                    @if (session('import_errors'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 rounded-lg shadow-md border border-red-200 dark:border-red-800">
                            <p class="font-bold text-red-700 dark:text-red-300">Terdapat beberapa kesalahan dalam file Anda:</p>
                            <ul class="list-disc list-inside mt-2 text-red-600 dark:text-red-400">
                                @foreach (session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="mb-4 text-gray-600 dark:text-gray-400">
                        Silakan unggah file Excel (.xlsx) atau CSV (.csv) untuk mengimpor data user. Pastikan file Anda memiliki kolom heading berikut: <strong>nama, username, email, role</strong>. Password default untuk semua user baru akan diatur menjadi "password".
                    </p>

                    <div class="mb-4">
                        <a href="{{ route('users.importTemplate') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline" data-turbo="false">
                            <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Unduh Template Excel
                        </a>
                    </div>

                    <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <x-input-label for="file" :value="__('Pilih File')" />
                            <input type="file" name="file" id="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 file:hover:bg-gray-300 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm mt-1" required>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 me-4">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Impor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
