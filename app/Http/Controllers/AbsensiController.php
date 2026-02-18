<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Absensi;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use App\Models\SiswaProfile;
use App\Models\Kelas;

class AbsensiController extends Controller
{
    // Arahkan user ke dashboard yang sesuai (guru/siswa)
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Logika untuk Dashboard Admin
            $totalAdmin = \App\Models\User::where('role', 'admin')->count();
            $totalGuru = \App\Models\User::where('role', 'guru')->count();
            $totalSiswa = \App\Models\User::where('role', 'siswa')->count();
            $totalKelas = \App\Models\Kelas::count();
            $totalMataPelajaran = \App\Models\MataPelajaran::count();
            $totalJadwalAbsensi = \App\Models\JadwalAbsensi::count();
            $absenHariIni = \App\Models\Absensi::whereDate('tanggal_absensi', \Carbon\Carbon::today())->count();
            $terlambatHariIni = \App\Models\Absensi::whereDate('tanggal_absensi', \Carbon\Carbon::today())->where('status', 'terlambat')->count();
            $izinSakitHariIni = \App\Models\Absensi::whereDate('tanggal_absensi', \Carbon\Carbon::today())->whereIn('status', ['izin', 'sakit'])->count();

            $persentaseKehadiranHariIni = ($totalSiswa > 0) ? round(($absenHariIni / $totalSiswa) * 100) : 0;
            $totalUsers = $totalAdmin + $totalGuru + $totalSiswa;

            return view('admin.dashboard', compact(
                'totalAdmin', 'totalGuru', 'totalSiswa', 'totalKelas', 'totalMataPelajaran', 'absenHariIni', 'persentaseKehadiranHariIni', 'terlambatHariIni', 'izinSakitHariIni', 'totalUsers', 'totalJadwalAbsensi'
            ));

        } elseif ($user->role === 'guru') {
            // Logika untuk Dasbor Guru
            $kelasDiampu = \App\Models\Kelas::where('wali_kelas_id', $user->id)->first();
            $stats = [
                'total_siswa' => 0,
                'total_hadir' => 0,
                'total_absen' => 0,
            ];
            $siswaAbsen = collect();
            $jadwalMengajar = collect();

            // Statistik umum untuk semua guru
            $totalMataPelajaranDiampu = \App\Models\JadwalAbsensi::where('guru_id', $user->id)->distinct('mata_pelajaran_id')->count();
            $totalKelasDiajar = \App\Models\JadwalAbsensi::where('guru_id', $user->id)->distinct('kelas_id')->count();

            // Jumlah absensi tercatat hari ini untuk kelas yang diajar guru
            $kelasDiajarIds = \App\Models\JadwalAbsensi::where('guru_id', $user->id)->pluck('kelas_id')->unique();
            $siswaDiKelasDiajarIds = \App\Models\SiswaProfile::whereIn('kelas_id', $kelasDiajarIds)->pluck('user_id');
            $absensiTercatatHariIni = \App\Models\Absensi::whereIn('user_id', $siswaDiKelasDiajarIds)
                                        ->whereDate('tanggal_absensi', \Carbon\Carbon::today())
                                        ->count();

            // Ambil jadwal mengajar guru untuk hari ini
            $dayMap = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $hariIni = $dayMap[Carbon::now()->dayOfWeek];

            $jadwalMengajar = \App\Models\JadwalAbsensi::where('guru_id', $user->id)
                ->where('hari', $hariIni)
                ->with(['kelas', 'mataPelajaran', 'absensis' => function ($query) {
                    $query->whereDate('tanggal_absensi', Carbon::today());
                }])
                ->orderBy('jam_mulai')
                ->get();

            $now = Carbon::now();
            $jadwalMengajar->each(function ($jadwal) use ($now) {
                $jamSelesai = Carbon::parse($jadwal->jam_selesai);
                $absensiSudahDiambil = $jadwal->absensis->isNotEmpty();

                if ($absensiSudahDiambil) {
                    $jadwal->status_absensi = 'Selesai';
                } elseif ($now->gt($jamSelesai)) {
                    $jadwal->status_absensi = 'Terlewat';
                } else {
                    $jadwal->status_absensi = 'Belum Diisi';
                }
            });

            // Logika untuk statistik wali kelas (jika ada)
            if ($kelasDiampu) {
                $siswaIds = \App\Models\SiswaProfile::where('kelas_id', $kelasDiampu->id)->pluck('user_id');
                $stats['total_siswa'] = $siswaIds->count();

                $siswaHadirIds = Absensi::whereIn('user_id', $siswaIds)
                    ->whereDate('tanggal_absensi', Carbon::today())
                    ->pluck('user_id');
                $stats['total_hadir'] = $siswaHadirIds->count();
                
                $stats['total_absen'] = $stats['total_siswa'] - $stats['total_hadir'];

                $siswaAbsenIds = $siswaIds->diff($siswaHadirIds);
                $siswaAbsen = User::whereIn('id', $siswaAbsenIds)->get();
            }

            return view('guru.dashboard', compact('kelasDiampu', 'stats', 'siswaAbsen', 'jadwalMengajar', 'totalMataPelajaranDiampu', 'totalKelasDiajar', 'absensiTercatatHariIni'));

        } elseif ($user->role === 'siswa') {
            // Logika baru untuk Dasbor Siswa
            $query = Absensi::where('user_id', $user->id);

            $totalHadir = (clone $query)->where('status', 'hadir')->count();
            $totalTerlambat = (clone $query)->where('status', 'terlambat')->count();
            $totalAbsensi = $totalHadir + $totalTerlambat;

            // Hitung persentase kehadiran
            $persentaseKehadiran = ($totalAbsensi > 0) ? round(($totalHadir / $totalAbsensi) * 100) : 0;

            $riwayatTerbaru = (clone $query)->latest()->take(5)->get();

            return view('siswa.dashboard', compact(
                'totalHadir',
                'totalTerlambat',
                'persentaseKehadiran',
                'riwayatTerbaru'
            ));
        }

        // Fallback jika role tidak valid (tidak berubah)
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('error', 'Role akun Anda tidak valid. Anda telah di-logout.');
    }

    // Proses saat siswa scan QR Code
    public function scan($token)
    {
        $user = Auth::user();
        $validToken = \Illuminate\Support\Facades\Cache::get('absensi_token');

        // Pastikan hanya siswa yang bisa scan
        if ($user->role !== 'siswa') {
            return redirect()->route('dashboard')->with('error', 'Hanya siswa yang dapat melakukan absensi.');
        }

        // Cek apakah token valid
        if (!$validToken || $validToken !== $token) {
            return redirect()->route('dashboard')->with('error', 'QR Code tidak valid atau sudah kedaluwarsa.');
        }

        // Cek apakah siswa sudah absen hari ini
        $today = \Carbon\Carbon::today();
        $now = \Carbon\Carbon::now();
        $sudahAbsen = Absensi::where('user_id', $user->id)->whereDate('tanggal_absensi', $today)->exists();

        if ($sudahAbsen) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah tercatat hadir hari ini.');
        }

        // --- LOGIKA BARU UNTUK STATUS ---
        // Ambil jam masuk dari pengaturan, default ke 07:00 jika tidak ada
        $jamMasuk = Cache::rememberForever('setting_jam_masuk', function () {
            return Setting::where('key', 'jam_masuk')->first()->value ?? '07:00';
        });

        // Gabungkan tanggal hari ini dengan jam masuk
        $waktuBatasMasuk = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $today->format('Y-m-d') . ' ' . $jamMasuk);

        $status = $now->gt($waktuBatasMasuk) ? 'terlambat' : 'hadir';
        // --- AKHIR LOGIKA BARU ---

        Absensi::create([
            'user_id' => $user->id,
            'tanggal_absensi' => $today,
            'waktu_masuk' => $now->toTimeString(),
            'status' => $status, // Simpan status
        ]);

        \Illuminate\Support\Facades\Cache::forget('absensi_token');

        $pesanSukses = 'Kehadiran berhasil dicatat. Status: ' . ucfirst($status);
        return redirect()->route('dashboard')->with('success', $pesanSukses);
    }

    /**
     * Display QR code for printing
     * 
     * @return \Illuminate\View\View
     */
    public function cetakQrCode()
    {
        // Generate random token
        $token = \Illuminate\Support\Str::random(40);
        
        // Store token in cache for 5 minutes
        \Illuminate\Support\Facades\Cache::put('absensi_token', $token, now()->addMinutes(5));
        
        // Generate scan URL with token
        $scanUrl = route('absensi.scan', ['token' => $token]);

        // Return view with scan URL
        return view('guru.cetak-qrcode', compact('scanUrl'));
    }

    /**
     * Process QR code scan from AJAX request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Menampilkan daftar siswa berdasarkan kelas yang diampu oleh guru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function indexSiswaByKelas(Request $request)
    {
        // Pastikan hanya guru yang bisa mengakses
        Gate::authorize('isGuru');

        $user = Auth::user();
        $kelasDiampu = Kelas::where('wali_kelas_id', $user->id)->first();

        if (!$kelasDiampu) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak diampu sebagai wali kelas manapun.');
        }

        $query = SiswaProfile::where('kelas_id', $kelasDiampu->id)->with('user');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        $siswaList = $query->paginate(10);

        return view('guru.absensi.index-siswa', compact('siswaList', 'kelasDiampu'));
    }

    /**
     * Menampilkan form untuk input absensi manual.
     *
     * @param  \App\Models\User  $siswa
     * @return \Illuminate\View\View
     */
    public function createManualAbsensi(User $siswa)
    {
        Gate::authorize('isGuru');

        $user = Auth::user();
        $kelasDiampu = Kelas::where('wali_kelas_id', $user->id)->first();

        // Pastikan siswa yang akan diabsen adalah siswa dari kelas yang diampu guru
        if (!$kelasDiampu || $siswa->siswaProfile->kelas_id !== $kelasDiampu->id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengelola absensi siswa ini.');
        }

        return view('guru.absensi.create-manual', compact('siswa'));
    }

    /**
     * Menyimpan absensi manual yang diinput oleh guru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeManualAbsensi(Request $request)
    {
        Gate::authorize('isGuru');

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_absensi' => 'required|date',
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $kelasDiampu = Kelas::where('wali_kelas_id', $user->id)->first();
        $siswa = User::find($validatedData['user_id']);

        // Pastikan siswa yang akan diabsen adalah siswa dari kelas yang diampu guru
        if (!$kelasDiampu || !$siswa || $siswa->siswaProfile->kelas_id !== $kelasDiampu->id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengelola absensi siswa ini.');
        }

        // Cek apakah absensi untuk siswa dan tanggal yang sama sudah ada
        $existingAbsensi = Absensi::where('user_id', $validatedData['user_id'])
                                  ->whereDate('tanggal_absensi', $validatedData['tanggal_absensi'])
                                  ->first();

        if ($existingAbsensi) {
            return back()->with('error', 'Absensi untuk siswa ini pada tanggal tersebut sudah ada. Silakan edit jika ingin mengubah.');
        }

        // Tentukan waktu masuk (jika status hadir/terlambat, gunakan waktu saat ini, jika tidak, null)
        $waktuMasuk = null;
        if (in_array($validatedData['status'], ['hadir', 'terlambat'])) {
            $waktuMasuk = Carbon::now()->toTimeString();
        }

        Absensi::create([
            'user_id' => $validatedData['user_id'],
            'tanggal_absensi' => $validatedData['tanggal_absensi'],
            'waktu_masuk' => $waktuMasuk,
            'status' => $validatedData['status'],
            'keterangan' => $validatedData['keterangan'],
            'jadwal_absensi_id' => null, // Untuk absensi manual, jadwal bisa null atau disesuaikan
        ]);

        return redirect()->route('guru.absensi.index-siswa')->with('success', 'Absensi siswa berhasil dicatat secara manual.');
    }

    /**
     * Process QR code scan from AJAX request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_code_data' => 'required|string',
        ]);

        $user = Auth::user();

        // Pastikan hanya siswa yang bisa melakukan absensi melalui scan
        if ($user->role !== 'siswa') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya siswa yang dapat melakukan absensi.'], 403);
        }

        $scannedToken = $request->input('qr_code_data');
        $validToken = Cache::get('absensi_token');

        // Cek apakah token valid dan cocok
        if (!$validToken || $validToken !== $scannedToken) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau sudah kedaluwarsa.'], 400);
        }

        // Cek apakah siswa sudah absen hari ini
        $today = Carbon::today();
        $now = Carbon::now();
        $sudahAbsen = Absensi::where('user_id', $user->id)->whereDate('tanggal_absensi', $today)->exists();

        if ($sudahAbsen) {
            return response()->json(['success' => false, 'message' => 'Anda sudah tercatat hadir hari ini.'], 409);
        }

        // Ambil jam masuk dari pengaturan, default ke 07:00 jika tidak ada
        $jamMasuk = Cache::rememberForever('setting_jam_masuk', function () {
            return Setting::where('key', 'jam_masuk')->first()->value ?? '07:00';
        });

        // Gabungkan tanggal hari ini dengan jam masuk
        $waktuBatasMasuk = Carbon::createFromFormat('Y-m-d H:i', $today->format('Y-m-d') . ' ' . $jamMasuk);

        $status = $now->gt($waktuBatasMasuk) ? 'terlambat' : 'hadir';

        // Cari jadwal absensi yang sesuai
        $jadwalAbsensiId = null;
        $siswaProfile = $user->siswaProfile; // Asumsi relasi siswaProfile ada di model User

        if ($siswaProfile && $siswaProfile->kelas_id) {
            $currentDay = Carbon::now()->dayName; // Nama hari dalam bahasa Inggris (e.g., 'Monday')
            // Konversi nama hari ke bahasa Indonesia jika diperlukan untuk pencarian di DB
            $hariMap = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ];
            $hariIndonesia = $hariMap[$currentDay] ?? $currentDay;

            $jadwal = \App\Models\JadwalAbsensi::where('kelas_id', $siswaProfile->kelas_id)
                ->where('hari', $hariIndonesia)
                ->where('jam_mulai', '<=', $now->toTimeString())
                ->where('jam_selesai', '>=', $now->toTimeString())
                ->first();

            if ($jadwal) {
                $jadwalAbsensiId = $jadwal->id;
            }
        }

        Absensi::create([
            'user_id' => $user->id,
            'tanggal_absensi' => $today,
            'waktu_masuk' => $now->toTimeString(),
            'status' => $status,
            'jadwal_absensi_id' => $jadwalAbsensiId, // Simpan jadwal_absensi_id
            'attendance_type' => 'qr_code', // Tambahkan ini
        ]);

        // Hapus token setelah digunakan untuk mencegah penggunaan berulang
        Cache::forget('absensi_token');

        return response()->json(['success' => true, 'message' => 'Kehadiran berhasil dicatat. Status: ' . ucfirst($status)]);
    }
}
