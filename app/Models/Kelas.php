<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kelas', 'wali_kelas_id'];

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    /**
     * Mendefinisikan relasi ke profil siswa.
     * Satu kelas bisa memiliki banyak siswa.
     */
    public function siswaProfiles()
    {
        return $this->hasMany(SiswaProfile::class);
    }

    /**
     * Relasi Many-to-Many ke MataPelajaran.
     * Satu kelas bisa memiliki banyak mata pelajaran.
     */
    public function mataPelajarans()
    {
        return $this->belongsToMany(MataPelajaran::class, 'kelas_mata_pelajaran');
    }

    /**
     * Get the attendance schedules for the class.
     */
    public function jadwalAbsensis()
    {
        return $this->hasMany(JadwalAbsensi::class);
    }
}
