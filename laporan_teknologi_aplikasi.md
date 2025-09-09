# Laporan Teknologi Aplikasi Absensi Sekolah

Dokumen ini merinci tumpukan teknologi (technology stack) yang digunakan dalam pengembangan Aplikasi Absensi Sekolah, mencakup komponen backend, frontend, dan alat bantu pengembangan.

---

## 1. Teknologi Backend

Infrastruktur sisi server aplikasi dibangun di atas ekosistem PHP modern yang berpusat pada Laravel.

*   **Bahasa & Framework:**
    *   **PHP `^8.2`:** Bahasa pemrograman utama yang menjalankan semua logika sisi server.
    *   **Laravel `^12.0`:** Framework aplikasi web yang menjadi fondasi utama, menyediakan struktur MVC, ORM Eloquent, sistem routing, dan berbagai fitur keamanan serta kemudahan pengembangan.

*   **Database:**
    *   **MySQL:** Sistem manajemen database relasional (RDBMS) yang digunakan dalam lingkungan pengembangan dan produksi aplikasi ini untuk menyimpan semua data secara persisten.

*   **Paket PHP Utama (via Composer):**
    *   **Otentikasi & Keamanan:**
        *   `laravel/breeze: ^2.3`: Menyediakan scaffolding awal untuk sistem otentikasi (login, registrasi).
        *   `laravel/sanctum: ^4.1`: Digunakan untuk otentikasi berbasis token API.
    *   **Fitur Inti Aplikasi:**
        *   `maatwebsite/excel: ^3.1`: Mengelola semua fungsionalitas impor dan ekspor data ke format Excel (.xlsx).
        *   `barryvdh/laravel-dompdf: ^3.1`: Menghasilkan file PDF untuk fitur seperti cetak kartu dan laporan.
        *   `simplesoftwareio/simple-qrcode: ^4.2`: Menghasilkan QR Code yang digunakan pada kartu siswa untuk proses absensi.
        *   `intervention/image: ^3.11`: Mengelola pemrosesan dan manipulasi gambar, seperti foto profil pengguna.
    *   **Interaktivitas & Real-time:**
        *   `hotwired-laravel/turbo-laravel: ^2.3`: Memberikan pengalaman navigasi secepat Single-Page Application (SPA) tanpa perlu menulis kode JavaScript yang kompleks.

*   **Manajemen Dependensi:**
    *   **Composer:** Alat untuk mengelola semua paket dan pustaka PHP yang digunakan dalam proyek.

---

## 2. Teknologi Frontend

Antarmuka pengguna (UI) dibangun dengan pendekatan modern yang mengutamakan kecepatan dan interaktivitas.

*   **Build Tool:**
    *   **Vite `^6.2.4`:** Alat build frontend generasi baru yang mengkompilasi aset (CSS, JS) dengan sangat cepat dan mendukung Hot Module Replacement (HMR) untuk pengembangan yang efisien.

*   **Framework & Pustaka JavaScript:**
    *   **Alpine.js `^3.4.2`:** Framework JavaScript minimalis untuk menambahkan interaktivitas pada antarmuka langsung dari markup HTML.
    *   `@hotwired/turbo: ^8.0.13`: Pustaka sisi klien yang bekerja dengan `turbo-laravel` untuk menangani navigasi halaman secara mulus.
    *   **Chart.js `^4.5.0`:** Digunakan untuk membuat dan menampilkan grafik dan diagram yang informatif di halaman dashboard.
    *   **Tom Select `^2.4.3`:** Menggantikan elemen `<select>` standar dengan antarmuka yang lebih kaya fitur, seperti pencarian dan tag.
    *   **Flatpickr `^4.6.13`:** Pustaka untuk membuat widget pemilih tanggal dan waktu (datepicker) yang ringan dan modern.
    *   **Axios `^1.8.2`:** Klien HTTP berbasis Promise untuk membuat permintaan data dari browser.

*   **Styling & Ikon:**
    *   **Tailwind CSS `^3.1.0`:** Framework CSS utility-first yang menjadi dasar dari seluruh desain antarmuka aplikasi.
    *   **Font Awesome `^6.7.2`:** Menyediakan set ikon yang luas dan konsisten di seluruh aplikasi.

*   **Manajemen Dependensi:**
    *   **NPM (Node Package Manager):** Digunakan untuk mengelola semua pustaka dan dependensi sisi frontend.

---

## 3. Alat Bantu & Lingkungan Pengembangan

*   **Concurrent-Scripts:**
    *   `concurrently`: Menjalankan beberapa perintah (seperti server PHP, Vite, dan antrian) secara bersamaan dalam satu terminal untuk memudahkan proses pengembangan.
*   **Testing:**
    *   **Pest `^3.8`:** Framework testing PHP yang elegan dengan fokus pada kesederhanaan, digunakan untuk pengujian unit dan fitur.
