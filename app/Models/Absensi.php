<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'tanggal_absensi', 'waktu_masuk', 'jadwal_absensi_id', 'status', 'keterangan', 'attendance_type'];

    protected $casts = [
        'tanggal_absensi' => 'date',
        'waktu_masuk' => 'datetime', // Cast to datetime for easier manipulation
    ];

    /**
     * Mendefinisikan relasi ke model User.
     * Setiap data absensi dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the jadwalAbsensi that owns the Absensi
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jadwalAbsensi()
    {
        return $this->belongsTo(JadwalAbsensi::class);
    }
}
