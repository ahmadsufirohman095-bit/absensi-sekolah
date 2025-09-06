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
     * Get the user that owns this teacher profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
