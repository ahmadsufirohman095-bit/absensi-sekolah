<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['key', 'value']; // <-- TAMBAHKAN BARIS INI

    /**
     * Mendapatkan nilai pengaturan sebagai boolean.
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Mendapatkan nilai pengaturan sebagai integer.
     */
    public static function getInt(string $key, int $default = 0): int
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        return (int) $setting->value;
    }
}