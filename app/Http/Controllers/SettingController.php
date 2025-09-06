<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Laravel\Facades\Image;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->all();
        return view('pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'login_title' => 'nullable|string|max:50',
            'login_subtitle' => 'nullable|string|max:100',
            'login_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'login_background' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($request->hasFile('login_logo')) {
            // Hapus logo dan favicon lama
            $this->deleteOldFile('login_logo');
            $this->deleteOldFile('favicon', false); // Favicon tidak di-database, hapus langsung

            // Simpan logo baru
            $path = $request->file('login_logo')->store('settings', 'public');
            $validated['login_logo'] = $path;

            // Buat dan simpan favicon
            $faviconPath = 'favicon.ico';
            Image::read(Storage::disk('public')->path($path))
                ->resize(32, 32)
                ->save(public_path($faviconPath));
            
            Setting::updateOrCreate(['key' => 'favicon'], ['value' => $faviconPath]);
        }

        if ($request->hasFile('login_background')) {
            $this->deleteOldFile('login_background');
            $path = $request->file('login_background')->store('settings', 'public');
            $validated['login_background'] = $path;
        }

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('settings');

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    private function deleteOldFile($key, $checkDb = true)
    {
        $path = null;
        if ($checkDb) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $path = $setting->value;
            }
        } else {
            // Untuk file seperti favicon yang path-nya kita tahu
            $path = $key;
        }

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
