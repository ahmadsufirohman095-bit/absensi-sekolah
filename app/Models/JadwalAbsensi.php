<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalAbsensi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jadwal_absensis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'guru_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
    ];

    /**
     * Get the kelas that owns the JadwalAbsensi.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Get the mata pelajaran that owns the JadwalAbsensi.
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    /**
     * Get all of the absensis for the JadwalAbsensi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'jadwal_absensi_id');
    }

    /**
     * Get the guru that owns the JadwalAbsensi.
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
