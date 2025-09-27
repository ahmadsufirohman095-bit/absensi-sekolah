<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class GuruProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'telepon',
        'jabatan',
        'tanggal_bergabung',
        'tempat_lahir',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
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
            \Log::info('GuruProfile setTanggalLahirAttribute - Input value:', ['value' => $value]);
            try {
                // Coba format d/m/Y
                $this->attributes['tanggal_lahir'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                \Log::info('GuruProfile setTanggalLahirAttribute - Formatted d/m/Y:', ['formatted' => $this->attributes['tanggal_lahir']]);
            } catch (\Exception $e) {
                // Jika gagal, coba parse secara umum
                try {
                    $this->attributes['tanggal_lahir'] = Carbon::parse($value)->format('Y-m-d');
                    \Log::info('GuruProfile setTanggalLahirAttribute - Parsed general:', ['parsed' => $this->attributes['tanggal_lahir']]);
                } catch (\Exception $e2) {
                    // Jika masih gagal, set null
                    $this->attributes['tanggal_lahir'] = null;
                    \Log::warning('GuruProfile setTanggalLahirAttribute - Failed to parse date, setting to null:', ['value' => $value, 'error' => $e2->getMessage()]);
                }
            }
        } else {
            $this->attributes['tanggal_lahir'] = null;
        }
    }

    /**
     * Get the user that owns this teacher profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
