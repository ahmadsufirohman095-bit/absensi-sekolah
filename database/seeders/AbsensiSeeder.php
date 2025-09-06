<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absensi;
use App\Models\User;
use App\Models\JadwalAbsensi;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        Schema::disableForeignKeyConstraints();
        DB::table('absensis')->truncate();
        Schema::enableForeignKeyConstraints();

        $jadwalAbsensis = JadwalAbsensi::with(['kelas', 'mataPelajaran'])->get();

        if ($jadwalAbsensis->isEmpty()) {
            $this->command->warn('Tidak ada Jadwal Absensi yang tersedia. Lewati AbsensiSeeder.');
            return;
        }

        $this->command->info('Membuat data absensi berdasarkan jadwal yang ada...');

        $statuses = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];
        $attendanceTypes = ['manual', 'qr_code'];
        $keteranganOptions = [
            'sakit' => ['Sakit demam', 'Sakit perut', 'Pusing'],
            'izin' => ['Acara keluarga', 'Keperluan mendesak', 'Surat izin terlampir'],
            'alpha' => ['Tanpa keterangan'],
            'terlambat' => ['Bangun kesiangan', 'Ban motor bocor'],
        ];

        // Seed absensi untuk 4 minggu terakhir
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subWeeks(4);

        // Mapping nama hari ke konstanta Carbon
        $dayOfWeekMap = [
            'Senin' => Carbon::MONDAY,
            'Selasa' => Carbon::TUESDAY,
            'Rabu' => Carbon::WEDNESDAY,
            'Kamis' => Carbon::THURSDAY,
            'Jumat' => Carbon::FRIDAY,
            'Sabtu' => Carbon::SATURDAY,
            'Minggu' => Carbon::SUNDAY,
        ];

        foreach ($jadwalAbsensis as $jadwal) {
            if (!$jadwal->kelas || !$jadwal->mataPelajaran) {
                continue; // Lewati jika jadwal tidak terhubung dengan kelas atau mapel
            }

            $this->command->warn(" -> Memproses jadwal untuk Kelas: {$jadwal->kelas->nama_kelas} - Mapel: {$jadwal->mataPelajaran->nama_mapel} pada hari {$jadwal->hari}");

            // Dapatkan semua siswa di kelas ini
            $siswas = User::where('role', 'siswa')
                ->whereHas('siswaProfile', function ($query) use ($jadwal) {
                    $query->where('kelas_id', $jadwal->kelas_id);
                })->get();

            if ($siswas->isEmpty()) {
                continue; // Lewati jika tidak ada siswa di kelas ini
            }

            // Dapatkan tanggal-tanggal yang sesuai dengan hari jadwal dalam rentang waktu
            $scheduleDayOfWeek = $dayOfWeekMap[$jadwal->hari] ?? null;
            if ($scheduleDayOfWeek === null) {
                continue;
            }

            $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate);
            foreach ($period as $date) {
                $currentDate = Carbon::instance($date);
                if ($currentDate->dayOfWeek !== $scheduleDayOfWeek) {
                    continue; // Hanya proses tanggal yang harinya cocok
                }

                // Buat absensi untuk setiap siswa di kelas pada tanggal ini
                foreach ($siswas as $siswa) {
                    $status = $faker->randomElement($statuses);
                    $waktuMasuk = null;
                    $keterangan = null;

                    if ($status === 'hadir') {
                        // Hadir tepat waktu
                        $waktuMasuk = Carbon::parse($jadwal->jam_mulai)->format('H:i:s');
                    } elseif ($status === 'terlambat') {
                        // Terlambat antara 5 - 30 menit
                        $waktuMasuk = Carbon::parse($jadwal->jam_mulai)->addMinutes($faker->numberBetween(5, 30))->format('H:i:s');
                        $keterangan = $faker->randomElement($keteranganOptions['terlambat']);
                    } elseif (in_array($status, ['sakit', 'izin', 'alpha'])) {
                        $keterangan = $faker->randomElement($keteranganOptions[$status]);
                    }

                    Absensi::create([
                        'user_id' => $siswa->id,
                        'jadwal_absensi_id' => $jadwal->id,
                        'tanggal_absensi' => $currentDate->format('Y-m-d'),
                        'status' => $status,
                        'waktu_masuk' => $waktuMasuk,
                        'keterangan' => $keterangan,
                        'attendance_type' => $faker->randomElement($attendanceTypes),
                    ]);
                }
            }
        }

        $this->command->info('Data Absensi yang realistis berhasil dibuat.');
    }
}