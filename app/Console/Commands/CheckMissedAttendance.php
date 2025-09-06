<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\JadwalAbsensi;
use App\Models\Absensi;
use App\Notifications\MissedAttendanceReminder;

class CheckMissedAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-missed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for classes that have ended but attendance has not been taken, and notify the teacher.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting check for missed attendance...');

        $now = Carbon::now();
        // Window: Check for classes that ended between 60 and 15 minutes ago.
        $checkFrom = $now->copy()->subMinutes(60);
        $checkTo = $now->copy()->subMinutes(15);

        $dayMap = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariIni = $dayMap[$now->dayOfWeek];

        // Find schedules that ended within our time window for the current day of the week.
        $jadwals = JadwalAbsensi::with(['guru', 'mataPelajaran', 'kelas'])
            ->where('hari', $hariIni)
            ->whereTime('jam_selesai', '>=', $checkFrom->toTimeString())
            ->whereTime('jam_selesai', '<=', $checkTo->toTimeString())
            ->get();

        if ($jadwals->isEmpty()) {
            $this->info('No schedules to check in the current time window.');
            return Command::SUCCESS;
        }

        $this->info("Found {$jadwals->count()} schedule(s) to check.");

        $missedCount = 0;
        foreach ($jadwals as $jadwal) {
            // Check if an attendance record exists for this specific schedule on today's date.
            $absensiExists = Absensi::where('jadwal_absensi_id', $jadwal->id)
                ->whereDate('tanggal_absensi', $now->toDateString())
                ->exists();

            // If attendance does NOT exist and the teacher exists, send a notification.
            if (!$absensiExists && $jadwal->guru) {
                $this->warn("Missed attendance found for: {$jadwal->mataPelajaran->nama_mapel} - {$jadwal->kelas->nama_kelas}. Notifying {$jadwal->guru->name}.");
                
                // Use the notification class we created.
                $jadwal->guru->notify(new MissedAttendanceReminder($jadwal));
                $missedCount++;
            }
        }

        if ($missedCount > 0) {
            $this->info("Sent {$missedCount} missed attendance notifications.");
        } else {
            $this->info('All relevant schedules had their attendance taken.');
        }
        
        $this->info('Check complete.');
        return Command::SUCCESS;
    }
}