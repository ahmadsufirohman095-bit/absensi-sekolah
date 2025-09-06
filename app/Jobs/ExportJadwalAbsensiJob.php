<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalAbsensiExport;
use App\Models\User; // Import User model if needed for notifications
use App\Models\JadwalAbsensi; // Import JadwalAbsensi model

class ExportJadwalAbsensiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $kelasId;
    protected $mataPelajaranId;
    protected $hari;
    protected $userId; // To notify the user after export

    /**
     * Create a new job instance.
     */
    public function __construct($kelasId, $mataPelajaranId, $hari, $userId)
    {
        $this->kelasId = $kelasId;
        $this->mataPelajaranId = $mataPelajaranId;
        $this->hari = $hari;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = JadwalAbsensi::with(['kelas', 'mataPelajaran']);

        if ($this->kelasId) {
            $query->where('kelas_id', $this->kelasId);
        }

        if ($this->mataPelajaranId) {
            $query->where('mata_pelajaran_id', $this->mataPelajaranId);
        }

        if ($this->hari) {
            $query->where('hari', $this->hari);
        }

        $fileName = 'jadwal_absensi-' . now()->format('d-m-Y_His') . '.xlsx';
        $filePath = 'exports/' . $fileName; // Path within storage/app

        Excel::store(new JadwalAbsensiExport($query), $filePath);

        // Notify the user
        $user = User::find($this->userId);
        if ($user) {
            \Log::info("Laporan jadwal absensi untuk user {$user->id} berhasil diekspor ke: {$filePath}");
        }
    }
}
