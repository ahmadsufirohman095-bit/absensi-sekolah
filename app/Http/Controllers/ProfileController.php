<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Eager load relasi profil sesuai role user yang login
        $user = $request->user();
        $user->load(['adminProfile', 'guruProfile', 'siswaProfile']);

        // Kosongkan profil yang tidak sesuai role agar data tidak bercampur di form profil
        switch ($user->role) {
            case 'admin':
                $user->guruProfile = null;
                $user->siswaProfile = null;
                break;
            case 'guru':
                $user->adminProfile = null;
                $user->siswaProfile = null;
                break;
            case 'siswa':
                $user->adminProfile = null;
                $user->guruProfile = null;
                break;
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Validate all incoming data
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            
            // Profile fields
            'jabatan' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
            'alamat' => ['nullable', 'string'],
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'telepon_ayah' => ['nullable', 'string', 'max:255'],
            'telepon_ibu' => ['nullable', 'string', 'max:255'],
        ]);

        // 2. Update User model's core data
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->username = $validatedData['username'];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // 3. Prepare and update profile data
        $profile = $user->profile;
        if ($profile) {
            $profileData = [];
            
            // Collect data based on role
            if ($user->role === 'admin') {
                $profileData = $request->only(['jabatan', 'telepon', 'tempat_lahir', 'jenis_kelamin']);
            } elseif ($user->role === 'guru') {
                $profileData = $request->only(['jabatan', 'telepon', 'tanggal_lahir', 'tempat_lahir', 'jenis_kelamin', 'alamat']);
            } elseif ($user->role === 'siswa') {
                $profileData = $request->only(['tanggal_lahir', 'tempat_lahir', 'jenis_kelamin', 'alamat', 'nama_ayah', 'nama_ibu', 'telepon_ayah', 'telepon_ibu']);
            }

            // Handle photo upload
            if ($request->hasFile('foto')) {
                // Delete old photo
                if ($profile->foto) {
                    Storage::disk('public')->delete($profile->foto);
                }
                // Store new photo
                $profileData['foto'] = $request->file('foto')->store('fotos', 'public');
            }

            // Update the profile
            if (!empty($profileData)) {
                $profile->update($profileData);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
