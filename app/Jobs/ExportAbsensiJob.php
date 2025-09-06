<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;
use App\Models\User; // Import User model if needed for notifications

class ExportAbsensiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tanggal;
    protected $kelasId;
    protected $userId; // To notify the user after export

    /**
     * Create a new job instance.
     */
    public function __construct($tanggal, $kelasId, $userId)
    {
        $this->tanggal = $tanggal;
        $this->kelasId = $kelasId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileName = 'laporan-absensi-' . now()->format('d-m-Y_His') . '.xlsx';
        $filePath = 'exports/' . $fileName; // Path within storage/app

        Excel::store(new AbsensiExport($this->tanggal, $this->kelasId), $filePath);

        // Notify the user (e.g., via database notification, email, etc.)
        // For simplicity, let's assume a basic notification system or log
        $user = User::find($this->userId);
        if ($user) {
            // Example: Log a message or send a simple notification
            // You might want to implement a proper notification system (e.g., Laravel Notifications)
            \Log::info("Laporan absensi untuk user {$user->id} berhasil diekspor ke: {$filePath}");
            // Example: $user->notify(new ReportExported($filePath));
        }
    }
}
