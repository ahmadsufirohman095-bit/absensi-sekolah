<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'jabatan',
        'telepon',
        'tanggal_bergabung',
        'tanggal_lahir', // Add tanggal_lahir to fillable
        'tempat_lahir',
        'jenis_kelamin',
        'foto', // Added this line
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_lahir' => 'date', // Add tanggal_lahir to casts
    ];

    /**
     * Get the user that owns this admin profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
