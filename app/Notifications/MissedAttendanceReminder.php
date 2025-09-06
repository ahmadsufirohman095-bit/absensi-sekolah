<?php

namespace App\Notifications;

use App\Models\JadwalAbsensi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissedAttendanceReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $jadwal;

    /**
     * Create a new notification instance.
     */
    public function __construct(JadwalAbsensi $jadwal)
    {
        $this->jadwal = $jadwal;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $mataPelajaran = $this->jadwal->mataPelajaran->nama_mapel;
        $kelas = $this->jadwal->kelas->nama_kelas;
        $jam = \Carbon\Carbon::parse($this->jadwal->jam_selesai)->format('H:i');

        return [
            'title' => 'Lupa Mengisi Absensi?',
            'message' => "Anda belum mengisi absensi untuk mapel {$mataPelajaran} di kelas {$kelas} yang berakhir jam {$jam}.",
            'link' => route('jadwal.absensi.create', $this->jadwal->id),
            'icon' => 'fas fa-clock text-yellow-500', // Font Awesome icon
        ];
    }
}