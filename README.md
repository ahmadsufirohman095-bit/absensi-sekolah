<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Panduan Instalasi

Untuk menjalankan proyek ini secara lokal, ikuti langkah-langkah berikut:

### Persyaratan Sistem

Pastikan Anda memiliki perangkat lunak berikut terinstal di sistem Anda:

*   PHP >= v8.2.12
*   Composer
*   Node.js >= v22.16.0
*   npm
*   Database MySQL

### Mengaktifkan Ekstensi PHP (XAMPP)

Sebelum memulai instalasi, pastikan Anda telah mengaktifkan ekstensi PHP berikut pada file `php.ini` di XAMPP Anda:

1.  Buka XAMPP Control Panel.
2.  Klik tombol "Config" di samping "Apache", lalu pilih "PHP (php.ini)".
3.  Cari baris-baris berikut dan hapus tanda titik koma (`;`) di depannya untuk mengaktifkannya:
    - `extension=bz2`
    - `extension=curl`
    - `extension=fileinfo`
    - `extension=gd`
    - `extension=gettext`
    - `extension=intl`
    - `extension=mbstring`
    - `extension=exif`
    - `extension=mysqli`
    - `extension=openssl`
    - `extension=pdo_mysql`
    - `extension=pdo_sqlite`
    - `extension=zip`
4.  Setelah mengedit file `php.ini`, simpan perubahan dan restart Apache dari XAMPP Control Panel.

### Memulai XAMPP

Jika Anda menggunakan XAMPP, ikuti langkah-langkah berikut untuk memulai server Apache dan MySQL:

1.  Buka XAMPP Control Panel.
2.  Di samping "Apache", klik tombol "Start".
3.  Di samping "MySQL", klik tombol "Start".

Pastikan kedua modul berjalan (status berwarna hijau) sebelum melanjutkan instalasi.

### Langkah-langkah Instalasi

1.  Kloning Repositori:

    bash
    `git clone https://github.com/ahmadsufirohman095-bit/absensi-sekolah.git`

    `cd absensi-sekolah`

2.  Buat Database MySQL:

    Sebelum melanjutkan, buat database MySQL baru untuk proyek ini. Anda bisa menggunakan phpMyAdmin, MySQL Workbench, atau baris perintah:

    sql
    `CREATE DATABASE db_absensi_sekolah;`

    Pastikan nama database sesuai dengan yang akan Anda konfigurasikan di file `.env`.

3.  Instal Dependensi PHP:

    bash
    `composer install`

4.  Konfigurasi Lingkungan:

    Salin file `.env.example` menjadi `.env`:

    bash
    `cp .env.example .env`

    Buka file `.env` dan konfigurasikan pengaturan lingkungan Anda. Berikut adalah beberapa variabel penting yang perlu Anda perhatikan untuk konteks lokal Indonesia:

    *   `APP_NAME`: Nama aplikasi Anda (misalnya, "Absensi Sekolah").
    *   `APP_URL`: URL dasar aplikasi Anda. Jika Anda menjalankan di lingkungan lokal, ini bisa `http://localhost:8000` (jika menggunakan `php artisan serve`   atau `http://127.0.0.1:8000`.
    *   `APP_DEBUG`: Setel ke `true` selama pengembangan untuk melihat pesan kesalahan yang detail. Setel ke `false` saat aplikasi sudah siap untuk produksi.
    *   `APP_LOCALE`: Atur ke `id` untuk bahasa Indonesia.
    *   `APP_FALLBACK_LOCALE`: Atur ke `id` sebagai bahasa cadangan.
    *   `APP_FAKER_LOCALE`: Atur ke `id_ID` untuk data dummy yang lebih relevan dengan Indonesia.

    **Konfigurasi Database:**
    Pastikan Anda mengkonfigurasi variabel database agar sesuai dengan database MySQL yang telah Anda buat. Contoh konfigurasi untuk XAMPP:

    *   `DB_CONNECTION=mysql` (Pastikan ini `mysql` jika Anda menggunakan MySQL)
    *   `DB_HOST=127.0.0.1` (Biasanya `127.0.0.1` atau `localhost`)
    *   `DB_PORT=3306` (Port default MySQL)
    *   `DB_DATABASE=db_absensi_sekolah` (Nama database yang Anda buat di langkah 2)
    *   `DB_USERNAME=root` (Username database Anda, default XAMPP adalah `root`)
    *   `DB_PASSWORD=` (Password database Anda, default XAMPP adalah kosong)

    Catatan: `APP_KEY` akan dibuat secara otomatis pada langkah berikutnya. Untuk login awal ke aplikasi setelah instalasi, Anda dapat menggunakan seeder untuk login sebagai admin.

    bash
    `php artisan db:seed --class=AdminSeeder`

5.  Buat Kunci Aplikasi:

    bash
    `php artisan key:generate`

6.  Migrasi Database:

    Jalankan migrasi database untuk membuat tabel yang diperlukan:

    bash
    `php artisan migrate`

7.  Jalankan Seeder (Opsional):

    Jika Anda ingin mengisi database dengan data dummy, jalankan seeder:

    bash
    `php artisan db:seed`
    Catatan: Jika Anda menjalankan `php artisan db:seed`, ini akan menjalankan `DatabaseSeeder` yang akan mengisi database dengan data dummy berikut:
        
        Mata Pelajaran: Beberapa mata pelajaran contoh.

        Kelas: Beberapa kelas contoh (misalnya VII-A, VII-B).

        Admin: Dua akun admin (`admin` dan `admin2`) dengan username dan password `password`.

        Guru: Sepuluh akun guru dengan data profil dan penugasan mata pelajaran.

        Wali Kelas: Guru-guru akan ditetapkan sebagai wali kelas untuk kelas-kelas yang ada.

        Siswa: Dua puluh siswa per kelas dengan data profil.

        Jadwal Absensi: Jadwal absensi acak untuk setiap kelas, mata pelajaran, dan guru.

        Absensi: Data absensi dummy untuk 4 minggu terakhir berdasarkan jadwal yang dibuat.

8.  Instal Dependensi JavaScript:

    bash
    `npm install` # atau yarn install

9.  Kompilasi Aset Frontend:

    Untuk mengkompilasi aset CSS dan JavaScript, jalankan:

    bash
    `npm run dev` # untuk pengembangan
    atau
    `npm run build` # untuk produksi


10. Jalankan Server Pengembangan:

    bash
    `php artisan serve`

    Aplikasi akan tersedia di `http://127.0.0.1:8000`.

# 📘 PANDUAN LENGKAP MENJALANKAN PROYEK DI LINUX

Project: **Absensi Sekolah (Laravel + MySQL + Node)**

---

# 🧱 BAGIAN 1 — Instalasi yang Dibutuhkan (HANYA SEKALI)

Kita hanya perlu install 3 hal di Linux:

1. Git
2. Docker
3. Docker Compose (sudah termasuk di docker modern)

---

## 1️⃣ Install Git

Git digunakan untuk mengambil kode project dari internet.

Buka Terminal lalu ketik:

```bash
sudo pacman -S git
```

Cek berhasil atau tidak:

```bash
git --version
```

Kalau muncul versi → berhasil.

---

## 2️⃣ Install Docker

Docker digunakan untuk menjalankan:

* PHP
* MySQL
* Nginx
* Node

Tanpa perlu install satu per satu.

Install:

```bash
sudo pacman -S docker docker-compose
```

Aktifkan Docker:

```bash
sudo systemctl enable docker
sudo systemctl start docker
```

Cek:

```bash
docker --version
```

Kalau muncul versi → berhasil.

---

## 3️⃣ Supaya Tidak Perlu Pakai sudo Terus

Tambahkan user ke group docker:

```bash
sudo usermod -aG docker $USER
```

Lalu logout dan login kembali.

---

# 📂 BAGIAN 2 — Mengambil Project

Masuk ke folder home:

```bash
cd ~
```

Buat folder khusus project:

```bash
mkdir projects
cd projects
```

Clone project:

```bash
git clone https://github.com/ahmadsufirohman095-bit/absensi-sekolah.git
cd absensi-sekolah
```

Sekarang kamu sudah berada di dalam folder project.

---

# ⚙ BAGIAN 3 — Konfigurasi Project

## 1️⃣ Buat File .env

Salin dari contoh:

```bash
cp .env.example .env
```

Buka file `.env` dan sesuaikan bagian database seperti ini:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=db_absensi_sekolah
DB_USERNAME=root
DB_PASSWORD=root
```

Simpan lalu keluar.

---

# 🐳 BAGIAN 4 — Menjalankan Project dengan Docker

## 1️⃣ Build Container (Pertama Kali Saja)

```bash
docker compose build
```

## 2️⃣ Jalankan Container

```bash
docker compose up -d
```

Cek apakah berjalan:

```bash
docker ps
```

Kalau ada container:

* absensi_app
* absensi_mysql
* absensi_nginx
* absensi_node

Berarti berhasil.

---

# 🔐 BAGIAN 5 — Mengatur Permission (PENTING)

Masuk ke container PHP:

```bash
docker exec -it absensi_app bash
```

Jalankan:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
exit
```

Kenapa?
Karena Laravel perlu izin menulis file.

---

# 🗄 BAGIAN 6 — Setup Database

Masuk lagi ke container:

```bash
docker exec -it absensi_app bash
```

Generate key:

```bash
php artisan key:generate
```

Migrasi database:

```bash
php artisan migrate
```

Isi data awal (opsional):

```bash
php artisan db:seed
```

Keluar:

```bash
exit
```

---

# 🌐 BAGIAN 7 — Membuka Website

Buka browser dan akses:

```
http://localhost:8000
```

Jika berhasil → website tampil.

---

# 🔁 Cara Menjalankan Lagi Besok

Jika komputer dimatikan:

1. Start Docker:

```bash
sudo systemctl start docker
```

2. Masuk ke folder project:

```bash
cd ~/projects/absensi-sekolah
```

3. Jalankan:

```bash
docker compose up -d
```

Selesai.

---

# 🛑 Cara Menghentikan Project

Stop saja:

```bash
docker compose down
```

---

# ❓ Jika Error Permission

Jalankan ulang:

```bash
docker exec -it absensi_app bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
exit
```

---

# 🧠 Kenapa Kita Pakai Docker?

Karena:

* Tidak perlu install PHP
* Tidak perlu install MySQL
* Tidak perlu XAMPP
* Tidak bentrok versi
* Lebih profesional

---


## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
