<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Kelola Kelas') }}
            </h2>
            <div class="flex flex-wrap items-center justify-start md:justify-end gap-2">
                <a href="{{ route('kelas.create') }}"
                    class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-indigo-700">
                    Tambah Kelas Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ isModalOpen: false, modalSiswa: [], modalKelasName: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div
                            class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Search and Filter Form --}}
                    <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm">
                        <form action="{{ route('kelas.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="flex-grow w-full md:w-auto">
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Nama Kelas:</label>
                                <input type="text" name="search" id="search"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                    placeholder="Cari kelas..." value="{{ request('search') }}">
                            </div>
                            <div class="flex-grow w-full md:w-auto">
                                <label for="wali_kelas_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Wali Kelas:</label>
                                <select name="wali_kelas_id" id="wali_kelas_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    <option value="">Semua Wali Kelas</option>
                                    @foreach ($gurus as $guru)
                                        <option value="{{ $guru->id }}" {{ request('wali_kelas_id') == $guru->id ? 'selected' : '' }}>
                                            {{ $guru->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full md:w-auto">
                                <button type="submit"
                                    class="w-full md:w-auto px-4 py-2 bg-indigo-600 text-white text-sm font-semibold uppercase rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                    Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 hidden sm:table-header-group">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Kelas</th>
                                    <th scope="col" class="px-6 py-3">Wali Kelas</th>
                                    <th scope="col" class="px-6 py-3 text-center">Jumlah Siswa</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block sm:table-row-group">
                                @forelse($kelas as $item)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 block sm:table-row mb-4 sm:mb-0 rounded-lg shadow-md sm:shadow-none">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">Nama Kelas: </span>
                                            <a href="{{ route('kelas.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                                {{ $item->nama_kelas }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">Wali Kelas: </span>
                                            @if ($item->waliKelas)
                                                <div class="flex items-center">
                                                    <img class="h-8 w-8 rounded-full object-cover mr-2"
                                                        src="{{ $item->waliKelas->foto_url }}"
                                                        alt="{{ $item->waliKelas->name }}">
                                                    <span>{{ $item->waliKelas->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">- Belum diatur -</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">Jumlah Siswa: </span>{{ $item->siswa_profiles_count }}
                                        </td>
                                        <td class="px-6 py-4 text-right block sm:table-cell" x-data="{ open: false, confirmDelete: false }">
                                            <div x-show="!confirmDelete" class="relative inline-block text-left">
                                                <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="options-menu" aria-haspopup="true" aria-expanded="true">
                                                    Aksi
                                                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>

                                                <div x-show="open" x-cloak
                                                     class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50">
                                                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                                        
                                                        <a href="{{ route('kelas.edit', $item) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" role="menuitem">
                                                            <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z" />
                                                            </svg>
                                                            <span>Edit</span>
                                                        </a>
                                                        <a href="{{ route('kelas.printCards', ['kelas_id' => $item->id]) }}" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" role="menuitem">
                                                            <svg class="mr-3 h-5 w-5 text-purple-500" xmlns="http://www.w3.org/20.svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l-3-3m0 0l3-3m-3 3h6" />
                                                            </svg>
                                                            <span>Cetak Kartu</span>
                                                        </a>
                                                        <button type="button" @click="confirmDelete = true; open = false"
                                                            class="w-full text-left flex items-center px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600"
                                                            :class="{'opacity-50 cursor-not-allowed': {{ $item->siswa_profiles_count }} > 0}"
                                                            x-bind:disabled="{{ $item->siswa_profiles_count }} > 0"
                                                            title="{{ $item->siswa_profiles_count > 0 ? 'Kelas tidak bisa dihapus karena masih ada siswa.' : 'Hapus kelas' }}">
                                                            <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tombol Konfirmasi Hapus --}}
                                            <div x-show="confirmDelete" x-cloak class="flex items-center justify-end sm:justify-end space-x-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Yakin?</span>
                                                <button @click="confirmDelete = false" class="px-2 py-1 text-xs text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">Batal</button>
                                                <form class="inline-block" action="{{ route('kelas.destroy', $item) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 text-xs text-white bg-red-600 hover:bg-red-700 rounded-md">Ya, Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 block sm:table-row">
                                        <td colspan="4" class="px-6 py-4 text-center block sm:table-cell">Belum ada data kelas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{ $kelas->links() }}
                    </div>

                </div>
            </div>
        </div>

        <!-- Siswa List Modal -->
        <div x-show="isModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="isModalOpen = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <!-- Modal Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="modal-title">Daftar Siswa Kelas <span x-text="modalKelasName" class="text-indigo-500"></span></h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Total: <span x-text="modalSiswa.length"></span> siswa</p>
                        </div>
                        <button @click="isModalOpen = false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <ul class="space-y-3">
                        <template x-for="siswaProfile in modalSiswa" :key="siswaProfile.id">
                            <li class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <img class="h-10 w-10 rounded-full object-cover mr-4" :src="siswaProfile.user.foto_url" :alt="siswaProfile.user.name">
                                <div>
                                    <p x-text="siswaProfile.user.name" class="font-semibold text-gray-900 dark:text-gray-100"></p>
                                    <p x-text="siswaProfile.nis ? 'NIS: ' + siswaProfile.nis : 'NIS: -'" class="text-sm text-gray-600 dark:text-gray-400"></p>
                                </div>
                            </li>
                        </template>
                        <template x-if="modalSiswa.length === 0">
                            <li class="text-center p-6 text-gray-500 dark:text-gray-400">
                                Belum ada siswa di kelas ini.
                            </li>
                        </template>
                    </ul>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end p-6 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                    <button @click="isModalOpen = false" type="button" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>