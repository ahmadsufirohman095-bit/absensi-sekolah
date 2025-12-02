<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PrintCardConfig;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class PrintCardsController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan semua konfigurasi kartu cetak yang tersedia
        $configs = PrintCardConfig::all();
        $selectedConfigId = $request->query('config_id', optional($configs->where('is_default', true)->first())->id);
        
        // Query dasar untuk user (tidak termasuk admin yang sedang login)
        $query = User::where('id', '!=', auth()->id())
                     ->where('role', '!=', 'siswa') // Mengecualikan role siswa
                     ->with(['adminProfile', 'guruProfile', 'tuProfile', 'otherProfile']);

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan pencarian nama, email, atau ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('created_at')->paginate(50);

        return view('admin.print_cards.index', compact('users', 'configs', 'selectedConfigId'));
    }

    public function generateCards(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'config_id' => 'nullable|exists:print_card_configs,id',
        ]);

        $userIds = $request->input('user_ids');
        $configId = $request->input('config_id');

        $users = User::whereIn('id', $userIds)
                     ->with(['adminProfile', 'guruProfile', 'siswaProfile.kelas', 'tuProfile', 'otherProfile'])
                     ->get();

        $config = null;
        $targetRole = $users->first()->role ?? null; // Assume all users in the batch have the same role or use the first one's role

        if ($configId) {
            $config = PrintCardConfig::find($configId);
        }

        // If no specific config is requested or found, try to find a default config
        if (!$config) {
            // First, try to find a default config specific to the target role
            if ($targetRole) {
                $config = PrintCardConfig::where('is_default', true)
                                         ->where('role_target', $targetRole)
                                         ->first();
            }
            // If no role-specific default or no target role, try to find a general default
            if (!$config) {
                $config = PrintCardConfig::where('is_default', true)
                                         ->whereNull('role_target')
                                         ->first();
            }
        }

        $config = PrintCardConfig::getMergedConfig($config);
        
        // Transform the users collection to match the expected format for kelas.print_cards
        // Transform the users collection to match the expected format for kelas.print_cards
        $siswa = $users->map(function ($user) {
            // Ensure siswaProfile exists for all users, even if it's a dummy for non-siswa
            if ($user->role === 'siswa' && $user->siswaProfile) {
                $user->identifier = $user->siswaProfile->nis;
                $user->foto_url = $user->siswaProfile->foto ? asset('storage/' . $user->siswaProfile->foto) : asset('images/default-avatar.svg');
                // Ensure tanggal_lahir and tempat_lahir are accessible from siswaProfile
                $user->siswaProfile->tanggal_lahir = $user->siswaProfile->tanggal_lahir ?? null;
                $user->siswaProfile->tempat_lahir = $user->siswaProfile->tempat_lahir ?? null;
            } else {
                // For non-siswa roles, assign identifier and foto_url directly to the user object
                // Also create a dummy siswaProfile with null values for relevant fields
                $profile = $user->adminProfile ?? $user->guruProfile ?? $user->tuProfile ?? $user->otherProfile;
                $user->identifier = $profile->nip ?? $profile->id ?? $user->id; // Use NIP or ID as identifier
                $user->foto_url = ($profile && $profile->foto) ? asset('storage/' . $profile->foto) : asset('images/default-avatar.svg');
                $user->siswaProfile = (object) [
                    'nis' => null, // Non-siswa won't have NIS
                    'kelas' => (object) ['nama_kelas' => '-'], // Default kelas
                    'tanggal_lahir' => null,
                    'tempat_lahir' => null,
                ];
                // For non-siswa, use their profile's tanggal_lahir if available
                if ($user->role === 'guru' && $user->guruProfile) {
                    $user->siswaProfile->tanggal_lahir = $user->guruProfile->tanggal_lahir;
                    $user->siswaProfile->tempat_lahir = $user->guruProfile->tempat_lahir;
                } elseif ($user->role === 'admin' && $user->adminProfile) {
                     $user->siswaProfile->tanggal_lahir = $user->adminProfile->tanggal_lahir;
                     $user->siswaProfile->tempat_lahir = $user->adminProfile->tempat_lahir;
                } elseif ($user->role === 'tu' && $user->tuProfile) {
                    $user->siswaProfile->tanggal_lahir = $user->tuProfile->tanggal_lahir;
                    $user->siswaProfile->tempat_lahir = $user->tuProfile->tempat_lahir;
                } elseif ($user->role === 'other' && $user->otherProfile) {
                    $user->siswaProfile->tanggal_lahir = $user->otherProfile->tanggal_lahir;
                    $user->siswaProfile->tempat_lahir = $user->otherProfile->tempat_lahir;
                }
            }
            return $user;
        });

        return view('kelas.print_cards', compact('siswa', 'config'));
    }
}
