@props(['sidebarOpen'])

<div class="flex flex-col h-full">
    <div class="flex items-center justify-start h-20 border-b dark:border-gray-700 flex-shrink-0 pl-4">
        <button @click="sidebarOpen = !sidebarOpen"
            class="text-gray-500 dark:text-gray-300 focus:outline-none">
            <template x-if="sidebarOpen">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18l-6-6 6-6"></path>
                </svg>
            </template>
            <template x-if="!sidebarOpen">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                </svg>
            </template>
        </button>
    </div>
    <!-- Daftar Menu -->
    <!-- Semua atribut 'data-turbo-frame' dihapus dari link di bawah ini -->
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto" :class="{ 'px-4': sidebarOpen, 'px-2': !sidebarOpen }">
        <x-side-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" :sidebarOpen="$sidebarOpen">
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
            </x-slot>
            {{ __('Dashboard') }}
        </x-side-nav-link>

        @php
            $absensiActive = request()->routeIs('siswa.laporan_absensi') ||
                             request()->routeIs('guru.jadwal-mengajar.index') ||
                             request()->routeIs('scan.index') ||
                             request()->routeIs('rekap_absensi.index') ||
                             request()->routeIs('rekap_absensi_pegawai.index'); // Add new route here
        @endphp
        <x-side-nav-dropdown :sidebarOpen="$sidebarOpen" :hasActiveLink="$absensiActive" id="manajemen-absensi">
            <x-slot name="trigger">
                <p class="pt-4 text-xs font-semibold text-gray-400 uppercase whitespace-nowrap"
                    :class="{ 'px-4': sidebarOpen, 'px-2': !sidebarOpen }" x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100">{{ __('Manajemen Absensi') }}</p>
            </x-slot>
            <x-slot name="content">
                @if(auth()->user()->role == 'siswa')
                    <x-side-nav-link :href="route('siswa.laporan_absensi')" :active="request()->routeIs('siswa.laporan_absensi')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-file-invoice w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Laporan Absensi') }}
                    </x-side-nav-link>
                @endif

                @can('isGuru')
                    <x-side-nav-link :href="route('guru.jadwal-mengajar.index')" :active="request()->routeIs('guru.jadwal-mengajar.index')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </x-slot>
                        {{ __('Jadwal Mengajar') }}
                    </x-side-nav-link>
                @endcan
                
                @can('manage-absensi')
                    <x-side-nav-link :href="route('scan.index')" :active="request()->routeIs('scan.index')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-qrcode w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Scan Absensi') }}
                    </x-side-nav-link>
                    
                    <x-side-nav-link :href="route('rekap_absensi.index')" :active="request()->routeIs('rekap_absensi.index')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-file-alt w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Rekap Absensi') }}
                    </x-side-nav-link>

                    <x-side-nav-link :href="route('rekap_absensi_pegawai.index')" :active="request()->routeIs('rekap_absensi_pegawai.index')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-user-tie w-6 h-6 flex items-center justify-center"></i> <!-- Icon for employee rekap -->
                        </x-slot>
                        {{ __('Rekap Absensi Pegawai') }}
                    </x-side-nav-link>
                @endcan
            </x-slot>
        </x-side-nav-dropdown>

        @can('isAdmin')
            @php
                $manajemenDataActive = request()->routeIs('users.*') ||
                                       request()->routeIs('kelas.*') ||
                                       request()->routeIs('mata-pelajaran.*');
            @endphp
            <x-side-nav-dropdown :sidebarOpen="$sidebarOpen" :hasActiveLink="$manajemenDataActive" id="manajemen-data">
                <x-slot name="trigger">
                    <p class="pt-4 text-xs font-semibold text-gray-400 uppercase whitespace-nowrap"
                        :class="{ 'px-4': sidebarOpen, 'px-2': !sidebarOpen }" x-show="sidebarOpen"
                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100">{{ __('Manajemen Data') }}</p>
                </x-slot>
                <x-slot name="content">
                    <x-side-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-users w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Manajemen User') }}
                    </x-side-nav-link>
                    <x-side-nav-link :href="route('kelas.index')" :active="request()->routeIs('kelas.*')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-chalkboard-teacher w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Kelola Kelas') }}
                    </x-side-nav-link>
                    <x-side-nav-link :href="route('mata-pelajaran.index')" :active="request()->routeIs('mata-pelajaran.*')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-book w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Kelola Mapel') }}
                    </x-side-nav-link>
                </x-slot>
            </x-side-nav-dropdown>

            @php
                $manajemenJadwalActive = request()->routeIs('jadwal.*') ||
                                         request()->routeIs('jadwal-absensi-pegawai.*');
            @endphp
            <x-side-nav-dropdown :sidebarOpen="$sidebarOpen" :hasActiveLink="$manajemenJadwalActive" id="manajemen-jadwal">
                <x-slot name="trigger">
                    <p class="pt-4 text-xs font-semibold text-gray-400 uppercase whitespace-nowrap"
                        :class="{ 'px-4': sidebarOpen, 'px-2': !sidebarOpen }" x-show="sidebarOpen"
                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100">{{ __('Manajemen Jadwal') }}</p>
                </x-slot>
                <x-slot name="content">
                    <x-side-nav-link :href="route('jadwal.index')" :active="request()->routeIs('jadwal.*')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-calendar-alt w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Kelola Jadwal Pelajaran') }}
                    </x-side-nav-link>
                    <x-side-nav-link :href="route('jadwal-absensi-pegawai.index')" :active="request()->routeIs('jadwal-absensi-pegawai.*')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-calendar-check w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Kelola Jadwal Absensi Pegawai') }}
                    </x-side-nav-link>
                </x-slot>
            </x-side-nav-dropdown>

            @php
                $pengaturanUtilitasActive = request()->routeIs('pengaturan.index') ||
                                            request()->routeIs('print-cards.index') ||
                                            request()->routeIs('pengaturan.faq');
            @endphp
            <x-side-nav-dropdown :sidebarOpen="$sidebarOpen" :hasActiveLink="$pengaturanUtilitasActive" id="pengaturan-utilitas">
                <x-slot name="trigger">
                    <p class="pt-4 text-xs font-semibold text-gray-400 uppercase whitespace-nowrap"
                        :class="{ 'px-4': sidebarOpen, 'px-2': !sidebarOpen }" x-show="sidebarOpen"
                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100">{{ __('Pengaturan & Utilitas') }}</p>
                </x-slot>
                <x-slot name="content">
                    <x-side-nav-link :href="route('pengaturan.index')" :active="request()->routeIs('pengaturan.index')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-cog w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Pengaturan') }}
                    </x-side-nav-link>

                    <x-side-nav-link :href="route('print-cards.index')" :active="request()->routeIs('print-cards.index')" :sidebarOpen="$sidebarOpen" class="pl-4">
                        <x-slot name="icon">
                            <i class="fas fa-id-card w-6 h-6 flex items-center justify-center"></i>
                        </x-slot>
                        {{ __('Cetak Kartu Absensi') }}
                    </x-side-nav-link>
                    {{-- Tautan FAQ untuk semua peran terotentikasi, di bagian paling bawah --}}
                    @if(in_array(auth()->user()->role, ['admin', 'guru', 'siswa']))
                        <x-side-nav-link :href="route('pengaturan.faq')" :active="request()->routeIs('pengaturan.faq')" :sidebarOpen="$sidebarOpen" class="pl-4">
                            <x-slot name="icon">
                                <i class="fas fa-question-circle w-6 h-6 flex items-center justify-center"></i>
                            </x-slot>
                            {{ __('FAQ') }}
                        </x-side-nav-link>
                    @endif
                </x-slot>
            </x-side-nav-dropdown>
        @endcan

        {{-- Tautan FAQ untuk semua peran terotentikasi, di bagian paling bawah (akan dipindahkan ke dropdown) --}}
        
    </nav>
</div>
