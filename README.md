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

    Buka file `.env` dan konfigurasikan pengaturan database Anda (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD) agar sesuai dengan database yang baru Anda buat.

    Catatan: Untuk login awal ke aplikasi setelah instalasi, Anda dapat menggunakan seeder untuk login sebagai admin.

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

