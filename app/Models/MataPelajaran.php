<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $fillable = ['kode_mapel', 'nama_mapel', 'deskripsi'];

    public function gurus()
    {
        return $this->belongsToMany(User::class, 'guru_mata_pelajaran', 'mata_pelajaran_id', 'user_id');
    }

    /**
     * Relasi Many-to-Many ke Kelas.
     * Satu mata pelajaran bisa diajarkan di banyak kelas.
     */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'jadwal_absensis', 'mata_pelajaran_id', 'kelas_id')->distinct()->orderBy('nama_kelas');
    }
}
