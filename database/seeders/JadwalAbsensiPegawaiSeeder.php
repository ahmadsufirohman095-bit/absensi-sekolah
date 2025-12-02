<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalAbsensiPegawai;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JadwalAbsensiPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua user yang ada
        $users = User::whereNotIn('role', ['siswa'])->get();

        if ($users->isEmpty()) {
            $this->command->info('Tidak ada user untuk dihubungkan dengan jadwal absensi pegawai.');
            return;
        }

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamMulai = ['07:00:00', '08:00:00', '09:00:00'];
        $jamSelesai = ['15:00:00', '16:00:00', '17:00:00'];

        foreach ($users as $user) {
            // Buat beberapa jadwal untuk setiap user
            foreach (range(1, 2) as $index) { // Setiap user mendapatkan 2 jadwal dummy
                JadwalAbsensiPegawai::create([
                    'user_id' => $user->id,
                    'hari' => $hari[array_rand($hari)],
                    'jam_mulai' => $jamMulai[array_rand($jamMulai)],
                    'jam_selesai' => $jamSelesai[array_rand($jamSelesai)],
                    'keterangan' => 'Jadwal kerja reguler',
                ]);
            }
        }
    }
}
