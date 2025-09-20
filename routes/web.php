<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\ScanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JadwalAbsensiController;
use App\Http\Controllers\CardAssetController;
use App\Http\Controllers\Api\PrintCardConfigController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rute-rute ini adalah jantung dari navigasi aplikasi Anda.
| Ditata secara profesional untuk kemudahan pemeliharaan dan skalabilitas.
|
*/

// Redirect dari root ke halaman login untuk pengguna yang belum terotentikasi.
Route::get('/', fn() => redirect()->route('login'));

// Memuat rute autentikasi standar dari Laravel (login, register, logout, dll.).
require __DIR__ . '/auth.php';

// Grup utama untuk semua rute yang memerlukan otentikasi.
Route::middleware(['auth', 'verified'])->group(function () {

    // Rute Dashboard Utama.
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/guru/dashboard/chart-data', [App\Http\Controllers\DashboardController::class, 'guruChartData'])->name('guru.dashboard.chart-data');

    Route::get('/siswa/dashboard/chart-data', [App\Http\Controllers\DashboardController::class, 'siswaChartData'])->name('siswa.dashboard.chart-data');

    

    // Rute untuk memproses scan QR Code (untuk siswa)
    Route::post('/absensi/process-scan', [AbsensiController::class, 'processScan'])->name('absensi.processScan');

    // --- MANAJEMEN PROFIL PENGGUNA ---
    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('/profile', 'edit')->name('edit');
        Route::patch('/profile', 'update')->name('update');
        Route::delete('/profile', 'destroy')->name('destroy');
    });

    // Rute Notifikasi
    Route::controller(App\Http\Controllers\NotificationController::class)->name('notifications.')->group(function () {
        Route::get('/notifications', 'index')->name('index');
        Route::post('/notifications/{notification}/mark-as-read', 'markAsRead')->name('markAsRead');
        Route::post('/notifications/mark-all-as-read', 'markAllAsRead')->name('markAllAsRead');
    });

    // --- FITUR UNTUK ADMIN & GURU ---
    

    // --- FITUR UNTUK ADMIN & GURU ---
    Route::middleware('can:manage-absensi')->group(function () {

        // Pemindai QR Code dan Laporan
        Route::controller(ScanController::class)->name('scan.')->group(function () {
            Route::get('/scan-absensi/{jadwal_id?}', 'index')->name('index');
            Route::post('/scan-absensi', 'store')->name('store');
            Route::get('/scan-absensi/scanned-students', 'getScannedStudents')->name('getScannedStudents'); // New route
        });

        

    // Rekap Absensi
    Route::get('/rekap_absensi', [App\Http\Controllers\RekapAbsensiController::class, 'index'])->name('rekap_absensi.index');
    Route::get('/rekap_absensi/create', [App\Http\Controllers\RekapAbsensiController::class, 'create'])->name('rekap_absensi.create');
    Route::post('/rekap_absensi', [App\Http\Controllers\RekapAbsensiController::class, 'store'])->name('rekap_absensi.store');
    Route::get('/rekap_absensi/{absensi}/edit', [App\Http\Controllers\RekapAbsensiController::class, 'edit'])->name('rekap_absensi.edit');
    Route::put('/rekap_absensi/{absensi}', [App\Http\Controllers\RekapAbsensiController::class, 'update'])->name('rekap_absensi.update');
    Route::delete('/rekap_absensi/{absensi}', [App\Http\Controllers\RekapAbsensiController::class, 'destroy'])->name('rekap_absensi.destroy');
    Route::post('/rekap_absensi/bulk-destroy', [App\Http\Controllers\RekapAbsensiController::class, 'bulkDestroy'])->name('rekap_absensi.bulkDestroy');
    Route::get('/rekap_absensi/export', [App\Http\Controllers\RekapAbsensiController::class, 'export'])->name('rekap_absensi.export');
        
        

    });

    

    // --- FITUR KHUSUS ADMIN ---
    Route::middleware('can:isAdmin')->group(function () {

        // Pengaturan Aplikasi
        Route::controller(SettingController::class)->name('pengaturan.')->group(function () {
            Route::get('/pengaturan', 'index')->name('index');
            Route::post('/pengaturan', 'update')->name('update');
        });

        // Manajemen Pengguna (Users)
        Route::post('/card-assets/upload', [CardAssetController::class, 'store'])->name('card-assets.store');
        Route::delete('/card-assets/delete', [CardAssetController::class, 'destroy'])->name('card-assets.destroy');
        Route::controller(UserController::class)->name('users.')->group(function () {
            Route::get('/users/export', 'export')->name('export');
            Route::get('/users/import', 'importForm')->name('import.form');
            Route::post('/users/import', 'import')->name('import');
            Route::get('/users/import-template', 'importTemplate')->name('importTemplate');
            Route::get('/users/{user}/cetak-kartu', 'cetakSatuKartu')->name('cetak-satu-kartu');
            Route::patch('/users/{user}/toggle-status', 'toggleStatus')->name('toggleStatus'); // Rute baru
            Route::post('/users/bulk-toggle-status', 'bulkToggleStatus')->name('bulkToggleStatus'); // Rute Aksi Massal
        });
        Route::get('/users/search-siswa', [UserController::class, 'searchSiswa'])->name('users.searchSiswa');
        Route::delete('/users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulkDestroy'); // Rute Aksi Massal Hapus
        Route::resource('users', UserController::class);

        // API Routes for Print Card Configurations
        Route::apiResource('print-card-configs', PrintCardConfigController::class);

        // Custom Print Card Absensi View
        Route::get('/absensi/cards/customize', function () {
            return view('admin.print_cards.customize');
        })->name('absensi.cards.customize');

        // Manajemen Kelas dan Mata Pelajaran (resourceful routes)
        Route::get('/kelas/print-cards', [KelasController::class, 'printCards'])->name('kelas.printCards');
        Route::get('/absensi/cards/pdf', [AbsensiController::class, 'generateAbsensiCardsPdf'])->name('absensi.cards.pdf');
        Route::resource('kelas', KelasController::class);
        Route::get('/kelas/{kela}/print-schedule', [KelasController::class, 'printSchedule'])->name('kelas.printSchedule');
        Route::delete('/kelas/{kela}/remove-siswa/{siswaId}', [KelasController::class, 'removeSiswa'])->name('kelas.removeSiswa');
        Route::resource('mata-pelajaran', MataPelajaranController::class);

        // Manajemen Jadwal Absensi (rute yang relevan untuk kelola kelas)
        Route::get('/jadwal', [JadwalAbsensiController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/{jadwal}/absensi', [JadwalAbsensiController::class, 'showAttendanceSheet'])->name('jadwal.absensi.create');
        Route::post('/jadwal/{jadwal}/absensi', [JadwalAbsensiController::class, 'storeAttendanceSheet'])->name('jadwal.absensi.store');
        Route::get('/jadwal/create', [JadwalAbsensiController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [JadwalAbsensiController::class, 'store'])->name('jadwal.store');
        Route::get('/jadwal/{jadwal}/edit', [JadwalAbsensiController::class, 'edit'])->name('jadwal.edit');
        Route::put('/jadwal/{jadwal}', [JadwalAbsensiController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{jadwal}', [JadwalAbsensiController::class, 'destroy'])->name('jadwal.destroy');
        Route::post('/jadwal/bulk-destroy', [JadwalAbsensiController::class, 'bulkDestroy'])->name('jadwal.bulkDestroy');
        Route::get('/jadwal/export', [JadwalAbsensiController::class, 'exportExcel'])->name('jadwal.export');
Route::post('/jadwal/import', [JadwalAbsensiController::class, 'importExcel'])->name('jadwal.import');
Route::get('/jadwal/import-template', [JadwalAbsensiController::class, 'downloadTemplate'])->name('jadwal.importTemplate');

        Route::get('/admin/dashboard/chart-data', [App\Http\Controllers\DashboardController::class, 'chartData'])->name('admin.dashboard.chart-data');
    });

    // Rute khusus untuk Guru
    Route::middleware('role:guru')->group(function () {
        Route::get('/guru/jadwal-mengajar', [App\Http\Controllers\Guru\JadwalMengajarController::class, 'index'])->name('guru.jadwal-mengajar.index');
        Route::post('/guru/jadwal-mengajar/{jadwal}/absensi', [App\Http\Controllers\Guru\JadwalMengajarController::class, 'storeAttendance'])->name('guru.jadwal-mengajar.storeAttendance');
        Route::get('/guru/jadwal-mengajar/export', [App\Http\Controllers\Guru\JadwalMengajarController::class, 'exportExcel'])->name('guru.jadwal-mengajar.export');
    });

    // --- FITUR KHUSUS SISWA ---
    Route::middleware('role:siswa')->group(function () {
        Route::get('/siswa/laporan-absensi', [App\Http\Controllers\RekapAbsensiController::class, 'laporanSiswa'])->name('siswa.laporan_absensi');
        Route::get('/siswa/laporan-absensi/export', [App\Http\Controllers\RekapAbsensiController::class, 'exportLaporanSiswa'])->name('siswa.laporan_absensi.export');
    });

});
