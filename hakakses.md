# Hak Akses Pengguna (User Access Rights)

Dokumen ini menjelaskan bagaimana hak akses pengguna dikelola dalam aplikasi ini, yang didasarkan pada kombinasi Kontrol Akses Berbasis Peran (RBAC), Laravel Gates, Laravel Policies, dan sebuah direktif Blade kustom.

## 1. Kontrol Akses Berbasis Peran (RBAC)

Sistem ini menggunakan peran (`role`) sebagai penentu utama hak akses. Setiap pengguna memiliki atribut `role` yang disimpan langsung di tabel `users`.

-   **Lokasi**: `app/Models/User.php`
-   **Atribut Kunci**: `role` (nilai yang mungkin: `admin`, `guru`, `siswa`) dan `is_active`.
-   **Metode Pembantu**:
    -   `hasRole($role)`: Memeriksa apakah pengguna memiliki peran tertentu atau salah satu dari beberapa peran.
    -   `isAdmin()`: Memeriksa apakah pengguna memiliki peran `admin`.

## 2. Laravel Gates

Gates adalah cara sederhana untuk mendefinisikan otorisasi yang tidak terkait dengan model tertentu. Mereka didefinisikan di `AuthServiceProvider` dan dapat digunakan di mana saja dalam aplikasi, termasuk sebagai middleware rute.

-   **Lokasi**: `app/Providers/AuthServiceProvider.php`
-   **Definisi Gates**:
    -   `isGuru`: Mengizinkan akses jika peran pengguna adalah `guru`.
        ```php
        Gate::define('isGuru', function (User $user) {
            return $user->role === 'guru';
        });
        ```
    -   `isAdmin`: Mengizinkan akses jika peran pengguna adalah `admin`.
        ```php
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });
        ```
    -   `manage-absensi`: Mengizinkan akses jika peran pengguna adalah `admin` atau `guru`.
        ```php
        Gate::define('manage-absensi', function (User $user) {
            return $user->role === 'admin' || $user->role === 'guru';
        });
        ```
-   **Penggunaan**: Gates ini digunakan di `routes/web.php` melalui middleware `can` (misalnya, `Route::middleware('can:isAdmin')`).

## 3. Laravel Policies

Policies adalah kelas yang mengorganisir logika otorisasi untuk model atau sumber daya tertentu. Mereka memberikan kontrol yang lebih granular atas tindakan yang dapat dilakukan pengguna pada model.

-   **Lokasi Pendaftaran**: `app/Providers/AuthServiceProvider.php`
-   **Implementasi Policies**:

    ### `app/Policies/JadwalAbsensiPolicy.php`
    Policy ini mengatur hak akses untuk model `JadwalAbsensi`.
    -   **`viewAny`, `view`, `create`, `delete`, `restore`, `forceDelete`, `deleteAll`**: Hanya diizinkan untuk pengguna dengan peran `admin`.
        ```php
        public function viewAny(User $user): bool { return $user->isAdmin(); }
        // ... dan metode lainnya
        ```
    -   **`update`**:
        -   Pengguna dengan peran `admin` dapat memperbarui `JadwalAbsensi` apa pun.
        -   Pengguna dengan peran `guru` hanya dapat memperbarui `JadwalAbsensi` yang mereka miliki (di mana `user->id` cocok dengan `jadwalAbsensi->guru_id`).
        ```php
        public function update(User $user, JadwalAbsensi $jadwalAbsensi): bool
        {
            if ($user->isAdmin()) { return true; }
            if ($user->hasRole('guru')) { return $user->id === $jadwalAbsensi->guru_id; }
            return false;
        }
        ```

    ### `app/Policies/AbsensiPolicy.php`
    Policy ini mengatur hak akses untuk model `Absensi`.
    -   **`viewAny`, `view`, `create`, `update`, `delete`, `restore`, `forceDelete`**: Diizinkan untuk pengguna dengan peran `admin` atau `guru`.
        ```php
        public function viewAny(User $user): bool { return $user->isAdmin() || $user->isGuru(); }
        // ... dan metode lainnya
        ```
    -   **`bulkDelete`, `export` (metode kustom)**: Diizinkan untuk pengguna dengan peran `admin` atau `guru`.
        ```php
        public function bulkDelete(User $user): bool { return $user->isAdmin() || $user->isGuru(); }
        public function export(User $user): bool { return $user->isAdmin() || $user->isGuru(); }
        ```

## 4. Direktif Blade Kustom

Sebuah direktif Blade kustom telah didefinisikan untuk mempermudah pemeriksaan peran pengguna dalam tampilan Blade. Direktif ini juga digunakan sebagai middleware rute.

-   **Lokasi**: `app/Providers/AuthServiceProvider.php`
-   **Definisi**:
    ```php
    Blade::if('role', function ($role) {
        return auth()->check() && auth()->user()->role === $role;
    });
    ```
-   **Penggunaan**:
    -   Dalam Blade templates: `@role('guru') ... @endrole`
    -   Sebagai middleware rute: `Route::middleware('role:guru')` (seperti yang terlihat di `routes/web.php`).

Secara keseluruhan, sistem ini menggunakan peran sebagai dasar otorisasi, dengan Gates dan Policies menyediakan kontrol yang lebih rinci atas tindakan dan sumber daya tertentu, memastikan bahwa setiap pengguna hanya dapat mengakses fungsionalitas yang sesuai dengan perannya.
