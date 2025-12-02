<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrintCardConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PrintCardConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PrintCardConfig::query();

        if ($request->has('role_target') && $request->role_target !== null) {
            $query->where('role_target', $request->role_target);
        } else {
            // Jika role_target tidak disediakan, ambil semua konfigurasi yang tidak memiliki role_target
            // atau tambahkan logika lain sesuai kebutuhan default.
            // Untuk saat ini, jika tidak ada role_target yang diminta, kembalikan semua.
        }

        return $query->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'boolean',
            'config_json' => 'required|array',
            'role_target' => ['nullable', 'string', Rule::in(['siswa', 'guru', 'tu', 'other'])],
            'card_orientation' => ['required', 'string', Rule::in(['portrait', 'landscape'])], // Tambahkan validasi untuk card_orientation
        ]);

        $config = new PrintCardConfig([
            'name' => $validated['name'],
            'is_default' => $validated['is_default'] ?? false,
            'config_json' => $validated['config_json'],
            'user_id' => auth()->id(),
            'role_target' => $validated['role_target'] ?? null,
            'card_orientation' => $validated['card_orientation'] ?? 'portrait', // Simpan card_orientation
        ]);

        DB::transaction(function () use ($config) {
            if ($config->is_default) {
                $query = PrintCardConfig::where('is_default', true);
                if ($config->role_target) {
                    $query->where('role_target', $config->role_target);
                } else {
                    $query->whereNull('role_target');
                }
                $query->update(['is_default' => false]);
            }
            $config->save();
        });

        return response()->json($config, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PrintCardConfig $printCardConfig)
    {
        return $printCardConfig;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrintCardConfig $printCardConfig)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'boolean',
            'config_json' => 'sometimes|required|array',
            'role_target' => ['nullable', 'string', Rule::in(['siswa', 'guru', 'tu', 'other'])],
            'card_orientation' => ['sometimes', 'required', 'string', Rule::in(['portrait', 'landscape'])], // Tambahkan validasi untuk card_orientation
        ]);

        DB::transaction(function () use ($validated, $printCardConfig) {
            if (isset($validated['is_default']) && $validated['is_default']) {
                $query = PrintCardConfig::where('id', '!=', $printCardConfig->id)
                                       ->where('is_default', true);
                if (isset($validated['role_target']) && $validated['role_target']) {
                    $query->where('role_target', $validated['role_target']);
                } else {
                    $query->whereNull('role_target');
                }
                $query->update(['is_default' => false]);
            }
            $printCardConfig->update($validated);
        });

        return response()->json($printCardConfig);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrintCardConfig $printCardConfig)
    {
        if ($printCardConfig->is_default) {
            return response()->json(['error' => 'Tidak dapat menghapus konfigurasi default.'], 400);
        }

        $printCardConfig->delete();

        return response()->json(null, 204);
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Request $request, PrintCardConfig $printCardConfig)
    {
        $validated = $request->validate([
            'role_target' => ['nullable', 'string', Rule::in(['siswa', 'guru', 'tu', 'other'])],
            'name' => 'nullable|string|max:255', // Nama baru opsional
        ]);

        $duplicatedConfig = $printCardConfig->replicate();
        $duplicatedConfig->name = $validated['name'] ?? 'Salinan dari ' . $printCardConfig->name;
        $duplicatedConfig->role_target = $validated['role_target'] ?? null;
        $duplicatedConfig->is_default = false; // Duplikat tidak boleh menjadi default secara otomatis
        $duplicatedConfig->user_id = auth()->id(); // Atur user_id ke user yang sedang login

        $duplicatedConfig->save();

        return response()->json($duplicatedConfig, 201);
    }
}
