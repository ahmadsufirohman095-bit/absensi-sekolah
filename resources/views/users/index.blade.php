<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{-- Judul Dinamis --}}
                @if(isset($kelasFilter))
                    {{ __('Daftar Siswa Kelas: ') . $kelasFilter->nama_kelas }}
                @else
                    {{ __('Manajemen User') }}
                @endif
            </h2>
            <div class="flex flex-wrap items-center justify-start md:justify-end gap-2">
                
                <a href="{{ route('users.export', request()->query()) }}" data-turbo="false" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-blue-700 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Ekspor</a>
                <a href="{{ route('users.import.form') }}" class="px-4 py-2 bg-green-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-green-700 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Impor</a>
                <a href="{{ route('users.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-indigo-700 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Tambah User</a>
            </div>
        </div>
    </x-slot>

    <turbo-frame id="main-content">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100" x-data="userManagement()" x-init="init({{ json_encode($users->getCollection()->pluck('id')) }})">
                        
                        <!-- Form Filter & Pencarian -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <form method="GET" action="{{ route('users.index') }}">
                                {{-- Menyimpan parameter kelas_id jika ada --}}
                                @if(request('kelas_id'))
                                    <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                                    <input type="hidden" name="role" value="siswa">
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    {{-- Input Pencarian --}}
                                    <div class="md:col-span-2">
                                        <label for="search" class="sr-only">Cari</label>
                                        <input type="text" name="search" id="search" placeholder="Cari berdasarkan Nama, Email, atau ID..." value="{{ request('search') }}"
                                               class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    {{-- Filter Kelas --}}
                                    <select name="kelas_id" id="kelas_id" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Semua Kelas</option>
                                        @foreach($allKelas as $kelas)
                                            <option value="{{ $kelas->id }}" @selected(request('kelas_id') == $kelas->id)>{{ $kelas->nama_kelas }}</option>
                                        @endforeach
                                    </select>

                                    {{-- Filter Role & Tombol --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @if(!request('kelas_id'))
                                        <select name="role" id="role" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Semua Role</option>
                                            <option value="admin" @selected(request('role') == 'admin')>Admin</option>
                                            <option value="guru" @selected(request('role') == 'guru')>Guru</option>
                                            <option value="siswa" @selected(request('role') == 'siswa')>Siswa</option>
                                        </select>
                                        @endif
                                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if (session('success'))
                            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-lg">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Form Aksi Massal -->
                        <form x-ref="bulkActionForm" action="{{ route('users.bulkToggleStatus') }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" x-model="bulkAction">
                            <template x-for="userId in selectedUsers" :key="userId">
                                <input type="hidden" name="user_ids[]" :value="userId">
                            </template>
                        </form>

                        <!-- Panel Aksi Massal -->
                        <div x-show="selectedUsers.length > 0" x-cloak class="bg-gray-100 dark:bg-gray-700/50 p-4 rounded-lg mb-4 flex items-center justify-between">
                            <div>
                                <span x-text="selectedUsers.length"></span> pengguna terpilih.
                            </div>
                            <div class="flex items-center space-x-3">
                                <select x-model="bulkAction" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Pilih Aksi</option>
                                    <option value="activate">Aktifkan yang Dipilih</option>
                                    <option value="deactivate">Nonaktifkan yang Dipilih</option>
                                    <option value="delete">Hapus yang Dipilih</option>
                                </select>
                                <button @click="submitBulkAction" :disabled="!bulkAction" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terapkan</button>
                            </div>
                        </div>

                        <!-- Tabel User -->
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-sm text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400 hidden sm:table-header-group">
                                    <tr>
                                        <th scope="col" class="p-4">
                                            <div class="flex items-center">
                                                <input id="checkbox-all-search" type="checkbox" @click="toggleSelectAll($event)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3">Pengguna</th>
                                        <th scope="col" class="px-6 py-3">ID (NIS/NIP)</th>
                                        <th scope="col" class="px-6 py-3">Role</th>
                                        <th scope="col" class="px-6 py-3">Status Aktif</th>
                                        <th scope="col" class="px-6 py-3">Login Terakhir</th>
                                        <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="block sm:table-row-group">
                                    @forelse ($users as $user)
                                    <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 block sm:table-row mb-4 sm:mb-0 rounded-lg shadow-md sm:shadow-none transition duration-150 ease-in-out">
                                        <td class="w-4 p-4">
                                            @if(in_array($user->role, ['guru', 'siswa']))
                                            <div class="flex items-center">
                                                <input type="checkbox" :value="{{ $user->id }}" x-model="selectedUsers" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white block sm:table-cell">
                                            <div class="flex items-center">
                                                <img class="w-10 h-10 rounded-full object-cover mr-4" src="{{ $user->foto_url }}" alt="{{ $user->name }}">
                                                <div>
                                                    <div class="text-base font-semibold"><a href="{{ route('users.show', $user->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $user->name }}</a></div>
                                                    <div class="font-normal text-gray-500">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">ID (NIS/NIP): </span>{{ $user->identifier }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">Role: </span>
                                            @php
                                                $roleColors = [
                                                    'admin' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300',
                                                    'guru'  => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                                    'siswa' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                                ];
                                                $roleClasses = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClasses }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">Status Aktif: </span>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' }}">
                                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">Login Terakhir: </span>{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum pernah login' }}
                                        </td>
                                        <td class="px-6 py-4 text-right block sm:table-cell">
                                            <div class="relative flex items-center justify-end sm:justify-end space-x-2">
                                                <x-dropdown align="right" width="32" contentClasses="py-1 bg-white dark:bg-gray-700 rounded-md shadow-lg">
                                                    <x-slot name="trigger">
                                                        <button class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                            Aksi <svg class="ms-1 fill-current h-4 w-4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                        </button>
                                                    </x-slot>
                                                    <x-slot name="content">
                                                        <x-dropdown-link :href="route('users.edit', $user->id)" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z" /></svg> Edit
                                                        </x-dropdown-link>
                                                        @if($user->role === 'siswa')
                                                            <x-dropdown-link :href="route('users.cetak-satu-kartu', $user->id)" target="_blank" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            <svg class="mr-3 h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l-3-3m0 0l3-3m-3 3h6" /></svg> Cetak Kartu
                                                        </x-dropdown-link>
                                                        @endif
                                                        
                                                        {{-- Tombol Toggle Status --}}
                                                        @if(in_array($user->role, ['guru', 'siswa']))
                                                        <button type="button" x-on:click="$dispatch('open-modal', 'confirm-toggle-status-{{ $user->id }}')" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                                            @if($user->is_active)
                                                        <svg class="mr-3 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2A9 9 0 111 10a9 9 0 0118 0z" />
                                                        </svg> Nonaktifkan
                                                        @else
                                                        <svg class="mr-3 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg> Aktifkan
                                                        @endif
                                                        </button>
                                                        @endif

                                                        {{-- Tombol Hapus --}}
                                                        <button x-on:click="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->id }}')" class="block w-full px-4 py-2 text-start text-sm leading-5 text-red-700 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/50 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                                            <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg> Hapus
                                                        </button>
                                                    </x-slot>
                                                </x-dropdown>
                                            </div>

                                            {{-- Modal Konfirmasi Toggle Status --}}
                                            <x-modal name="confirm-toggle-status-{{ $user->id }}" :show="$errors->any()" focusable>
                                                <form method="post" action="{{ route('users.toggleStatus', $user->id) }}" class="p-6" x-on:submit="$dispatch('close')">
                                                    @csrf
                                                    @method('patch')
                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                        Konfirmasi Perubahan Status
                                                    </h2>
                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                        Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun <span class="font-bold">{{ $user->name }}</span>?
                                                    </p>
                                                    <div class="mt-6 flex justify-end">
                                                        <x-secondary-button x-on:click="$dispatch('close')">
                                                            Batal
                                                        </x-secondary-button>
                                                        <x-primary-button class="ms-3">
                                                            Ya, Lanjutkan
                                                        </x-primary-button>
                                                    </div>
                                                </form>
                                            </x-modal>

                                            <x-modal name="confirm-user-deletion-{{ $user->id }}" :show="$errors->any()" focusable>
                                                <form method="post" action="{{ route('users.destroy', $user->id) }}" class="p-6" x-on:submit="$dispatch('close')">
                                                    @csrf
                                                    @method('delete')
                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                        Konfirmasi Hapus Pengguna
                                                    </h2>
                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                        Apakah Anda yakin ingin menghapus akun <span class="font-bold">{{ $user->name }}</span>? Tindakan ini tidak dapat dibatalkan.
                                                    </p>
                                                    <div class="mt-6 flex justify-end">
                                                        <x-secondary-button x-on:click="$dispatch('close')">
                                                            Batal
                                                        </x-secondary-button>
                                                        <x-danger-button class="ms-3">
                                                            Yakin, Hapus
                                                        </x-danger-button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 block sm:table-row">
                                        <td colspan="7" class="px-6 py-4 text-center block sm:table-cell">Tidak ada data user yang cocok.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>

                    <script>
                        function userManagement() {
                            return {
                                selectedUsers: [],
                                bulkAction: '',
                                allUserIds: [],
                                init(ids) {
                                    this.allUserIds = ids;
                                },
                                toggleSelectAll(event) {
                                    if (event.target.checked) {
                                        this.selectedUsers = [...this.allUserIds];
                                    } else {
                                        this.selectedUsers = [];
                                    }
                                },
                                submitBulkAction() {
                                    if (this.bulkAction && this.selectedUsers.length > 0) {
                                        const form = this.$refs.bulkActionForm;
                                        if (this.bulkAction === 'delete') {
                                            if (!confirm('Apakah Anda yakin ingin menghapus pengguna yang dipilih? Tindakan ini tidak dapat dibatalkan.')) {
                                                return; // User cancelled
                                            }
                                            form.action = '{{ route("users.bulkDestroy") }}';
                                            form.method = 'POST'; // Laravel uses POST for DELETE via _method field
                                            // Add a hidden _method field for DELETE
                                            let methodField = form.querySelector('input[name="_method"]');
                                            if (!methodField) {
                                                methodField = document.createElement('input');
                                                methodField.type = 'hidden';
                                                methodField.name = '_method';
                                                form.appendChild(methodField);
                                            }
                                            methodField.value = 'DELETE';
                                        } else {
                                            form.action = '{{ route("users.bulkToggleStatus") }}';
                                            form.method = 'POST';
                                            // Remove _method field if it exists
                                            let methodField = form.querySelector('input[name="_method"]');
                                            if (methodField) {
                                                form.removeChild(methodField);
                                            }
                                        }
                                        form.submit();
                                    }
                                }
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </turbo-frame>
</x-app-layout>
