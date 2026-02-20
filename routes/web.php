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
use App\Http\Controllers\UtilitiesController;
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

    // Rute utilitas untuk generate QR Code
    Route::get('/utilities/qrcode', [UtilitiesController::class, 'generateQrCode'])->name('utilities.qrcode');

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
        
    // Rekap Absensi Pegawai
    Route::controller(App\Http\Controllers\RekapAbsensiPegawaiController::class)->name('rekap_absensi_pegawai.')->group(function () {
        Route::get('/rekap-absensi-pegawai', 'index')->name('index');
        Route::get('/rekap-absensi-pegawai/create', 'create')->name('create');
        Route::post('/rekap-absensi-pegawai', 'store')->name('store');
        Route::get('/rekap-absensi-pegawai/{absensiPegawai}/edit', 'edit')->name('edit');
        Route::put('/rekap-absensi-pegawai/{absensiPegawai}', 'update')->name('update');
        Route::delete('/rekap-absensi-pegawai/{absensiPegawai}', 'destroy')->name('destroy');
        Route::post('/rekap-absensi-pegawai/bulk-destroy', 'bulkDestroy')->name('bulkDestroy');
        Route::get('/rekap-absensi-pegawai/export', 'export')->name('export');
    });

    });

    

    // --- FITUR KHUSUS ADMIN ---
    Route::middleware('can:isAdmin')->group(function () {

        // QR Code Generator
        Route::get('/users/generate-qr-codes', [UserController::class, 'showQrCodeGenerator'])->name('users.qr-generator');
        Route::get('/users/bulk-download-qr-codes', [UserController::class, 'bulkDownloadQrCodes'])->name('users.bulk-qr-generator.download');
        Route::get('/users/{user}/qr-code/download', [UserController::class, 'downloadQrCode'])->name('users.qr-code.download');

        // Pengaturan Aplikasi
        Route::controller(SettingController::class)->name('pengaturan.')->group(function () {
            Route::get('/pengaturan', 'index')->name('index');
            Route::post('/pengaturan', 'update')->name('update');
        });

        // Manajemen Pengguna (Users)
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

        // Manajemen Kelas dan Mata Pelajaran (resourceful routes)
        Route::resource('kelas', KelasController::class);
        Route::get('/kelas/{kela}/print-schedule', [KelasController::class, 'printSchedule'])->name('kelas.printSchedule');
        Route::delete('/kelas/{kela}/remove-siswa/{siswaId}', [KelasController::class, 'removeSiswa'])->name('kelas.removeSiswa');
        Route::resource('mata-pelajaran', MataPelajaranController::class);

        // Manajemen Jadwal Absensi Siswa (terkait kelas)
        Route::controller(JadwalAbsensiController::class)->name('jadwal.')->group(function () {
            Route::get('/jadwal', 'index')->name('index'); // Jadwal Siswa
            Route::get('/jadwal/{jadwal}/absensi', 'showAttendanceSheet')->name('absensi.create');
            Route::post('/jadwal/{jadwal}/absensi', 'storeAttendanceSheet')->name('absensi.store');
            Route::get('/jadwal/create', 'create')->name('create');
            Route::post('/jadwal', 'store')->name('store');
            Route::get('/jadwal/{jadwal}/edit', 'edit')->name('edit');
            Route::put('/jadwal/{jadwal}', 'update')->name('update');
            Route::delete('/jadwal/{jadwal}', 'destroy')->name('destroy');
            Route::post('/jadwal/bulk-destroy', 'bulkDestroy')->name('bulkDestroy');
            Route::get('/jadwal/export', 'exportExcel')->name('export');
            Route::post('/jadwal/import', 'importExcel')->name('import');
            Route::get('/jadwal/import-template', 'downloadTemplate')->name('importTemplate');
        });

        // Manajemen Jadwal Absensi Pegawai (Guru, TU, Lainnya)
        Route::controller(App\Http\Controllers\JadwalAbsensiPegawaiController::class)->name('jadwal-absensi-pegawai.')->group(function () {
            Route::get('/jadwal-absensi-pegawai', 'index')->name('index');
            Route::get('/jadwal-absensi-pegawai/create', 'create')->name('create');
            Route::post('/jadwal-absensi-pegawai', 'store')->name('store');
            Route::get('/jadwal-absensi-pegawai/{jadwalAbsensiPegawai}/edit', 'edit')->name('edit');
            Route::put('/jadwal-absensi-pegawai/{jadwalAbsensiPegawai}', 'update')->name('update');
            Route::delete('/jadwal-absensi-pegawai/{jadwalAbsensiPegawai}', 'destroy')->name('destroy');
            Route::post('/jadwal-absensi-pegawai/bulk-destroy', 'bulkDestroy')->name('bulkDestroy');
            Route::get('/jadwal-absensi-pegawai/export', 'exportExcel')->name('export');
            Route::post('/jadwal-absensi-pegawai/import', 'importExcel')->name('import');
            Route::get('/jadwal-absensi-pegawai/import-template', 'downloadTemplate')->name('importTemplate');
        });

        Route::get('/admin/dashboard/chart-data', [App\Http\Controllers\DashboardController::class, 'chartData'])->name('admin.dashboard.chart-data');
    });

    // Rute untuk FAQ (dapat diakses oleh semua pengguna terotentikasi)
    Route::get('/pengaturan/faq', function () {
        return view('pengaturan.faq');
    })->name('pengaturan.faq');

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
