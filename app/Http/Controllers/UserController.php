<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules as ValidationRules;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\UsersTemplateExport;
use App\Imports\UsersImport;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;
use App\Notifications\AccountStatusChangedNotification;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('id', '!=', auth()->id())
                     ->with(['adminProfile', 'guruProfile', 'siswaProfile.kelas']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $kelasFilter = null;
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswaProfile', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
            $kelasFilter = Kelas::find($request->kelas_id);
        }
        
        $users = $query->latest('created_at')->paginate(50);
        $allKelas = Kelas::orderBy('nama_kelas')->get();
        
        return view('users.index', compact('users', 'kelasFilter', 'allKelas'));
    }

    public function show(User $user)
    {
        switch ($user->role) {
            case 'admin':
                $user->load('adminProfile');
                break;
            case 'guru':
                $user->load(['guruProfile', 'mataPelajarans']);
                break;
            case 'siswa':
                $user->load(['siswaProfile', 'siswaProfile.kelas']);
                break;
            case 'tu':
                $user->load('tuProfile');
                break;
            case 'other':
                $user->load('otherProfile');
                break;
        }
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        switch ($user->role) {
            case 'admin':
                $user->load('adminProfile');
                break;
            case 'guru':
                $user->load(['guruProfile', 'mataPelajarans']);
                break;
            case 'siswa':
                $user->load(['siswaProfile', 'siswaProfile.kelas']);
                break;
            case 'tu':
                $user->load('tuProfile');
                break;
            case 'other':
                $user->load('otherProfile');
                break;
        }

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mataPelajaranList = MataPelajaran::orderBy('nama_mapel')->get();

        return view('users.edit', compact('user', 'kelas', 'mataPelajaranList'));
    }

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mataPelajaranList = \App\Models\MataPelajaran::orderBy('nama_mapel')->get();
        $user = new User();
        return view('users.create', compact('kelas', 'mataPelajaranList', 'user'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users', 'username')->whereNull('deleted_at')],
            'identifier' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users', 'identifier')->whereNull('deleted_at')],
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->whereNull('deleted_at')],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa', 'tu', 'other'])],
            'custom_role_name' => ['nullable', 'string', 'max:255', 'required_if:role,other'],
            'password' => ['required', 'confirmed', ValidationRules\Password::min(8)],
            'is_active' => ['boolean'],
        ];

        if ($request->role === 'siswa') {
            $rules['kelas_id'] = ['nullable', 'exists:kelas,id'];
            $rules['siswa_tanggal_lahir'] = ['nullable', 'date'];
            $rules['alamat'] = ['nullable', 'string'];
            $rules['nama_ayah'] = ['nullable', 'string', 'max:255'];
            $rules['nama_ibu'] = ['nullable', 'string', 'max:255'];
            $rules['telepon_ayah'] = ['nullable', 'string', 'max:255'];
            $rules['telepon_ibu'] = ['nullable', 'string', 'max:255'];
            $rules['siswa_foto'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'];
            $rules['siswa_tempat_lahir'] = ['nullable', 'string', 'max:255'];
            $rules['siswa_jenis_kelamin'] = ['nullable', Rule::in(['laki-laki', 'perempuan'])];
        }

        $validatedData = $request->validate($rules);

        try {
            DB::transaction(function () use ($validatedData, $request) {
                $user = User::create([
                    'name' => $validatedData['name'],
                    'username' => $validatedData['username'],
                    'identifier' => $validatedData['identifier'],
                    'email' => $validatedData['email'],
                    'role' => $validatedData['role'],
                    'custom_role' => $validatedData['role'] === 'other' ? $validatedData['custom_role_name'] : null,
                    'password' => Hash::make($validatedData['password']),
                    'is_active' => $validatedData['is_active'] ?? true,
                ]);

                $profileData = [];
                $fotoPath = null;

                if ($user->role === 'siswa') {
                    $profileData = [
                        'kelas_id' => $validatedData['kelas_id'] ?? null,
                        'nis' => $validatedData['identifier'],
                        'nama_lengkap' => $validatedData['name'],
                        'tanggal_lahir' => $validatedData['siswa_tanggal_lahir'] ?? null,
                        'alamat' => $validatedData['alamat'] ?? null,
                        'nama_ayah' => $validatedData['nama_ayah'] ?? null,
                        'nama_ibu' => $validatedData['nama_ibu'] ?? null,
                        'telepon_ayah' => $validatedData['telepon_ayah'] ?? null,
                        'telepon_ibu' => $validatedData['telepon_ibu'] ?? null,
                        'jenis_kelamin' => $validatedData['siswa_jenis_kelamin'] ?? null,
                        'tempat_lahir' => $validatedData['siswa_tempat_lahir'] ?? null,
                    ];
                    if ($request->hasFile('siswa_foto')) {
                        $fotoPath = $request->file('siswa_foto')->store('fotos', 'public');
                    }
                }

                if ($fotoPath) {
                    $profileData['foto'] = $fotoPath;
                }

                if (!empty($profileData)) {
                    if ($user->role === 'siswa') {
                        $user->siswaProfile()->create($profileData);
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan user: ' . $e->getMessage());
        }

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)->whereNull('deleted_at')],
            'identifier' => ['required', 'string', 'max:255', Rule::unique('users', 'identifier')->ignore($user->id)->whereNull('deleted_at')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at')],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa', 'tu', 'other'])],
            'password' => ['nullable', 'confirmed', ValidationRules\Password::min(8)],
            'is_active' => ['boolean'],
        ];

        // Add validation for profile fields based on role
        if ($request->role === 'admin') {
            $rules += [
                'admin_jabatan' => ['nullable', 'string', 'max:255'],
                'admin_telepon' => ['nullable', 'string', 'max:255'],
                'tanggal_lahir' => ['nullable', 'date'],
                'admin_tempat_lahir' => ['nullable', 'string', 'max:255'],
                'admin_jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
                'admin_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ];
        } elseif ($request->role === 'guru') {
            $rules += [
                'guru_jabatan' => ['nullable', 'string', 'max:255'],
                'guru_telepon' => ['nullable', 'string', 'max:255'],
                'guru_tanggal_lahir' => ['nullable', 'date'],
                'guru_tempat_lahir' => ['nullable', 'string', 'max:255'],
                'guru_jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
                'alamat' => ['nullable', 'string'],
                'guru_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ];
        } elseif ($request->role === 'siswa') {
            $rules += [
                'kelas_id' => ['nullable', 'exists:kelas,id'],
                'siswa_tanggal_lahir' => ['nullable', 'date'],
                'siswa_tempat_lahir' => ['nullable', 'string', 'max:255'],
                'siswa_jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
                'nama_ayah' => ['nullable', 'string', 'max:255'],
                'nama_ibu' => ['nullable', 'string', 'max:255'],
                'telepon_ayah' => ['nullable', 'string', 'max:255'],
                'telepon_ibu' => ['nullable', 'string', 'max:255'],
                'alamat' => ['nullable', 'string'],
                'siswa_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ];
        } elseif ($request->role === 'tu') {
            $rules += [
                'tu_jabatan' => ['nullable', 'string', 'max:255'],
                'tu_telepon' => ['nullable', 'string', 'max:255'],
                'tu_tanggal_lahir' => ['nullable', 'date'],
                'tu_tempat_lahir' => ['nullable', 'string', 'max:255'],
                'tu_jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
                'tu_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ];
        } elseif ($request->role === 'other') {
            $rules += [
                'custom_role_name' => ['required', 'string', 'max:255'],
                'other_jabatan' => ['nullable', 'string', 'max:255'],
                'other_telepon' => ['nullable', 'string', 'max:255'],
                'other_tanggal_lahir' => ['nullable', 'date'],
                'other_tempat_lahir' => ['nullable', 'string', 'max:255'],
                'other_jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
                'alamat' => ['nullable', 'string'],
                'other_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ];
        }

        $validatedData = $request->validate($rules);

        try {
            DB::transaction(function () use ($validatedData, $request, $user) {
                $userData = [
                    'name' => $validatedData['name'],
                    'username' => $validatedData['username'],
                    'identifier' => $validatedData['identifier'],
                    'email' => $validatedData['email'],
                    'role' => $validatedData['role'],
                    'is_active' => $request->has('is_active') ? $validatedData['is_active'] : $user->is_active,
                ];

                if (!empty($validatedData['password'])) {
                    $userData['password'] = Hash::make($validatedData['password']);
                }

                if ($validatedData['role'] === 'other') {
                    $userData['custom_role'] = $validatedData['custom_role_name'];
                }

                $user->update($userData);

                $profileData = [];
                $profileRelation = null;
                $photoField = null;

                switch ($user->role) {
                    case 'admin':
                        $profileRelation = 'adminProfile';
                        $photoField = 'admin_foto';
                        $profileData = [
                            'jabatan' => $validatedData['admin_jabatan'] ?? null,
                            'telepon' => $validatedData['admin_telepon'] ?? null,
                            'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null,
                            'tempat_lahir' => $validatedData['admin_tempat_lahir'] ?? null,
                            'jenis_kelamin' => $validatedData['admin_jenis_kelamin'] ?? null,
                        ];
                        break;
                    case 'guru':
                        $profileRelation = 'guruProfile';
                        $photoField = 'guru_foto';
                        $profileData = [
                            'jabatan' => $validatedData['guru_jabatan'] ?? null,
                            'telepon' => $validatedData['guru_telepon'] ?? null,
                            'tanggal_lahir' => $validatedData['guru_tanggal_lahir'] ?? null,
                            'tempat_lahir' => $validatedData['guru_tempat_lahir'] ?? null,
                            'jenis_kelamin' => $validatedData['guru_jenis_kelamin'] ?? null,
                            'alamat' => $validatedData['alamat'] ?? null,
                        ];
                        break;
                    case 'siswa':
                        $profileRelation = 'siswaProfile';
                        $photoField = 'siswa_foto';
                        $profileData = [
                            'kelas_id' => $validatedData['kelas_id'] ?? null,
                            'nis' => $validatedData['identifier'],
                            'nama_lengkap' => $validatedData['name'],
                            'tanggal_lahir' => $validatedData['siswa_tanggal_lahir'] ?? null,
                            'tempat_lahir' => $validatedData['siswa_tempat_lahir'] ?? null,
                            'jenis_kelamin' => $validatedData['siswa_jenis_kelamin'] ?? null,
                            'nama_ayah' => $validatedData['nama_ayah'] ?? null,
                            'nama_ibu' => $validatedData['nama_ibu'] ?? null,
                            'telepon_ayah' => $validatedData['telepon_ayah'] ?? null,
                            'telepon_ibu' => $validatedData['telepon_ibu'] ?? null,
                            'alamat' => $validatedData['alamat'] ?? null,
                        ];
                        break;
                    case 'tu':
                        $profileRelation = 'tuProfile';
                        $photoField = 'tu_foto';
                        $profileData = [
                            'jabatan' => $validatedData['tu_jabatan'] ?? null,
                            'telepon' => $validatedData['tu_telepon'] ?? null,
                            'tanggal_lahir' => $validatedData['tu_tanggal_lahir'] ?? null,
                            'tempat_lahir' => $validatedData['tu_tempat_lahir'] ?? null,
                            'jenis_kelamin' => $validatedData['tu_jenis_kelamin'] ?? null,
                        ];
                        break;
                    case 'other':
                        $profileRelation = 'otherProfile';
                        $photoField = 'other_foto';
                        $profileData = [
                            'jabatan' => $validatedData['other_jabatan'] ?? null,
                            'telepon' => $validatedData['other_telepon'] ?? null,
                            'tanggal_lahir' => $validatedData['other_tanggal_lahir'] ?? null,
                            'tempat_lahir' => $validatedData['other_tempat_lahir'] ?? null,
                            'jenis_kelamin' => $validatedData['other_jenis_kelamin'] ?? null,
                            'alamat' => $validatedData['alamat'] ?? null,
                        ];
                        break;
                }

                if ($profileRelation) {
                    $profile = $user->$profileRelation ?: $user->$profileRelation()->create([]);
                    
                    if ($request->hasFile($photoField)) {
                        // Delete old photo
                        if ($profile->foto) {
                            Storage::disk('public')->delete($profile->foto);
                        }
                        $profileData['foto'] = $request->file($photoField)->store('fotos', 'public');
                    }

                    $profile->update($profileData);
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui user: ' . $e->getMessage());
        }

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $profile = $user->adminProfile ?? $user->guruProfile ?? $user->siswaProfile;
        if ($profile && $profile->foto) {
            Storage::disk('public')->delete($profile->foto);
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
    
    public function importForm() { return view('users.import'); }

    public function importTemplate()
    {
        return Excel::download(new UsersTemplateExport(), 'template_import_users.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,csv']);
        try {
            User::withoutEvents(function () use ($request) {
                Excel::import(new \App\Imports\UsersImport, $request->file('file'));
            });
            return redirect()->route('users.index')->with('success', 'Data user berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
    
    public function export(Request $request)
    {
        $query = User::query();
        return Excel::download(new UsersExport($query), 'users_export_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function toggleStatus(User $user) {}

    public function bulkToggleStatus(Request $request) {}

    public function bulkDestroy(Request $request) {}
    
    public function showQrCodeGenerator(Request $request)
    {
        $roles = ['admin', 'guru', 'siswa', 'tu', 'other'];
        $kelases = \App\Models\Kelas::orderBy('nama_kelas')->get();

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswaProfile', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        $users = $query->orderBy('name')->get();
        $logoPath = public_path('images/icon_mts_al_muttaqin.png');

        $usersWithQr = $users->map(function ($user) use ($logoPath) {
            try {
                // Try to generate with the logo first
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                                ->size(150)
                                ->margin(1)
                                ->merge($logoPath, 0.3, true)
                                ->generate($user->identifier);
            } catch (\Exception $e) {
                // If merging fails, generate a basic QR code as a fallback
                \Log::error('QR Code generation with logo failed for user ' . $user->id . ': ' . $e->getMessage());
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                                ->size(150)
                                ->margin(1)
                                ->generate($user->identifier);
            }
            
            $user->qr_code_svg = (string) $qrCode;
            return $user;
        });

        return view('users.qr-generator', [
            'users' => $usersWithQr,
            'roles' => $roles,
            'kelases' => $kelases
        ]);
    }

    public function bulkDownloadQrCodes(Request $request)
    {
        $query = User::query();
        $filenameParts = ['QR_Codes'];

        if ($request->filled('role')) {
            $query->where('role', $request->role);
            $filenameParts[] = ucfirst($request->role);
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswaProfile', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
            $kelas = \App\Models\Kelas::find($request->kelas_id);
            if ($kelas) {
                $filenameParts[] = \Illuminate\Support\Str::slug($kelas->nama_kelas);
            }
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada user yang ditemukan untuk filter tersebut.');
        }

        $logoPath = public_path('images/icon_mts_al_muttaqin.png');
        $filenameParts[] = now()->format('Ymd_His');
        $zipName = implode('_', $filenameParts) . '.zip';
        $zipPath = storage_path('app/public/' . $zipName);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($users as $user) {
                try {
                    $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                                    ->size(500)
                                    ->margin(2)
                                    ->merge($logoPath, 0.3, true)
                                    ->generate($user->identifier);
                } catch (\Exception $e) {
                    $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                                    ->size(500)
                                    ->margin(2)
                                    ->generate($user->identifier);
                }

                $safeName = \Illuminate\Support\Str::slug($user->name);
                $fileName = "{$user->role}/QR_{$safeName}_{$user->identifier}.svg";
                $zip->addFromString($fileName, $qrCode);
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function downloadQrCode(User $user)
    {
        $logoPath = public_path('images/icon_mts_al_muttaqin.png');
        
        try {
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                            ->size(500)
                            ->margin(2)
                            ->merge($logoPath, 0.3, true)
                            ->generate($user->identifier);
        } catch (\Exception $e) {
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                            ->size(500)
                            ->margin(2)
                            ->generate($user->identifier);
        }

        $safeName = \Illuminate\Support\Str::slug($user->name);
        $fileName = "QR_{$safeName}_{$user->identifier}.svg";

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
