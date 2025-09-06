<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        $settings = Cache::rememberForever('settings', function () {
            return Setting::all()->pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('logo_url')) {
    function logo_url()
    {
        $logoPath = setting('login_logo');
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            return asset('storage/' . $logoPath);
        }
        // Kembalikan path ke logo default jika tidak ada logo yang diunggah
        return asset('images/icon_mts_al_muttaqin.png');
    }
}

if (! function_exists('get_total_days_in_month')) {
    function get_total_days_in_month($month, $year) {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }
}
