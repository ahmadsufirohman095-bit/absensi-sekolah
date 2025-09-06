<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountStatusChangedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $isActive;

    /**
     * Create a new notification instance.
     */
    public function __construct(bool $isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Mengubah channel menjadi database
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $status = $this->isActive ? 'diaktifkan' : 'dinonaktifkan';
        $message = 'Akun Anda telah ' . $status . ' oleh administrator.';

        return [
            'message' => $message,
            'status' => $status,
            'is_active' => $this->isActive,
            'action_by' => auth()->user()->name ?? 'Sistem',
        ];
    }
}
