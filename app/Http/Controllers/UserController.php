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
use App\Notifications\AccountStatusChangedNotification; // Import notifikasi
use App\Models\PrintCardConfig; // Add this import
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query dan langsung muat relasi yang dibutuhkan
        $query = User::where('id', '!=', auth()->id())
                     ->with(['adminProfile', 'guruProfile', 'siswaProfile.kelas']);

        // Filter berdasarkan pencarian nama, email, atau ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // --- LOGIKA BARU: Filter berdasarkan kelas_id ---
        $kelasFilter = null;
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswaProfile', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
            // Ambil data kelas untuk ditampilkan di view
            $kelasFilter = Kelas::find($request->kelas_id);
        }
        
        $users = $query->latest('created_at')->paginate(50);
        $allKelas = Kelas::orderBy('nama_kelas')->get();
        
        // Kirim data ke view
        return view('users.index', compact('users', 'kelasFilter', 'allKelas'));
    }

    /**
     * Display the specified resource.
     */
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
                $user->load('adminProfile');
                break;
            case 'other':
                // Peran kustom mungkin tidak memiliki profil khusus secara default
                break;
        }
        return view('users.show', compact('user'));
    }

    public function create()
    {
        // PERBAIKAN: Mengambil data kelas untuk dropdown di form
        $kelas = Kelas::orderBy('nama_kelas')->get();

        // TAMBAHKAN: Mengambil daftar semua mata pelajaran untuk form
        $mataPelajaranList = \App\Models\MataPelajaran::orderBy('nama_mapel')->get();

        $user = new User(); // Inisialisasi $user sebagai instance baru User untuk mencegah error null

        return view('users.create', compact('kelas', 'mataPelajaranList', 'user'));
    }

    public function store(Request $request)
    {
        \Log::info('UserController@store: Memulai proses penambahan user.');

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('users', 'username')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'identifier' => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('users', 'identifier')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'email' => [
                'required', 'string', 'email', 'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa', 'tu', 'other'])],
            'custom_role_name' => ['nullable', 'string', 'max:255', 'required_if:role,other'], // Validasi untuk peran kustom
            'password' => ['required', 'confirmed', ValidationRules\Password::min(8)],

            // Validasi untuk field profil spesifik (dibuat nullable semua agar lebih fleksibel)
            'admin_jabatan' => ['nullable', 'string', 'max:255'],
            'admin_telepon' => ['nullable', 'string', 'max:255'],
            'admin_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'], // Untuk peran 'other'
            'jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])], // Untuk peran 'other'
            'tanggal_lahir' => ['nullable', 'date'], // Untuk admin (TU dan Other juga)
            'guru_jabatan' => ['nullable', 'string', 'max:255'],
            'guru_telepon' => ['nullable', 'string', 'max:255'],
            'guru_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'siswa_tanggal_lahir' => ['nullable', 'date'], // Untuk siswa
            'guru_tanggal_lahir' => ['nullable', 'date'], // Untuk guru
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'telepon_ayah' => ['nullable', 'string', 'max:255'],
            'telepon_ibu' => ['nullable', 'string', 'max:255'],
            'siswa_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'alamat' => ['nullable', 'string'],
            'jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'], // Tambahkan validasi untuk is_active
        ]);

        \Log::info('UserController@store: Data tervalidasi.', ['validatedData' => $validatedData]);

        try {
            DB::transaction(function () use ($validatedData, $request) {
                \Log::info('UserController@store: Memulai transaksi database.');

                $user = User::create([
                    'name' => $validatedData['name'],
                    'username' => $validatedData['username'],
                    'identifier' => $validatedData['identifier'],
                    'email' => $validatedData['email'],
                    'role' => $validatedData['role'],
                    'custom_role' => $validatedData['role'] === 'other' ? $validatedData['custom_role_name'] : null, // Simpan peran kustom jika role 'other'
                    'password' => Hash::make($validatedData['password']),
                    'is_active' => $validatedData['is_active'] ?? true, // Set default true jika tidak ada
                ]);

                \Log::info('UserController@store: User utama berhasil dibuat dalam transaksi.', ['user_id' => $user->id, 'role' => $user->role]);

                $profileData = [];
                $fotoPath = null;

                switch ($user->role) {
                    case 'admin':
                    case 'tu': // TU juga menggunakan profil admin
                        $profileData = [
                            'jabatan' => $validatedData['admin_jabatan'] ?? null,
                            'telepon' => $validatedData['admin_telepon'] ?? null,
                            'tanggal_bergabung' => $user->created_at, // Otomatis diisi
                            'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null, // Tambahkan tanggal_lahir
                            'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
                            'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
                        ];
                        if ($request->hasFile('admin_foto')) {
                            $fotoPath = $request->file('admin_foto')->store('fotos', 'public');
                        }
                        break;
                    case 'guru':
                        $profileData = [
                            'jabatan' => $validatedData['guru_jabatan'] ?? null,
                            'telepon' => $validatedData['guru_telepon'] ?? null,
                            'tanggal_lahir' => $validatedData['guru_tanggal_lahir'] ?? null, // Mengambil dari guru_tanggal_lahir
                            'alamat' => $validatedData['alamat'] ?? null,
                            'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
                            'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
                        ];
                        if ($request->hasFile('guru_foto')) {
                            $fotoPath = $request->file('guru_foto')->store('fotos', 'public');
                            \Log::info('UserController@store: Foto guru diupload dalam transaksi.', ['path' => $fotoPath]);
                        }
                        break;
                    case 'siswa':
                        $profileData = [
                            'kelas_id' => $validatedData['kelas_id'] ?? null,
                            'nis' => $validatedData['identifier'],
                            'nama_lengkap' => $validatedData['name'], // Menggunakan nama dari user
                            'tanggal_lahir' => $validatedData['siswa_tanggal_lahir'] ?? null,
                            'alamat' => $validatedData['alamat'] ?? null,
                            'nama_ayah' => $validatedData['nama_ayah'] ?? null,
                            'nama_ibu' => $validatedData['nama_ibu'] ?? null,
                            'telepon_ayah' => $validatedData['telepon_ayah'] ?? null,
                            'telepon_ibu' => $validatedData['telepon_ibu'] ?? null,
                            'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
                            'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
                        ];
                        if ($request->hasFile('siswa_foto')) {
                            $fotoPath = $request->file('siswa_foto')->store('fotos', 'public');
                            \Log::info('UserController@store: Foto siswa diupload dalam transaksi.', ['path' => $fotoPath]);
                        }
                        break;
                    case 'other': // Peran kustom menggunakan profil admin untuk input data generik
                        $profileData = [
                            'jabatan' => $validatedData['admin_jabatan'] ?? null,
                            'telepon' => $validatedData['admin_telepon'] ?? null,
                            'tanggal_bergabung' => $user->created_at, // Otomatis diisi
                            'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null, // Tambahkan tanggal_lahir
                            'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
                            'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
                        ];
                        if ($request->hasFile('admin_foto')) {
                            $fotoPath = $request->file('admin_foto')->store('fotos', 'public');
                        }
                        break;
                }

                if ($fotoPath) {
                    $profileData['foto'] = $fotoPath;
                }
                \Log::info('UserController@store: Data profil disiapkan dalam transaksi.', ['profileData' => $profileData]);

                if (!empty($profileData)) {
                    switch ($user->role) {
                        case 'admin':
                        case 'tu': // TU juga menggunakan profil admin
                            $user->adminProfile()->create($profileData);
                            \Log::info('UserController@store: Profil admin/TU dibuat dalam transaksi.');
                            break;
                        case 'guru':
                            $user->guruProfile()->create($profileData);
                            $user->mataPelajarans()->sync($request->input('mata_pelajaran_ids', []));
                            \Log::info('UserController@store: Profil guru dibuat dan mata pelajaran disinkronkan dalam transaksi.');
                            break;
                        case 'siswa':
                            $user->siswaProfile()->create($profileData);
                            \Log::info('UserController@store: Profil siswa dibuat dalam transaksi.');
                            break;
                        case 'other':
                            // Untuk peran 'other', menggunakan profil admin untuk penyimpanan
                            $user->adminProfile()->create($profileData);
                            \Log::info('UserController@store: Profil kustom (other) dibuat menggunakan profil admin dalam transaksi.');
                            break;
                    }
                }
                \Log::info('UserController@store: Transaksi database selesai.');
            });
        } catch (\Exception $e) {
            \Log::error('UserController@store: Error saat membuat user atau profil: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan user: ' . $e->getMessage());
        }

        \Log::info('UserController@store: User berhasil ditambahkan, mengarahkan ke halaman index.');
        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        // Muat profil spesifik berdasarkan peran user
        if ($user->role === 'admin' || $user->role === 'tu') {
            $user->load('adminProfile');
        } elseif ($user->role === 'guru') {
            $user->load(['guruProfile', 'mataPelajarans']);
            \Log::info('UserController@edit: User Guru dimuat.', [
                'user_id' => $user->id,
                'guruProfile_exists' => $user->guruProfile ? 'Yes' : 'No',
                'guruProfile_data' => $user->guruProfile ? $user->guruProfile->toArray() : null,
                'mataPelajarans_exists' => $user->mataPelajarans ? 'Yes' : 'No',
                'mataPelajarans_ids' => $user->mataPelajarans ? $user->mataPelajarans->pluck('id')->toArray() : null
            ]);
        } elseif ($user->role === 'siswa') {
            $user->load(['siswaProfile', 'siswaProfile.kelas']);
        } elseif ($user->role === 'other') {
            $user->load('adminProfile'); // Muat profil admin jika peran adalah 'other'
        }

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mataPelajaranList = MataPelajaran::orderBy('nama_mapel')->get();

        return view('users.edit', compact('user', 'kelas', 'mataPelajaranList'));
    }

    public function update(Request $request, User $user)
    {
        \Log::info('UserController@update: Request has admin_foto: ' . $request->hasFile('admin_foto'));
        
        // 1. Validate all incoming data
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:255',
                Rule::unique('users', 'username')->ignore($user->id)->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'identifier' => [
                'required', 'string', 'max:255',
                Rule::unique('users', 'identifier')->ignore($user->id)->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id)->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa', 'tu', 'other'])],
            'custom_role_name' => ['nullable', 'string', 'max:255', 'required_if:role,other'], // Validasi untuk peran kustom
            'password' => ['nullable', 'confirmed', ValidationRules\Password::min(8)],
            'jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])], // Ditambahkan di sini
        ];

        // Add role-specific validation rules
        if ($request->role === 'admin' || $request->role === 'tu') { // Admin dan TU berbagi aturan validasi profil yang sama
            $rules['admin_jabatan'] = ['nullable', 'string', 'max:255'];
            $rules['admin_telepon'] = ['nullable', 'string', 'max:255'];
            $rules['admin_foto'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'];
            $rules['tempat_lahir'] = ['nullable', 'string', 'max:255']; // Explicitly added
            $rules['jenis_kelamin'] = ['nullable', Rule::in(['laki-laki', 'perempuan'])]; // Explicitly added
            $rules['tanggal_lahir'] = ['nullable', 'date']; // Untuk Admin, TU, Other
        } elseif ($request->role === 'guru') {
            $rules['guru_jabatan'] = ['nullable', 'string', 'max:255'];
            $rules['guru_telepon'] = ['nullable', 'string', 'max:255'];
            $rules['guru_foto'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'];
            $rules['guru_tanggal_lahir'] = ['nullable', 'date'];
            $rules['guru_alamat'] = ['nullable', 'string'];
            $rules['guru_jenis_kelamin'] = ['nullable', Rule::in(['laki-laki', 'perempuan'])];
            $rules['guru_tempat_lahir'] = ['nullable', 'string', 'max:255'];
        } elseif ($request->role === 'siswa') {
            $rules['kelas_id'] = ['nullable', 'exists:kelas,id'];
            $rules['tanggal_lahir'] = ['nullable', 'date'];
            $rules['alamat'] = ['nullable', 'string'];
            $rules['nama_ayah'] = ['nullable', 'string', 'max:255'];
            $rules['nama_ibu'] = ['nullable', 'string', 'max:255'];
            $rules['telepon_ayah'] = ['nullable', 'string', 'max:255'];
            $rules['telepon_ibu'] = ['nullable', 'string', 'max:255'];
            $rules['siswa_foto'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'];
            $rules['nama_lengkap'] = ['nullable', 'string', 'max:255'];
            $rules['tempat_lahir'] = ['nullable', 'string', 'max:255']; // TAMBAHKAN INI
            $rules['is_active'] = ['boolean']; // Tambahkan validasi untuk is_active
        }
        // Tidak ada aturan validasi khusus untuk peran 'other' secara default, tetapi custom_role_name divalidasi di atas

        $validatedData = $request->validate($rules);
        \Log::info('UserController@update: Data tervalidasi.', ['validatedData' => $validatedData]);

        // 2. Update User model's core data
        $user->update($request->only('name', 'username', 'identifier', 'email', 'role', 'is_active'));

        // Update custom_role if the role is 'other'
        if ($request->role === 'other') {
            $user->custom_role = $validatedData['custom_role_name'];
        } else {
            $user->custom_role = null; // Clear custom_role if role is not 'other'
        }
        $user->save();

        if ($request->filled('password')) {
            \Log::info('UserController@update: Bidang password diisi, mencoba memperbarui password secara langsung di DB.');
            DB::table('users')->where('id', $user->id)->update(['password' => Hash::make($request->password)]);
            \Log::info('UserController@update: Password seharusnya sudah diperbarui melalui DB::table.');
        }

        // 3. Prepare profile data based on role
        $profileData = [];
        $currentProfile = null; // To store the current profile model (adminProfile, guruProfile, siswaProfile)
        $fotoPath = null;

        switch ($user->role) {
            case 'admin':
            case 'tu': // TU juga menggunakan profil admin
                $profileData = [
                    'jabatan' => $validatedData['admin_jabatan'] ?? null,
                    'telepon' => $validatedData['admin_telepon'] ?? null,
                    'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
                    'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null, // Tambahkan tanggal_lahir
                    'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
                ];
                $currentProfile = $user->adminProfile;
                if ($request->hasFile('admin_foto')) {
                    $fotoPath = $request->file('admin_foto')->store('fotos', 'public');
                }
                break;
            case 'guru':
                $profileData = [
                    'jabatan' => $validatedData['guru_jabatan'] ?? null,
                    'telepon' => $validatedData['guru_telepon'] ?? null,
                    'tanggal_lahir' => $validatedData['guru_tanggal_lahir'] ?? null,
                    'alamat' => $validatedData['guru_alamat'] ?? null,
                    'jenis_kelamin' => $validatedData['guru_jenis_kelamin'] ?? null,
                    'tempat_lahir' => $validatedData['guru_tempat_lahir'] ?? null,
                ];
                $currentProfile = $user->guruProfile;
                if ($request->hasFile('guru_foto')) {
                    $fotoPath = $request->file('guru_foto')->store('fotos', 'public');
                }
                break;
            case 'siswa':
                $profileData = [
                    'kelas_id' => $validatedData['kelas_id'] ?? null,
                    'nis' => $validatedData['identifier'], // NIS diambil dari identifier
                    'nama_lengkap' => $validatedData['name'], // Menggunakan nama dari user
                    'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null,
                    'alamat' => $validatedData['alamat'] ?? null,
                    'nama_ayah' => $validatedData['nama_ayah'] ?? null,
                    'nama_ibu' => $validatedData['nama_ibu'] ?? null,
                    'telepon_ayah' => $validatedData['telepon_ayah'] ?? null,
                    'telepon_ibu' => $validatedData['telepon_ibu'] ?? null,
                    'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
                    'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
                ];
                $currentProfile = $user->siswaProfile;
                if ($request->hasFile('siswa_foto')) {
                    $fotoPath = $request->file('siswa_foto')->store('fotos', 'public');
                }
                break;
            case 'other': // Peran kustom menggunakan profil admin
                $profileData = [
                    'jabatan' => $validatedData['admin_jabatan'] ?? null,
                    'telepon' => $validatedData['admin_telepon'] ?? null,
                    'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
                    'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null, // Tambahkan tanggal_lahir
                    'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
                ];
                $currentProfile = $user->adminProfile;
                if ($request->hasFile('admin_foto')) {
                    $fotoPath = $request->file('admin_foto')->store('fotos', 'public');
                }
                break;
        }

        // 4. Handle photo upload (delete old and set new path)
        if ($fotoPath) {
            if ($currentProfile && $currentProfile->foto) {
                Storage::disk('public')->delete($currentProfile->foto);
            }
            $profileData['foto'] = $fotoPath;
            \Log::info('Foto diupload: ' . $fotoPath);
        } else {
            // If no new photo uploaded, retain the existing photo path if any
            if ($currentProfile && $currentProfile->foto) {
                $profileData['foto'] = $currentProfile->foto;
                \Log::info('Foto lama dipertahankan: ' . $currentProfile->foto);
            } else {
                // If no new photo and no old photo, ensure 'foto' is null in profileData
                $profileData['foto'] = null;
            }
        }

        // 5. Update or Create the specific profile
        if (!empty($profileData)) {
            switch ($user->role) {
                case 'admin':
                case 'tu': // TU juga menggunakan profil admin
                    $user->adminProfile()->updateOrCreate([], $profileData);
                    break;
                case 'guru':
                    \Log::info('UserController@update: Memperbarui profil guru.', [
                        'user_id' => $user->id,
                        'profileData' => $profileData,
                        'currentProfile_exists' => $currentProfile ? 'Yes' : 'No',
                        'currentProfile_data' => $currentProfile ? $currentProfile->toArray() : null,
                    ]);
                    $user->guruProfile()->updateOrCreate([], $profileData);
                    break;
                case 'siswa':
                    $user->siswaProfile()->updateOrCreate([], $profileData);
                    break;
                case 'other':
                    // Untuk peran 'other', menggunakan profil admin untuk penyimpanan
                    $user->adminProfile()->updateOrCreate([], $profileData);
                    break;
            }
        }

        // LOGIKA BARU: Simpan relasi mata pelajaran untuk guru
        if ($user->role === 'guru') {
            $user->mataPelajarans()->sync($request->input('mata_pelajaran_ids', []));
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
    
    // ... (method import dan export tetap sama) ...
    public function importForm() { return view('users.import'); }

    public function importTemplate()
    {
        return Excel::download(new UsersTemplateExport(), 'template_import_users.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            // Temporarily disable User model events during import
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
        // Mulai query dan langsung muat relasi yang dibutuhkan
        $query = User::where('id', '!=', auth()->id())
                     ->with(['adminProfile', 'guruProfile', 'siswaProfile.kelas']);

        // Filter berdasarkan pencarian nama, email, atau ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Ekspor data menggunakan UsersExport dan kirim file excel
        return Excel::download(new UsersExport($query), 'users_export_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function cetakSatuKartu(Request $request, User $user)
    {
        if ($user->role !== 'siswa') {
            abort(404, 'User bukan siswa.');
        }
        $user->load('siswaProfile.kelas'); // Load kelas relationship for siswaProfile

        $configId = $request->input('config_id');
        $config = null;
        $targetRole = $user->role; // Get the role of the specific user being printed

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

        // Since we are printing a single card, we pass the user in an array
        $siswa = collect([$user]); // Wrap the single user in a collection to match the view's expectation

        // Pass $config to the view
        return view('kelas.print_cards', compact('siswa', 'config'));
    }

    public function toggleStatus(User $user)
    {
        // Pembatasan: Administrator tidak dapat menonaktifkan akun mereka sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        // ATURAN BARU: Hanya guru, siswa, dan TU yang bisa diubah statusnya
        if (!in_array($user->role, ['guru', 'siswa', 'tu'])) {
            return back()->with('error', 'Status akun untuk peran ' . ($user->role === 'other' ? $user->custom_role : $user->role) . ' tidak dapat diubah.');
        }

        $oldStatus = $user->is_active;
        $user->is_active = !$user->is_active;
        $user->save();

        // Catat aktivitas
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(), // Admin yang melakukan aksi
            'target_user_id' => $user->id, // User yang diubah statusnya
            'action' => $user->is_active ? 'activated_account' : 'deactivated_account',
            'old_status' => $oldStatus,
            'new_status' => $user->is_active,
            'description' => 'Akun ' . $user->name . ' di' . ($user->is_active ? 'aktifkan' : 'nonaktifkan') . ' oleh ' . auth()->user()->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        // Kirim notifikasi ke user yang bersangkutan
        $user->notify(new AccountStatusChangedNotification($user->is_active));

        return back()->with('success', 'Status user berhasil diperbarui.');
    }

    public function bulkToggleStatus(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'action' => ['required', 'string', Rule::in(['activate', 'deactivate'])],
        ]);

        $userIds = $validated['user_ids'];
        $action = $validated['action'];
        $newStatus = ($action === 'activate');

        // Filter user ID untuk memastikan admin yang sedang login tidak termasuk
        $authId = auth()->id();
        $filteredUserIds = collect($userIds)->reject(function ($id) use ($authId) {
            return $id == $authId;
        })->all();

        // Ambil user yang akan diupdate (hanya guru, siswa, dan TU)
        $usersToUpdate = User::whereIn('id', $filteredUserIds)
                               ->whereIn('role', ['guru', 'siswa', 'tu'])
                               ->get();

        if ($usersToUpdate->isEmpty()) {
            return back()->with('error', 'Tidak ada pengguna (guru/siswa) yang valid untuk diproses.');
        }

        $updatedCount = 0;
        $logEntries = [];
        $now = now();

        foreach ($usersToUpdate as $user) {
            if ($user->is_active != $newStatus) {
                $oldStatus = $user->is_active;
                
                // Update status
                $user->is_active = $newStatus;
                $user->save();

                $logEntries[] = [
                    'user_id' => $authId,
                    'target_user_id' => $user->id,
                    'action' => $newStatus ? 'activated_account' : 'deactivated_account',
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'description' => 'Aksi massal: Akun ' . $user->name . ' di' . ($newStatus ? 'aktifkan' : 'nonaktifkan') . ' oleh ' . auth()->user()->name,
                    'ip_address' => $request->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $updatedCount++;
            }
        }

        // Insert semua log dalam satu query
        if (!empty($logEntries)) {
            \App\Models\ActivityLog::insert($logEntries);
        }

        if ($updatedCount > 0) {
            return back()->with('success', "Berhasil memperbarui status {$updatedCount} pengguna.");
        } else {
            return back()->with('info', 'Tidak ada status pengguna yang diubah.');
        }
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $userIds = $validated['user_ids'];

        // Prevent deleting the currently authenticated user
        $authId = auth()->id();
        $filteredUserIds = collect($userIds)->reject(function ($id) use ($authId) {
            return $id == $authId;
        })->all();

        if (empty($filteredUserIds)) {
            return back()->with('error', 'Tidak ada pengguna yang valid untuk dihapus.');
        }

        $deletedCount = 0;
        $logEntries = [];
        $now = now();

        DB::transaction(function () use ($filteredUserIds, &$deletedCount, &$logEntries, $request, $now, $authId) {
            $usersToDelete = User::whereIn('id', $filteredUserIds)->get();

            foreach ($usersToDelete as $user) {
                // Delete associated profiles and their photos
                $profile = $user->adminProfile ?? $user->guruProfile ?? $user->siswaProfile;
                if ($profile && $profile->foto) {
                    Storage::disk('public')->delete($profile->foto);
                }

                // Delete the user
                $user->delete();
                $deletedCount++;

                $logEntries[] = [
                    'user_id' => $authId,
                    'target_user_id' => null, // Set to null for deletion logs. Requires migration to make 'target_user_id' nullable in 'activity_logs' table.
                    'action' => 'deleted_account',
                    'description' => 'Aksi massal: Akun ' . $user->name . ' dihapus oleh ' . auth()->user()->name,
                    'ip_address' => $request->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        });

        // Insert all logs in one query
        if (!empty($logEntries)) {
            \App\Models\ActivityLog::insert($logEntries);
        }

        if ($deletedCount > 0) {
            return redirect()->route('users.index')->with('success', "Berhasil menghapus {$deletedCount} pengguna.");
        } else {
            return redirect()->route('users.index')->with('info', 'Tidak ada pengguna yang dihapus.');
        }
    }
}
