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
        'role_target',
        'card_orientation',
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

    /**
     * Get a merged print card configuration with default fallback values.
     *
     * @param self|null $existingConfig
     * @return self
     */
    public static function getMergedConfig(?self $existingConfig = null): self
    {
        $defaultConfigTemplate = [
            'name' => 'Tema Default',
            'is_default' => false,
            'role_target' => null,
            'card_orientation' => 'portrait',
            'config_json' => [
                'theme' => [
                    'background_color' => '#ffffff',
                    'background_opacity' => 1,
                    'header_background_color' => '#1e3a8a',
                    'header_background_opacity' => 1,
                    'text_color_header' => '#ffffff',
                    'text_color_body' => '#333333',
                ],
                'assets' => [
                    'logo_path' => null,
                    'logo_url' => null,
                    'watermark_path' => null,
                    'watermark_url' => null,
                    'watermark_opacity' => 0.1,
                    'watermark_size' => 70,
                    'watermark_position_y' => 50
                ],
                'selected_fields' => ['foto', 'name', 'nis', 'kelas', 'tanggal_lahir'],
                'qr_size' => 70,
                'photo_width' => 70,
                'photo_height' => 90,
                'header_title' => 'Kartu Absensi Siswa',
                'school_name' => 'Nama Sekolah Contoh',
                'header_padding_x' => 8,
                'qr_position_x' => 75,
                'qr_position_y' => 75,
                'watermark_enabled' => true, // Ensure this is also defaulted
            ]
        ];

        if (!$existingConfig) {
            $existingConfig = new self($defaultConfigTemplate);
            $existingConfig->exists = false; // Mark as not existing in DB
            $existingConfig->config_json = $defaultConfigTemplate['config_json'];
            return $existingConfig;
        }

        // Deep merge config_json
        $mergedConfigJson = array_replace_recursive(
            $defaultConfigTemplate['config_json'],
            $existingConfig->config_json
        );

        $existingConfig->config_json = $mergedConfigJson;
        // Also ensure card_orientation is set if missing
        if (!isset($existingConfig->card_orientation)) {
            $existingConfig->card_orientation = $defaultConfigTemplate['card_orientation'];
        }

        return $existingConfig;
    }
}
