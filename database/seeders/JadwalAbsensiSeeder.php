<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalAbsensi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JadwalAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Nonaktifkan foreign key untuk truncate dengan aman
        Schema::disableForeignKeyConstraints();
        DB::table('jadwal_absensis')->truncate();
        Schema::enableForeignKeyConstraints();

        $kelas = Kelas::all();
        $mataPelajaran = MataPelajaran::all();
        $gurus = User::where('role', 'guru')->get();

        if ($kelas->isEmpty() || $mataPelajaran->isEmpty() || $gurus->isEmpty()) {
            $this->command->warn('Tidak ada Kelas, Mata Pelajaran, atau Guru yang tersedia untuk membuat Jadwal Absensi. Lewati seeder ini.');
            return;
        }

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; // Typically school days

        $timeSlots = [
            ['07:00', '08:00'], ['08:00', '09:00'], ['09:00', '10:00'],
            ['10:00', '11:00'], ['11:00', '12:00'], ['13:00', '14:00'],
            ['14:00', '15:00'], ['15:00', '16:00'],
        ];

        foreach ($kelas as $kela) {
            foreach ($days as $day) {
                // Assign 3-5 random subjects per day for each class
                $numSubjects = $faker->numberBetween(3, 5);
                $assignedSubjects = $mataPelajaran->shuffle()->take($numSubjects);

                $usedTimeSlots = [];

                foreach ($assignedSubjects as $subject) {
                    // Find an unused time slot
                    $availableTimeSlots = array_diff_key($timeSlots, $usedTimeSlots);
                    if (empty($availableTimeSlots)) {
                        break; // No more time slots for this day
                    }

                    $randomTimeSlotKey = array_rand($availableTimeSlots);
                    $timeSlot = $availableTimeSlots[$randomTimeSlotKey];
                    $usedTimeSlots[$randomTimeSlotKey] = true; // Mark as used

                    // Assign a random guru
                    $randomGuru = $gurus->random();

                    JadwalAbsensi::create([
                        'kelas_id' => $kela->id,
                        'mata_pelajaran_id' => $subject->id,
                        'guru_id' => $randomGuru->id,
                        'hari' => $day,
                        'jam_mulai' => $timeSlot[0] . ':00',
                        'jam_selesai' => $timeSlot[1] . ':00',
                    ]);
                }
            }
        }

        $this->command->info('Jadwal Absensi realistis berhasil dibuat.');
    }
}
