<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AbsensiPegawai;
use App\Models\JadwalAbsensiPegawai;
use Carbon\Carbon;

class AbsensiPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikasi saat menjalankan seeder berkali-kali
        AbsensiPegawai::truncate();

        // Ambil semua pegawai (guru, tu, other)
        $pegawais = User::whereIn('role', ['guru', 'tu', 'other'])->get();

        // Ambil jadwal absensi pegawai yang sudah ada, atau buat dummy jika belum ada
        $jadwalAbsensiPegawais = JadwalAbsensiPegawai::all();

        // Status absensi yang mungkin
        $statuses = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];

        foreach ($pegawais as $pegawai) {
            // Buat 10-20 data absensi untuk setiap pegawai
            for ($i = 0; $i < rand(10, 20); $i++) {
                $date = Carbon::now()->subDays(rand(1, 60)); // Absensi dalam 60 hari terakhir
                $status = $statuses[array_rand($statuses)];
                $waktuMasuk = $date->copy()->setTime(rand(7, 9), rand(0, 59), 0);

                // Pilih jadwal absensi pegawai secara acak jika ada
                $jadwalAbsensiPegawaiId = $jadwalAbsensiPegawais->isEmpty() ? null : $jadwalAbsensiPegawais->random()->id;

                AbsensiPegawai::create([
                    'user_id' => $pegawai->id,
                    'jadwal_absensi_pegawai_id' => $jadwalAbsensiPegawaiId,
                    'tanggal_absensi' => $date->format('Y-m-d'),
                    'status' => $status,
                    'waktu_masuk' => $waktuMasuk->format('H:i:s'),
                    'keterangan' => $status === 'sakit' ? 'Surat dokter' : ($status === 'izin' ? 'Keperluan pribadi' : null),
                    'attendance_type' => 'manual',
                ]);
            }
        }
    }
}
