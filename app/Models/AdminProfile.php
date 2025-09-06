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
        'tempat_lahir',
        'jenis_kelamin',
        'foto', // Added this line
    ];

    protected $casts = ['tanggal_bergabung' => 'date'];

    /**
     * Get the user that owns this admin profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
