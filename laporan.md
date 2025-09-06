# Laporan Analisis Proyek Absensi Sekolah

Dokumen ini berisi hasil analisis fungsional dan teknis dari proyek aplikasi absensi sekolah.

## 1. Struktur Database

Database aplikasi ini dirancang secara relasional untuk mengelola data sekolah, pengguna, dan absensi secara efisien.

**Tabel Inti & Fungsinya:**

*   **`users`**: Tabel pusat yang menyimpan semua pengguna (admin, guru, siswa). Peran pengguna dibedakan oleh kolom `role`.
*   **`admin_profiles`**, **`guru_profiles`**, **`siswa_profiles`**: Tabel-tabel ini menyimpan data detail yang spesifik untuk setiap peran, terhubung ke tabel `users` dengan `user_id`.
*   **`kelas`**: Menyimpan daftar semua kelas. Setiap kelas dapat memiliki satu `wali_kelas_id` yang merujuk pada seorang guru di tabel `users`.
*   **`mata_pelajarans`**: Daftar semua mata pelajaran yang ada di sekolah.
*   **`jadwal_absensis`**: Mendefinisikan jadwal pelajaran, menghubungkan `kelas`, `mata_pelajaran`, dan `guru` pada hari dan jam tertentu.
*   **`absensis`**: Tabel transaksi utama yang mencatat setiap kehadiran (`hadir`, `sakit`, `izin`, `alpha`) dari seorang siswa (`user_id`) untuk sebuah jadwal (`jadwal_absensi_id`).
*   **`profil_sekolah`**: Menyimpan informasi umum tentang sekolah seperti nama, alamat, logo, dll.

**Tabel Relasi (Pivot):**

*   **`guru_mata_pelajaran`**: Menentukan mata pelajaran apa saja yang diajarkan oleh seorang guru.
*   **`kelas_mata_pelajaran`**: Menentukan mata pelajaran apa saja yang tersedia di sebuah kelas.

## 2. Operasi CRUD (Create, Read, Update, Delete)

Aplikasi ini mengimplementasikan operasi CRUD secara ekstensif pada berbagai sumber daya (resource), yang dikelola oleh Controller masing-masing:

*   **Manajemen Pengguna (`UserController`)**: Admin dapat melakukan CRUD penuh pada data pengguna (siswa, guru, admin), termasuk fitur impor/ekspor data via Excel dan aktivasi/deaktivasi akun.
*   **Manajemen Kelas (`KelasController`)**: Admin dapat membuat, membaca, mengubah, dan menghapus data kelas.
*   **Mata Pelajaran (`MataPelajaranController`)**: Dikelola penuh oleh admin.
*   **Jadwal Absensi (`JadwalAbsensiController`)**: Admin dapat mengatur jadwal pelajaran untuk setiap kelas.
*   **Rekap Absensi (`RekapAbsensiController`)**: Admin dan Guru dapat mengelola data absensi, termasuk membuat, mengedit, dan menghapus catatan absensi secara massal.
*   **Pengaturan (`SettingController` & `ProfilSekolahController`)**: Admin dapat mengubah pengaturan umum aplikasi dan profil sekolah.

## 3. UI (User Interface) & UX (User Experience)

Tampilan antarmuka aplikasi ini dibangun dengan teknologi modern untuk memberikan pengalaman pengguna yang responsif dan interaktif.

*   **Framework & Teknologi**:
    *   **Vite**: Sebagai *build tool* modern yang mempercepat proses development frontend.
    *   **Tailwind CSS**: Digunakan sebagai framework CSS utama, memungkinkan desain yang konsisten dan modern. Terdapat dukungan untuk *dark mode*.
    *   **Alpine.js**: Menangani interaktivitas pada antarmuka tanpa memerlukan framework JavaScript yang berat.
    *   **Hotwired/Turbo**: Membuat navigasi terasa sangat cepat (seperti Single Page Application/SPA) dengan memuat hanya bagian halaman yang berubah.
*   **Komponen UI/UX**:
    *   **Chart.js**: Digunakan untuk menampilkan data statistik absensi dalam bentuk grafik di dashboard.
    *   **TomSelect**: Menggantikan elemen `<select>` standar menjadi input yang lebih interaktif.
    *   **Flatpickr**: Menyediakan *date & time picker* yang modern.
    *   **Font Awesome**: Menyediakan ikon-ikon yang memperjelas fungsi tombol dan menu.

## 4. Alur Sistem (System Flow)

Alur kerja aplikasi ini didasarkan pada peran (role-based) pengguna setelah mereka melakukan login.

*   **Otentikasi & Otorisasi**:
    *   Sistem menggunakan sistem otentikasi bawaan Laravel.
    *   Akses ke fitur-fitur tertentu dibatasi berdasarkan peran (`admin`, `guru`, `siswa`) menggunakan *Middleware* dan *Gates*.

*   **Alur Admin**:
    1.  Login dan masuk ke **Dashboard Admin** untuk melihat statistik umum.
    2.  Mengelola seluruh data master: **Pengguna** (termasuk cetak kartu siswa), **Kelas**, **Mata Pelajaran**, dan **Jadwal Pelajaran**.
    3.  Mengelola **Rekap Absensi**, melakukan ekspor data, dan melihat laporan.
    4.  Mengubah **Pengaturan Aplikasi** dan **Profil Sekolah**.

*   **Alur Guru**:
    1.  Login dan masuk ke **Dashboard Guru** untuk melihat jadwal dan statistik.
    2.  Melakukan absensi dengan memindai **QR Code** siswa saat jam mengajar.
    3.  Mengelola data absensi untuk kelas dan mata pelajaran yang diampunya.
    4.  Melihat dan mengekspor laporan absensi dari kelasnya.

*   **Alur Siswa**:
    1.  Login dan masuk ke **Dashboard Siswa**.
    2.  Melihat rekap absensinya sendiri dalam bentuk statistik dan grafik.
    3.  Melihat jadwal pelajarannya.
    4.  Melakukan absensi dengan menunjukkan **QR Code** pada kartu pelajarnya untuk dipindai oleh guru.
