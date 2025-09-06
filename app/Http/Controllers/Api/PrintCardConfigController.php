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
    public function index()
    {
        return PrintCardConfig::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'boolean',
            'config_json' => 'required|json',
        ]);

        $configJson = json_decode($validated['config_json'], true);

        $config = new PrintCardConfig([
            'name' => $validated['name'],
            'is_default' => $validated['is_default'] ?? false,
            'config_json' => $configJson,
            'user_id' => auth()->id(),
        ]);

        DB::transaction(function () use ($config) {
            if ($config->is_default) {
                PrintCardConfig::where('is_default', true)->update(['is_default' => false]);
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
        ]);

        DB::transaction(function () use ($validated, $printCardConfig) {
            if (isset($validated['is_default']) && $validated['is_default']) {
                PrintCardConfig::where('id', '!=', $printCardConfig->id)
                               ->where('is_default', true)
                               ->update(['is_default' => false]);
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
}