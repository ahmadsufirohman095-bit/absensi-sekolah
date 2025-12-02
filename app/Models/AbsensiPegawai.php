<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsensiPegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'jadwal_absensi_pegawai_id',
        'tanggal_absensi',
        'status',
        'waktu_masuk',
        'keterangan',
        'attendance_type',
    ];

    protected $casts = [
        'tanggal_absensi' => 'date',
        'waktu_masuk' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function jadwalAbsensiPegawai()
    {
        return $this->belongsTo(JadwalAbsensiPegawai::class)->withTrashed();
    }
}
