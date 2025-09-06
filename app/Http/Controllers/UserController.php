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
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'identifier' => ['required', 'string', 'max:255', 'unique:users,identifier'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa'])],
            'password' => ['required', 'confirmed', ValidationRules\Password::min(8)],

            // Validasi untuk field profil spesifik (dibuat nullable semua agar lebih fleksibel)
            'admin_jabatan' => ['nullable', 'string', 'max:255'],
            'admin_telepon' => ['nullable', 'string', 'max:255'],
            'admin_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'guru_jabatan' => ['nullable', 'string', 'max:255'],
            'guru_telepon' => ['nullable', 'string', 'max:255'],
            'guru_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'tanggal_lahir' => ['nullable', 'date'],
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
                    'password' => Hash::make($validatedData['password']),
                    'is_active' => $validatedData['is_active'] ?? true, // Set default true jika tidak ada
                ]);

                \Log::info('UserController@store: User utama berhasil dibuat dalam transaksi.', ['user_id' => $user->id, 'role' => $user->role]);

                $profileData = [];
                $fotoPath = null;

                switch ($user->role) {
                    case 'admin':
                        $profileData = [
                            'jabatan' => $validatedData['admin_jabatan'] ?? null,
                            'telepon' => $validatedData['admin_telepon'] ?? null,
                            'tanggal_bergabung' => $user->created_at, // Otomatis diisi
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
                            'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null,
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
                            'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null,
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
                }

                if ($fotoPath) {
                    $profileData['foto'] = $fotoPath;
                }
                \Log::info('UserController@store: Data profil disiapkan dalam transaksi.', ['profileData' => $profileData]);

                if (!empty($profileData)) {
                    switch ($user->role) {
                        case 'admin': 
                            $user->adminProfile()->create($profileData);
                            \Log::info('UserController@store: Profil admin dibuat dalam transaksi.');
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
        if ($user->role === 'admin') {
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
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'identifier' => ['required', 'string', 'max:255', Rule::unique('users', 'identifier')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa'])],
            'password' => ['nullable', 'confirmed', ValidationRules\Password::min(8)],
            'jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])], // Ditambahkan di sini
        ];

        // Add role-specific validation rules
        if ($request->role === 'admin') {
            $rules['admin_jabatan'] = ['nullable', 'string', 'max:255'];
            $rules['admin_telepon'] = ['nullable', 'string', 'max:255'];
            $rules['admin_foto'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'];
            $rules['tempat_lahir'] = ['nullable', 'string', 'max:255']; // Explicitly added
            $rules['jenis_kelamin'] = ['nullable', Rule::in(['laki-laki', 'perempuan'])]; // Explicitly added
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

        $validatedData = $request->validate($rules);
        \Log::info('UserController@update: Data tervalidasi.', ['validatedData' => $validatedData]);

        // 2. Update User model's core data
        $user->update($request->only('name', 'username', 'identifier', 'email', 'role', 'is_active'));

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
                $profileData = [
                    'jabatan' => $validatedData['admin_jabatan'] ?? null,
                    'telepon' => $validatedData['admin_telepon'] ?? null,
                    'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
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
                case 'admin': $user->adminProfile()->updateOrCreate([], $profileData); break;
                case 'guru':
                    \Log::info('UserController@update: Memperbarui profil guru.', [
                        'user_id' => $user->id,
                        'profileData' => $profileData,
                        'currentProfile_exists' => $currentProfile ? 'Yes' : 'No',
                        'currentProfile_data' => $currentProfile ? $currentProfile->toArray() : null,
                    ]);
                    $user->guruProfile()->updateOrCreate([], $profileData);
                    break;
                case 'siswa': $user->siswaProfile()->updateOrCreate([], $profileData); break;
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

        if ($configId) {
            $config = PrintCardConfig::find($configId);
        }

        // Jika tidak ada konfigurasi yang ditemukan atau tidak ada config_id, gunakan default
        if (!$config) {
            // Coba ambil konfigurasi default dari database
            $config = PrintCardConfig::where('is_default', true)->first();

            // Jika tidak ada default di DB, buat konfigurasi default hardcoded
            if (!$config) {
                $config = new PrintCardConfig();
                $config->config_json = [
                    'selected_fields' => ['name', 'nis', 'kelas', 'foto', 'tanggal_lahir'],
                    'qr_size' => 70,
                    'watermark_enabled' => true,
                    'watermark_opacity' => 0.1,
                    'card_orientation' => 'portrait',
                ];
            }
        }

        // Pastikan config_json adalah array asosiatif
        if (is_string($config->config_json)) {
            $config->config_json = json_decode($config->config_json, true);
        }
        if (!is_array($config->config_json)) {
            $config->config_json = []; // Fallback ke array kosong jika decoding gagal
        }

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

        // ATURAN BARU: Hanya guru dan siswa yang bisa diubah statusnya
        if (!in_array($user->role, ['guru', 'siswa'])) {
            return back()->with('error', 'Status akun untuk peran ' . $user->role . ' tidak dapat diubah.');
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

        // Ambil user yang akan diupdate (hanya guru dan siswa)
        $usersToUpdate = User::whereIn('id', $filteredUserIds)
                               ->whereIn('role', ['guru', 'siswa'])
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
                    'user_agent' => $request->header('User-Agent'),
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
                    'user_agent' => $request->header('User-Agent'),
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
