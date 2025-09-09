<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CardAssetController extends Controller
{
    /**
     * Store a newly uploaded card asset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:5120'],
            'old_path' => ['nullable', 'string'], // Validate the old path
        ]);

        // Delete the old file if the path is provided
        if ($request->filled('old_path')) {
            $oldPath = $request->input('old_path');
            // Basic security check to prevent traversing directories
            if (strpos($oldPath, '..') === false && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        if ($request->hasFile('file')) {
            // Store the new file in storage/app/public/card_assets
            $path = $request->file('file')->store('card_assets', 'public');

            // Return the path and the public URL
            return response()->json([
                'path' => $path,
                'url' => Storage::url($path)
            ], 201);
        }

        return response()->json(['error' => 'File not found.'], 400);
    }

    /**
     * Remove the specified asset from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);

        $path = $request->input('path');

        // Basic security check to prevent traversing directories
        if (strpos($path, '..') !== false || !Storage::disk('public')->exists($path)) {
            return response()->json(['error' => 'File not found or invalid path.'], 404);
        }

        // Another security check to ensure we are only deleting files in card_assets
        if (strpos($path, 'card_assets/') !== 0) {
            return response()->json(['error' => 'Invalid path specified.'], 400);
        }

        Storage::disk('public')->delete($path);

        return response()->json(['success' => 'File deleted successfully.']);
    }
}
