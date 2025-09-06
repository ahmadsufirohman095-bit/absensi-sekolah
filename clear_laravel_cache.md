```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
composer dump-autoload
```

**Penjelasan:**

*   `php artisan cache:clear`: Menghapus semua file cache aplikasi.
*   `php artisan config:clear`: Menghapus file cache konfigurasi.
*   `php artisan view:clear`: Menghapus semua file cache view yang dikompilasi.
*   `php artisan route:clear`: Menghapus file cache rute.
*   `composer dump-autoload`: Memperbarui file autoloader Composer, yang penting setelah menambahkan atau mengubah kelas di proyek Anda.

baca resources\js, vite.config.js, tailwind.config.js, postcss.config.js, package.json, composer.lock, resources\js\app.js
