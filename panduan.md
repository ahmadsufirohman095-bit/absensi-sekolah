# Panduan Sistem Aplikasi Absensi Sekolah

Dokumen ini berfungsi sebagai panduan komprehensif untuk memahami dan mengoperasikan sistem aplikasi absensi sekolah. Sistem ini dirancang untuk mengelola kehadiran siswa dengan peran pengguna yang berbeda: Siswa, Guru, dan Admin.

## 1. Gambaran Umum Sistem

Aplikasi ini dibangun menggunakan **Laravel** sebagai framework backend utama dan **Vite** untuk kompilasi aset frontend.

### Teknologi yang Digunakan:
*   **Backend:**
    *   Laravel v12.x (PHP Framework)
    *   Composer (Manajemen Dependensi PHP)
    *   Laravel Sanctum (Otentikasi API)
    *   Maatwebsite/Excel (Ekspor Data Excel)
    *   SimpleSoftwareIO/Simple-QRCode (Pembuatan QR Code)
    *   Eloquent ORM (Interaksi Database)
*   **Frontend:**
    *   Vite v6.2.4 (Build Tool)
    *   NPM/Yarn (Manajemen Dependensi JavaScript)
    *   Tailwind CSS (Styling)
    *   Alpine.js (Interaktivitas JavaScript)
    *   Axios (Klien HTTP)
    *   Font Awesome (Icon Library)
    *   Tom Select (Enhanced Select Inputs)
    *   Hotwired Turbo (Navigasi Halaman Cepat)

### Konfigurasi Penting:
*   `config/app.php`: Mengatur nama aplikasi, lingkungan, debug mode, URL, zona waktu, dan lokal.
*   `config/database.php`: Mengatur koneksi database (default SQLite, mendukung MySQL, MariaDB, PostgreSQL, SQL Server).

## 2. Struktur Pengguna dan Peran

Sistem ini memiliki tiga peran pengguna utama, masing-masing dengan fungsionalitas dan akses yang berbeda:

*   **Admin:** Memiliki kontrol penuh atas sistem, termasuk manajemen pengguna, kelas, mata pelajaran, jadwal, pengaturan sistem, laporan, dan rekap absensi.
*   **Guru:** Dapat mencetak QR Code absensi, memindai absensi siswa secara manual, melihat laporan dan rekap absensi, serta mengelola pengajuan izin/sakit siswa.
*   **Siswa:** Dapat melakukan absensi dengan memindai QR Code dan melihat riwayat absensi mereka.

Setiap pengguna memiliki profil terkait (`AdminProfile`, `GuruProfile`, `SiswaProfile`) yang menyimpan detail spesifik peran mereka.

## 3. Alur Penggunaan Sistem Berdasarkan Peran

### 3.1. Untuk Siswa

1.  **Login:** Akses sistem melalui halaman login (`/login`).
2.  **Dashboard:** Setelah login, siswa akan melihat dashboard yang menampilkan:
    *   Total kehadiran dan keterlambatan.
    *   Persentase kehadiran.
    *   Riwayat absensi terbaru.
3.  **Melakukan Absensi (Scan QR Code):**
    *   Pilih opsi "Scan Absensi".
    *   Pindai QR Code yang disediakan oleh guru atau admin.
    *   Sistem akan memvalidasi QR Code dan mencatat absensi Anda (hadir atau terlambat) berdasarkan waktu scan dan jam masuk yang ditentukan.
    *   Anda akan menerima konfirmasi absensi berhasil atau pesan error jika ada masalah (misalnya, QR Code tidak valid, sudah absen hari ini).

### 3.2. Untuk Guru

1.  **Login:** Akses sistem melalui halaman login (`/login`).
2.  **Dashboard:** Dashboard guru menampilkan:
    *   Informasi kelas yang diampu.
    *   Statistik kehadiran siswa di kelas yang diampu.
    *   Daftar siswa yang belum absen hari ini.
    *   Jadwal mengajar hari ini.
    *   Total mata pelajaran dan kelas yang diajar.
    *   Jumlah absensi tercatat hari ini.
3.  **Mencetak QR Code Absensi:**
    *   Pilih menu untuk mencetak QR Code.
    *   Sistem akan menghasilkan QR Code unik yang berlaku selama 5 menit.
    *   Tampilkan QR Code ini kepada siswa untuk dipindai.
4.  **Memindai Absensi (Manual):**
    *   Akses halaman pemindai QR Code (`/scan-absensi`).
    *   Pindai QR Code identitas siswa.
    *   Sistem akan mencatat absensi siswa tersebut.
5.  **Melihat Laporan Absensi:**
    *   Akses menu "Laporan Absensi" (`/laporan`).
    *   Gunakan filter tanggal dan kelas untuk melihat laporan yang spesifik.
    *   Anda dapat mengekspor laporan ke format Excel.
6.  **Melihat Rekap Absensi:**
    *   Akses menu "Rekap Absensi" (`/rekap-absensi`).
    *   Gunakan filter rentang tanggal, kelas, atau NIS siswa untuk rekapitulasi yang lebih detail.
    *   Anda dapat mengekspor rekap absensi ke format Excel (rekap per siswa atau detail absensi siswa tunggal).
7.  **Mengelola Pengajuan Izin/Sakit:**
    *   Akses menu "Izin/Sakit" untuk melihat dan mengelola pengajuan izin atau sakit dari siswa.

### 3.3. Untuk Admin

1.  **Login:** Akses sistem melalui halaman login (`/login`).
2.  **Dashboard:** Dashboard admin menyediakan gambaran umum sistem, termasuk:
    *   Total Admin, Guru, Siswa, Kelas, dan Mata Pelajaran.
    *   Jumlah absensi, keterlambatan, dan izin/sakit hari ini.
    *   Persentase kehadiran hari ini.
3.  **Pengaturan Aplikasi:**
    *   Akses menu "Pengaturan" untuk mengelola pengaturan sistem, seperti `jam_masuk` untuk absensi.
4.  **Manajemen Pengguna:**
    *   Akses menu "Pengguna" (`/users`).
    *   Lakukan operasi CRUD (Create, Read, Update, Delete) untuk semua jenis pengguna (Admin, Guru, Siswa).
    *   Gunakan fitur pencarian dan filter untuk menemukan pengguna.
    *   Ekspor data pengguna ke Excel atau impor data pengguna dari file Excel/CSV.
    *   Cetak kartu identitas untuk siswa.
5.  **Manajemen Kelas:**
    *   Akses menu "Kelas" (`/kelas`).
    *   Lakukan operasi CRUD untuk kelas.
    *   Tambahkan/hapus siswa dari kelas.
    *   Sinkronkan mata pelajaran yang diajarkan di kelas.
    *   Cetak kartu identitas untuk semua siswa di kelas tertentu.
6.  **Manajemen Mata Pelajaran:**
    *   Akses menu "Mata Pelajaran" (`/mata-pelajaran`).
    *   Lakukan operasi CRUD untuk mata pelajaran.
    *   Kaitkan guru pengajar dengan mata pelajaran.
7.  **Manajemen Jadwal Absensi:**
    *   Akses menu "Jadwal Absensi" (`/jadwal`).
    *   Lakukan operasi CRUD untuk jadwal absensi (Kelas, Mata Pelajaran, Guru, Hari, Jam Mulai, Jam Selesai).
    *   Gunakan fitur filter untuk melihat jadwal spesifik.
    *   Lakukan hapus massal atau hapus semua jadwal.
    *   Ekspor jadwal ke Excel.
8.  **Laporan dan Rekap:** Admin memiliki akses penuh ke semua fitur laporan dan rekap absensi seperti yang dijelaskan pada bagian Guru.
9.  **Pengajuan Izin/Sakit:** Admin juga memiliki akses untuk mengelola pengajuan izin/sakit siswa.

## 4. Interaksi Database dan Model Kunci

Sistem ini menggunakan beberapa model Eloquent untuk berinteraksi dengan database:

*   `User`: Model dasar untuk semua pengguna (Admin, Guru, Siswa).
*   `AdminProfile`, `GuruProfile`, `SiswaProfile`: Model profil tambahan untuk detail spesifik peran.
*   `Absensi`: Menyimpan catatan kehadiran siswa.
*   `JadwalAbsensi`: Mendefinisikan jadwal pelajaran atau absensi.
*   `Kelas`: Mengelola data kelas.
*   `MataPelajaran`: Mengelola data mata pelajaran.
*   `Setting`: Menyimpan pengaturan aplikasi (misalnya, `jam_masuk`).

## 5. Keamanan dan Otentikasi

*   **Laravel Breeze:** Digunakan untuk scaffolding otentikasi dasar (login, register, reset password).
*   **Laravel Sanctum:** Menyediakan fondasi untuk otentikasi API.
*   **Middleware:** Rute-rute penting dilindungi oleh middleware `auth` (memastikan pengguna terotentikasi) dan `verified` (memastikan email pengguna terverifikasi).
*   **Otorisasi (Gate & can:):** Akses ke fungsionalitas tertentu dikontrol berdasarkan peran pengguna (misalnya, `manage-absensi`, `isAdmin`).

## 6. Pelaporan dan Ekspor Data

Sistem ini menyediakan fungsionalitas pelaporan dan ekspor data yang komprehensif melalui:

*   `LaporanController`: Menangani tampilan dan ekspor laporan absensi.
*   `RekapController`: Menangani tampilan dan ekspor rekap absensi.
*   `App\Exports`: Direktori yang berisi kelas-kelas eksportir untuk menghasilkan file Excel (Absensi, Jadwal Absensi, Rekap Absensi, Pengguna).
*   `App\Imports\UsersImport`: Digunakan untuk mengimpor data pengguna dari file Excel/CSV.

Panduan ini diharapkan dapat membantu pengguna memahami struktur dan fungsionalitas sistem absensi sekolah secara menyeluruh.
