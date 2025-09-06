<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (Auth::user()->role === 'admin')
                <!-- Admin Dashboard Content -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-2xl font-bold mb-4">Dashboard Admin</h3>
                        <p class="mb-4">Selamat datang, Admin! Berikut adalah gambaran umum data absensi sekolah.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                            <!-- Statistik Card 1 -->
                            <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg shadow-md">
                                <h4 class="font-semibold text-lg text-blue-800 dark:text-blue-200">Total Siswa</h4>
                                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">[Jumlah Siswa]</p>
                            </div>
                            <!-- Statistik Card 2 -->
                            <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg shadow-md">
                                <h4 class="font-semibold text-lg text-green-800 dark:text-green-200">Total Guru</h4>
                                <p class="text-3xl font-bold text-green-900 dark:text-green-100">[Jumlah Guru]</p>
                            </div>
                            <!-- Statistik Card 3 -->
                            <div class="bg-yellow-100 dark:bg-yellow-900 p-4 rounded-lg shadow-md">
                                <h4 class="font-semibold text-lg text-yellow-800 dark:text-yellow-200">Absensi Hari Ini</h4>
                                <p class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">[Persentase Absensi]</p>
                            </div>
                            <!-- Tambahkan lebih banyak statistik atau grafik di sini -->
                        </div>

                        <div class="mt-8">
                            <h4 class="font-semibold text-xl mb-3">Tren Absensi Mingguan</h4>
                            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-md h-48 flex items-center justify-center">
                                <p class="text-gray-600 dark:text-gray-400">Grafik tren absensi akan ditampilkan di sini.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Regular User Dashboard Content (QR Code) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- QR Code Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6 text-center flex flex-col items-center justify-center">
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Scan QR Code Ini Untuk Absensi</h3>
                        <p class="mb-6 text-gray-700 dark:text-gray-300">QR Code ini hanya berlaku selama beberapa menit.</p>
                        
                        <turbo-frame id="qr_code_frame">
                            <div class="flex justify-center items-center bg-white p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                                {!! QrCode::size(250)->generate($scanUrl) !!}
                            </div>
                            
                            <div class="mt-8 space-y-4 w-full max-w-xs">
                                <!-- Tombol Refresh -->
                                <a href="{{ route('dashboard') }}" data-turbo-frame="qr_code_frame"
                                        class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out shadow-md text-center">
                                    Refresh QR Code
                                </a>
                                
                                <!-- Tombol Cetak -->
                                <a href="{{ route('absensi.cetak') }}" 
                                   target="_blank" 
                                   class="w-full inline-block bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition duration-300 ease-in-out shadow-md text-center">
                                    Cetak QR Code
                                </a>
                            </div>
                        </turbo-frame>
                    </div>

                    <!-- Placeholder for other dashboard elements (e.g., attendance summary, announcements) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6 flex flex-col items-center justify-center">
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Informasi Tambahan</h3>
                        <p class="text-gray-700 dark:text-gray-300">Area ini dapat digunakan untuk menampilkan ringkasan absensi, pengumuman, atau statistik relevan lainnya.</p>
                        <!-- Content will be added here in future steps based on user's needs -->
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
