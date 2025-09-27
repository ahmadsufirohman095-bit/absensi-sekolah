<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiswaProfile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'kelas_id',
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'telepon_ayah',
        'telepon_ibu',
        'nama_ayah',
        'nama_ibu',
        'foto'
    ];

    protected $casts = ['tanggal_lahir' => 'date'];

    /**
     * Set the user's date of birth.
     *
     * @param  string  $value
     * @return void
     */
    public function setTanggalLahirAttribute($value)
    {
        if ($value) {
            try {
                $this->attributes['tanggal_lahir'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->attributes['tanggal_lahir'] = Carbon::parse($value)->format('Y-m-d');
            }
        } else {
            $this->attributes['tanggal_lahir'] = null;
        }
    }

    /**
     * Get the user that owns this student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
