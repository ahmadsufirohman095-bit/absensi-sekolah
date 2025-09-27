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
            \Log::info('SiswaProfile setTanggalLahirAttribute - Input value:', ['value' => $value]);
            try {
                // Coba format d/m/Y
                $this->attributes['tanggal_lahir'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                \Log::info('SiswaProfile setTanggalLahirAttribute - Formatted d/m/Y:', ['formatted' => $this->attributes['tanggal_lahir']]);
            } catch (\Exception $e) {
                // Jika gagal, coba parse secara umum
                try {
                    $this->attributes['tanggal_lahir'] = Carbon::parse($value)->format('Y-m-d');
                    \Log::info('SiswaProfile setTanggalLahirAttribute - Parsed general:', ['parsed' => $this->attributes['tanggal_lahir']]);
                } catch (\Exception $e2) {
                    // Jika masih gagal, set null
                    $this->attributes['tanggal_lahir'] = null;
                    \Log::warning('SiswaProfile setTanggalLahirAttribute - Failed to parse date, setting to null:', ['value' => $value, 'error' => $e2->getMessage()]);
                }
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
