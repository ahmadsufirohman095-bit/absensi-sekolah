<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Mata Pelajaran') }}: {{ $mataPelajaran->nama_mapel }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Informasi Mata Pelajaran</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>Kode Mapel:</strong> {{ $mataPelajaran->kode_mapel }}<br>
                            <strong>Nama Mapel:</strong> {{ $mataPelajaran->nama_mapel }}<br>
                            @if ($mataPelajaran->deskripsi)
                                <strong>Deskripsi:</strong> {{ $mataPelajaran->deskripsi }}
                            @else
                                <strong>Deskripsi:</strong> -
                            @endif
                        </p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Guru Pengampu</h3>
                        @forelse ($mataPelajaran->gurus as $guru)
                            <div class="flex items-center mt-2">
                                <img class="h-8 w-8 rounded-full object-cover mr-3" src="{{ $guru->foto_url }}" alt="{{ $guru->name }}">
                                <a href="{{ route('users.show', $guru->id) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">{{ $guru->name }} ({{ $guru->identifier }})</a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada guru yang mengampu mata pelajaran ini.</p>
                        @endforelse
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Diajarkan di Kelas</h3>
                        @forelse ($mataPelajaran->kelas as $kelas)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $kelas->nama_kelas }}</p>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Mata pelajaran ini belum diajarkan di kelas mana pun.</p>
                        @endforelse
                    </div>

                    <div class="mt-8 flex justify-end">
                        <a href="{{ route('mata-pelajaran.edit', $mataPelajaran->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Edit Mata Pelajaran
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
