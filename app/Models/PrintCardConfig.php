<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintCardConfig extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'config_json',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'config_json' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the PrintCardConfig.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}