<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil UserSeeder yang telah kita buat
        $this->call([
            UserSeeder::class,
            JadwalAbsensiSeeder::class,
            AbsensiSeeder::class,
            JadwalAbsensiPegawaiSeeder::class,
            AbsensiPegawaiSeeder::class, // Tambahkan seeder absensi pegawai
        ]);
    }
}
