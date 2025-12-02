<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absensi;
use App\Models\Setting;
use App\Models\JadwalAbsensi;
use App\Models\JadwalAbsensiPegawai; // Tambahkan import ini
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    /**
     * Menampilkan halaman pemindai QR Code.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $currentDayName = $today->translatedFormat('l'); // e.g., Senin, Selasa
        $currentTime = $today->format('H:i:s');

        $jadwalSiswa = collect();
        $jadwalPegawai = collect();

        if ($user->isGuru()) {
            $querySiswa = JadwalAbsensi::with(['kelas', 'mataPelajaran', 'guru'])
                                        ->where('hari', $currentDayName)
                                        ->where('guru_id', $user->id); // Hanya jadwal guru yang login
            $jadwalSiswa = $querySiswa->orderBy('jam_mulai')->get()->map(function($jadwal) {
                $jadwal->type = 'siswa';
                $jadwal->formatted_id = 'siswa_' . $jadwal->id;
                return $jadwal;
            });
        } else { // Jika bukan guru, maka admin atau role lain
            // Admin tidak perlu melihat jadwal pegawai di daftar, karena akan auto-scan.
            // Admin hanya akan melihat jadwal siswa milik guru lain (jika ada, dan itu bukan fokus utama mereka).
            // Jadi, untuk admin, kita hanya perlu jadwal siswa yang *bukan* miliknya sendiri (opsional, tergantung kebutuhan).
            // Namun, untuk tujuan ini, saya akan memastikan admin hanya melihat jadwal guru (non-siswa) jika tidak ada auto-scan.
            // Biarkan $jadwalSiswa kosong jika admin auto-scan
            $jadwalSiswa = collect();
        }

        // Untuk admin, jadwal pegawai akan di-handle secara otomatis di `store()`,
        // jadi tidak perlu ditampilkan di `$availableSchedules` untuk tampilan.
        // Jika `$user->isAdmin()` adalah true, `$jadwalPegawai` harus kosong di sini
        $jadwalPegawai = collect();
        if ($user->isGuru()) { // Hanya guru yang melihat jadwal mereka
            $availableSchedules = $jadwalSiswa->sortBy('jam_mulai');
        } elseif ($user->isAdmin()) { // Admin tidak memerlukan daftar jadwal di sini
            $availableSchedules = collect(); // Admin tidak perlu daftar jadwal
        } else {
            $availableSchedules = collect(); // Pengguna lain juga tidak perlu daftar
        }
        
        return view('scan.index', compact('availableSchedules', 'user'));
    }

    /**
     * Memproses dan menyimpan data absensi dari hasil pindaian QR Code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi input yang lebih ketat
        $validated = $request->validate([
            'identifier' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9_]+$/' // Hanya alphanumeric dan underscore
            ],
            'jadwal_id' => 'string', // Akan berisi 'siswa_ID' atau 'pegawai_ID', opsional untuk admin
            'jadwal_type' => 'in:siswa,pegawai', // Opsional untuk admin
        ]);

        $identifier = $validated['identifier'];
        $rawJadwalId = $validated['jadwal_id'] ?? null;
        $jadwalType = $validated['jadwal_type'] ?? null;
        $jadwalOriginalId = null;

        if ($rawJadwalId) {
            $jadwalOriginalId = (int) explode('_', $rawJadwalId)[1];
        }

        Log::info('Menerima permintaan absensi untuk identifier:', [
            'identifier' => $identifier,
            'raw_jadwal_id' => $rawJadwalId,
            'jadwal_type' => $jadwalType,
            'jadwal_original_id' => $jadwalOriginalId
        ]);

        // 2. Optimalkan query pencarian user dan relasi profile
        // Temukan pengguna berdasarkan identifier tanpa membatasi role awal
        $targetUser = User::with(['adminProfile', 'guruProfile', 'siswaProfile.kelas', 'tuProfile', 'otherProfile'])
                            ->where('identifier', $identifier)
                            ->first();

        if (!$targetUser) {
            Log::warning('Scan Gagal: Identifier tidak valid.', compact('identifier'));
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau pengguna tidak ditemukan.'
            ], 404);
        }

        // Otorisasi berdasarkan role pengguna yang sedang login (Auth::user())
        $loggedInUser = Auth::user();
        $user = $targetUser; // Ganti $user dengan $targetUser untuk konsistensi

        // Validasi status pengguna yang akan diabsen
        if (!$user->is_active) {
            Log::warning('Scan Gagal: Pengguna nonaktif mencoba absen.', ['user_id' => $user->id, 'is_active' => $user->is_active]);
            return response()->json([
                'success' => false,
                'message' => "Pengguna {$user->name} tidak dapat absen karena statusnya nonaktif."
            ], 403); // 403 Forbidden
        }

        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $currentDayName = $now->translatedFormat('l');

        $jadwal = null;

        if ($loggedInUser->isAdmin()) {
            // Admin dapat absen guru, tu, lainnya, KECUALI siswa
            if ($user->isSiswa()) {
                Log::warning('Scan Gagal: Admin mencoba absen siswa.', ['admin_id' => $loggedInUser->id, 'siswa_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Admin tidak dapat melakukan absensi untuk siswa.'
                ], 403);
            }

            // Jika admin melakukan auto-scan (jadwal_id tidak diberikan), cari jadwal pegawai secara otomatis
            if (!$rawJadwalId && !$jadwalType) {
                $availableEmployeeSchedules = JadwalAbsensiPegawai::with('user')
                    ->where('user_id', $user->id)
                    ->where('hari', $currentDayName)
                    ->whereTime('jam_mulai', '<=', $now->format('H:i:s'))
                    ->whereTime('jam_selesai', '>=', $now->format('H:i:s'))
                    ->get();

                if ($availableEmployeeSchedules->count() === 1) {
                    $jadwal = $availableEmployeeSchedules->first();
                    $jadwalType = 'pegawai';
                } elseif ($availableEmployeeSchedules->count() > 1) {
                    Log::warning('Scan Gagal: Beberapa jadwal pegawai ditemukan untuk auto-scan.', ['admin_id' => $loggedInUser->id, 'target_user_id' => $user->id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Beberapa jadwal pegawai aktif ditemukan. Silakan pilih jadwal secara manual jika opsi tersebut tersedia, atau hubungi administrator.'
                    ], 400);
                } else {
                    Log::warning('Scan Gagal: Tidak ada jadwal pegawai aktif yang cocok untuk auto-scan.', ['admin_id' => $loggedInUser->id, 'target_user_id' => $user->id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada jadwal absensi pegawai aktif yang cocok saat ini.'
                    ], 400);
                }
            } else {
                // Admin memilih jadwal secara manual, lanjutkan dengan logika yang ada
                if ($jadwalType === 'siswa') {
                    Log::warning('Scan Gagal: Admin mencoba absen siswa dengan memilih jadwal.', ['admin_id' => $loggedInUser->id, 'siswa_id' => $user->id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin tidak dapat melakukan absensi untuk siswa.'
                    ], 403);
                }
                if ($jadwalType === 'pegawai') {
                    $jadwal = JadwalAbsensiPegawai::with(['user'])->find($jadwalOriginalId);
                }
            }
        } elseif ($loggedInUser->isGuru()) {
            // Guru hanya dapat absen siswa
            if (!$user->isSiswa()) {
                Log::warning('Scan Gagal: Guru mencoba absen selain siswa.', ['guru_id' => $loggedInUser->id, 'target_role' => $user->role]);
                return response()->json([
                    'success' => false,
                    'message' => 'Guru hanya dapat melakukan absensi untuk siswa.'
                ], 403);
            }

            // Guru harus memilih jadwal siswa secara manual
            if (!$rawJadwalId || $jadwalType !== 'siswa') {
                Log::warning('Scan Gagal: Guru tidak memilih jadwal siswa yang valid.', ['guru_id' => $loggedInUser->id, 'raw_jadwal_id' => $rawJadwalId, 'jadwal_type' => $jadwalType]);
                return response()->json([
                    'success' => false,
                    'message' => 'Guru harus memilih jadwal absensi siswa yang valid.'
                ], 400);
            }
            $jadwal = JadwalAbsensi::with(['kelas', 'mataPelajaran', 'guru'])->find($jadwalOriginalId);
        } else {
            // Pengguna lain (selain admin/guru) tidak diizinkan menggunakan fitur scan ini
            Log::warning('Scan Gagal: Pengguna tanpa role yang diizinkan mencoba absen.', ['user_id' => $loggedInUser->id, 'role' => $loggedInUser->role]);
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk melakukan absensi ini.'
            ], 403);
        }

        if (!$jadwal) {
            Log::warning('Scan Gagal: Jadwal Absensi tidak ditemukan setelah otorisasi.', ['jadwal_original_id' => $jadwalOriginalId, 'jadwal_type' => $jadwalType]);
            return response()->json([
                'success' => false,
                'message' => 'Jadwal absensi tidak valid atau tidak dapat ditentukan.'
            ], 404);
        }

        // Validasi spesifik berdasarkan tipe jadwal (hanya untuk siswa)
        if ($jadwalType === 'siswa') {
            if ($user->isSiswa()) { // Pastikan user yang diabsen memang siswa
                if (!$user->siswaProfile || $user->siswaProfile->kelas_id !== $jadwal->kelas_id) {
                    Log::warning('Scan Gagal: Siswa tidak terdaftar di kelas jadwal yang dipilih.', ['user_id' => $user->id, 'kelas_siswa' => $user->siswaProfile?->kelas_id, 'kelas_jadwal' => $jadwal->kelas_id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Siswa ' . $user->name . ' tidak terdaftar di kelas ini.'
                    ], 400);
                }
            }
        }

        // 3. Cek absensi yang lebih efisien untuk jadwal spesifik
        $today = Carbon::now(config('app.timezone', 'Asia/Jakarta'))->startOfDay();
        $existingAbsensi = Absensi::where('user_id', $user->id)
                                    ->where('jadwal_absensi_id', $jadwal->id) // Use original ID
                                    ->where('jadwal_absensi_type', $jadwalType) // Store type
                                    ->whereDate('tanggal_absensi', $today)
                                    ->first();

        if ($existingAbsensi) {
            $statusSaatIni = $existingAbsensi->status;
            if (in_array($statusSaatIni, ['sakit', 'izin', 'alpha'])) {
                Log::info('Absensi tidak diizinkan: Sudah tercatat dengan status khusus.', ['user_id' => $user->id, 'name' => $user->name, 'status' => $statusSaatIni]);
                return response()->json([
                    'success' => false,
                    'message' => 'Absensi tidak dapat diubah: ' . $user->name . ' sudah tercatat dengan status ' . ucfirst($statusSaatIni) . ' untuk jadwal ini hari ini.'
                ]);
            } else if (in_array($statusSaatIni, ['hadir', 'terlambat'])) {
                Log::info('Duplikasi Absensi: Sudah absen untuk jadwal ini hari ini.', ['user_id' => $user->id, 'name' => $user->name, 'jadwal_id' => $jadwal->id, 'jadwal_type' => $jadwalType]);
                return response()->json([
                    'success' => false,
                    'message' => 'Sudah Absen: ' . $user->name . ' telah tercatat ' . ucfirst($statusSaatIni) . ' untuk jadwal ini hari ini.'
                ]);
            }
        }

        // 4. Tentukan status absensi berdasarkan jam_mulai jadwal
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $jadwalStartTime = Carbon::parse($jadwal->jam_mulai);
        $jadwalEndTime = Carbon::parse($jadwal->jam_selesai);

        // Check if current time is before the schedule start time
        if ($now->lt($jadwalStartTime)) {
            Log::warning('Scan Gagal: Absensi di luar waktu jadwal.', ['user_id' => $user->id, 'now' => $now->format('H:i:s'), 'start' => $jadwalStartTime->format('H:i:s'), 'end' => $jadwalEndTime->format('H:i:s')]);
            return response()->json([
                'success' => false,
                'message' => 'Absensi hanya bisa dilakukan setelah jadwal pelajaran dimulai.'
            ], 400);
        }
        // If it's after the end time (even with grace period), it will simply be 'terlambat'

        // Tentukan status absensi
        $status = 'hadir'; // Default status
        if ($now->gt($jadwalEndTime)) {
            $status = 'terlambat'; // Jika scan setelah waktu selesai jadwal
        }
        // Tidak perlu grace period jika ingin hadir selama masih dalam jam pelajaran

        // 5. Simpan data absensi dengan transaksi database untuk integritas data
            try {
            $absensi = Absensi::create([
                'user_id' => $user->id,
                'tanggal_absensi' => $today,
                'waktu_masuk' => $now,
                'status' => $status,
                'keterangan' => 'Absensi via QR Scan',
                'jadwal_absensi_id' => $jadwal->id, // Save original ID
                'jadwal_absensi_type' => $jadwalType, // Save type of schedule
                'attendance_type' => 'qr_code',
            ]);
            Log::info('Absensi berhasil disimpan.', ['absensi_id' => $absensi->id, 'user_id' => $user->id, 'status' => $status, 'jadwal_id' => $jadwal->id, 'jadwal_type' => $jadwalType]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan absensi ke database.', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal. Gagal menyimpan data absensi.'
            ], 500);
        }

        // 6. Kirim response sukses dengan data yang relevan
            // 6. Kirim response sukses dengan data yang relevan
            $profileData = [];
            $jadwalInfo = [];

            if ($jadwalType === 'siswa' && $user->isSiswa() && $user->siswaProfile) {
                $profileData['kelas'] = $user->siswaProfile->kelas?->nama_kelas ?? 'Belum diatur';
                $jadwalInfo['mata_pelajaran'] = $jadwal->mataPelajaran->nama_mapel ?? 'N/A';
                $jadwalInfo['guru'] = $jadwal->guru->name ?? 'N/A';
            } elseif ($jadwalType === 'pegawai' && $user->userProfile()) { // userProfile() method to get specific profile
                // Menggunakan user yang diabsen ($user) untuk mendapatkan profilnya
                if ($user->isGuru() && $user->guruProfile) {
                    $profileData['nip'] = $user->guruProfile->nip ?? 'N/A';
                } elseif ($user->hasRole('tu') && $user->tuProfile) {
                    $profileData['jabatan'] = $user->tuProfile->jabatan ?? 'N/A';
                } elseif ($user->hasRole('other') && $user->otherProfile) {
                    $profileData['jabatan_custom'] = $user->otherProfile->custom_role_name ?? $user->custom_role ?? 'N/A';
                }
                $jadwalInfo['pegawai'] = $jadwal->user->name ?? 'N/A';
                $jadwalInfo['keterangan_jadwal'] = $jadwal->keterangan ?? 'N/A';
            }


            return response()->json([
                'success' => true,
                'message' => "Absensi berhasil: {$user->name} tercatat {$status}.",
                'data' => array_merge([
                    'name' => $user->name,
                    'identifier' => $user->identifier,
                    'role' => $user->role,
                    'waktu' => $now->format('H:i:s'),
                    'status' => $status,
                    'jadwal_type' => $jadwalType,
                ], $profileData, $jadwalInfo)
            ]);
    }

    /**
     * Mengambil daftar siswa yang sudah discan untuk jadwal absensi tertentu pada hari ini.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getScannedStudents(Request $request)
    {
        $request->validate([
            // Ubah validasi untuk menerima formatted_id dan jadwal_type
            'jadwal_id' => 'required|string',
            'jadwal_type' => 'required|in:siswa,pegawai',
        ]);

        $rawJadwalId = $request->input('jadwal_id');
        $jadwalType = $request->input('jadwal_type');
        $jadwalOriginalId = (int) explode('_', $rawJadwalId)[1];

        $today = Carbon::now(config('app.timezone', 'Asia/Jakarta'))->startOfDay();

        $scannedUsers = Absensi::with(['user.siswaProfile.kelas', 'user.adminProfile', 'user.guruProfile', 'user.tuProfile', 'user.otherProfile'])
            ->where('jadwal_absensi_id', $jadwalOriginalId)
            ->where('jadwal_absensi_type', $jadwalType)
            ->whereDate('tanggal_absensi', $today)
            ->get()
            ->map(function ($absensi) use ($jadwalType) {
                $profileData = [];
                if ($absensi->user->isSiswa() && $absensi->user->siswaProfile) {
                    $profileData['kelas'] = $absensi->user->siswaProfile->kelas?->nama_kelas ?? 'Belum diatur';
                } elseif ($absensi->user->isGuru() && $absensi->user->guruProfile) {
                    $profileData['nip'] = $absensi->user->guruProfile->nip ?? 'N/A';
                } elseif ($absensi->user->hasRole('tu') && $absensi->user->tuProfile) {
                    $profileData['jabatan'] = $absensi->user->tuProfile->jabatan ?? 'N/A';
                } elseif ($absensi->user->hasRole('other') && $absensi->user->otherProfile) {
                    $profileData['jabatan_custom'] = $absensi->user->otherProfile->custom_role_name ?? $absensi->user->custom_role ?? 'N/A';
                }

                return array_merge([
                    'name' => $absensi->user->name,
                    'role' => $absensi->user->role,
                    'waktu' => Carbon::parse($absensi->waktu_masuk)->format('H:i:s'),
                    'status' => $absensi->status,
                ], $profileData);
            });

        return response()->json([
            'success' => true,
            'data' => $scannedUsers,
        ]);
    }
}
