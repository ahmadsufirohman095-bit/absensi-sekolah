<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absensi;
use App\Models\Setting;
use App\Models\JadwalAbsensi;
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

        $query = JadwalAbsensi::with(['kelas', 'mataPelajaran', 'guru'])
                               ->where('hari', $currentDayName);

        if ($user->hasRole('guru')) {
            $query->where('guru_id', $user->id);
        }

        $availableSchedules = $query->orderBy('jam_mulai')->get();

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
            'jadwal_absensi_id' => 'required|exists:jadwal_absensis,id',
        ]);

        $identifier = $validated['identifier'];
        $jadwalAbsensiId = $validated['jadwal_absensi_id'];
        Log::info('Menerima permintaan absensi untuk identifier:', ['identifier' => $identifier, 'jadwal_absensi_id' => $jadwalAbsensiId]);

        // 2. Optimalkan query pencarian user dan relasi profile
        $user = User::with('siswaProfile.kelas')
                    ->where('identifier', $identifier)
                    ->where('role', 'siswa')
                    ->first();

        if (!$user) {
            Log::warning('Scan Gagal: Identifier tidak valid atau bukan siswa.', compact('identifier'));
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau bukan milik siswa.'
            ], 404);
        }

        // Get the selected JadwalAbsensi
        $jadwalAbsensi = JadwalAbsensi::with(['kelas', 'mataPelajaran', 'guru'])->find($jadwalAbsensiId);

        if (!$jadwalAbsensi) {
            Log::warning('Scan Gagal: Jadwal Absensi tidak ditemukan.', compact('jadwalAbsensiId'));
            return response()->json([
                'success' => false,
                'message' => 'Jadwal absensi tidak valid.'
            ], 404);
        }

        // Check if the student belongs to the class of the selected schedule
        if ($user->siswaProfile->kelas_id !== $jadwalAbsensi->kelas_id) {
            Log::warning('Scan Gagal: Siswa tidak terdaftar di kelas jadwal yang dipilih.', ['user_id' => $user->id, 'kelas_siswa' => $user->siswaProfile->kelas_id, 'kelas_jadwal' => $jadwalAbsensi->kelas_id]);
            return response()->json([
                'success' => false,
                'message' => 'Siswa ' . $user->name . ' tidak terdaftar di kelas ini.'
            ], 400);
        }

        // 3. Cek absensi yang lebih efisien untuk jadwal spesifik
        $today = Carbon::now(config('app.timezone', 'Asia/Jakarta'))->startOfDay();
        $existingAbsensi = Absensi::where('user_id', $user->id)
                                    ->where('jadwal_absensi_id', $jadwalAbsensiId)
                                    ->whereDate('tanggal_absensi', $today)
                                    ->first();

        if ($existingAbsensi) {
            $statusSaatIni = $existingAbsensi->status;
            // Jika status sudah sakit, izin, atau alpha, jangan timpa dengan scan QR
            if (in_array($statusSaatIni, ['sakit', 'izin', 'alpha'])) {
                Log::info('Absensi tidak diizinkan: Siswa sudah tercatat dengan status khusus.', ['user_id' => $user->id, 'name' => $user->name, 'status' => $statusSaatIni]);
                return response()->json([
                    'success' => false,
                    'message' => 'Absensi tidak dapat diubah: ' . $user->name . ' sudah tercatat dengan status ' . ucfirst($statusSaatIni) . ' untuk jadwal ini hari ini.'
                ]);
            }
            // Jika status sudah hadir atau terlambat, anggap sudah absen
            else if (in_array($statusSaatIni, ['hadir', 'terlambat'])) {
                Log::info('Duplikasi Absensi: Siswa sudah absen untuk jadwal ini hari ini.', ['user_id' => $user->id, 'name' => $user->name, 'jadwal_absensi_id' => $jadwalAbsensiId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Sudah Absen: ' . $user->name . ' telah tercatat ' . ucfirst($statusSaatIni) . ' untuk jadwal ini hari ini.'
                ]);
            }
        }

        // 4. Tentukan status absensi berdasarkan jam_mulai jadwal
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $jadwalStartTime = Carbon::parse($jadwalAbsensi->jam_mulai);
        $jadwalEndTime = Carbon::parse($jadwalAbsensi->jam_selesai);

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
                'jadwal_absensi_id' => $jadwalAbsensiId, // Save jadwal_absensi_id
                'attendance_type' => 'qr_code',
            ]);
            Log::info('Absensi berhasil disimpan.', ['absensi_id' => $absensi->id, 'user_id' => $user->id, 'status' => $status, 'jadwal_absensi_id' => $jadwalAbsensiId]);
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
        return response()->json([
            'success' => true,
            'message' => "Absensi berhasil: {$user->name} tercatat {$status}.",
            'data' => [
                'name' => $user->name,
                'identifier' => $user->identifier,
                'kelas' => $user->siswaProfile?->kelas?->nama_kelas ?? 'Belum diatur',
                'waktu' => $now->format('H:i:s'),
                'status' => $status,
                'mata_pelajaran' => $jadwalAbsensi->mataPelajaran->nama_mapel,
                'guru' => $jadwalAbsensi->guru->name,
            ]
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
            'jadwal_absensi_id' => 'required|exists:jadwal_absensis,id',
        ]);

        $jadwalAbsensiId = $request->input('jadwal_absensi_id');
        $today = Carbon::now(config('app.timezone', 'Asia/Jakarta'))->startOfDay();

        $scannedStudents = Absensi::with(['user.siswaProfile.kelas'])
            ->where('jadwal_absensi_id', $jadwalAbsensiId)
            ->whereDate('tanggal_absensi', $today)
            ->get()
            ->map(function ($absensi) {
                return [
                    'name' => $absensi->user->name,
                    'kelas' => $absensi->user->siswaProfile?->kelas?->nama_kelas ?? 'Belum diatur',
                    'waktu' => Carbon::parse($absensi->waktu_masuk)->format('H:i:s'),
                    'status' => $absensi->status,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $scannedStudents,
        ]);
    }
}
