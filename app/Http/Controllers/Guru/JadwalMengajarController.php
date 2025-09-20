<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalAbsensi;
use App\Models\GuruProfile;
use App\Models\Absensi;
use App\Models\User;

class JadwalMengajarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $guruProfile = GuruProfile::where('user_id', $user->id)->first();

        if (!$guruProfile) {
            abort(403, 'Profil guru tidak ditemukan.');
        }

        $selectedDate = $request->input('date', now()->toDateString());
        $carbonSelectedDate = \Carbon\Carbon::parse($selectedDate);

        $jadwalMengajar = JadwalAbsensi::where('guru_id', $user->id)
            ->with([
                'mataPelajaran',
                'kelas',
                'kelas.siswa.absensi' => function ($query) use ($carbonSelectedDate) {
                    $query->whereDate('tanggal_absensi', $carbonSelectedDate->toDateString());
                }
            ])
            ->get();

        $groupedJadwal = $jadwalMengajar->groupBy('hari')->map(function ($daySchedules) use ($carbonSelectedDate) {
            return $daySchedules->sortBy('jam_mulai')->map(function ($jadwal) use ($carbonSelectedDate) {
                $jadwal->status_pertemuan = $this->getMeetingStatus($jadwal, $carbonSelectedDate);
                
                $jadwal->siswa_dengan_absensi = $jadwal->kelas->siswa->map(function ($siswa) use ($jadwal) {
                    // Ambil absensi yang sudah di-eager load dan filter berdasarkan jadwal_absensi_id
                    $absensi = $siswa->absensi->where('jadwal_absensi_id', $jadwal->id)->first();

                    $siswa->absensi_status = $absensi ? $absensi->status : null;
                    $siswa->absensi_keterangan = $absensi ? $absensi->keterangan : null;
                    $siswa->absensi_waktu_masuk = $absensi ? $absensi->waktu_masuk : null;
                    return $siswa;
                });

                // Hitung ringkasan absensi untuk jadwal ini
                $jadwal->absensi_summary = $jadwal->siswa_dengan_absensi->groupBy('absensi_status')->map->count();
                $allStatuses = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha', null]; // Tambahkan null untuk 'Belum Tercatat'
                foreach ($allStatuses as $status) {
                    if (!isset($jadwal->absensi_summary[$status])) {
                        $jadwal->absensi_summary[$status] = 0;
                    }
                }
                
                return $jadwal;
            });
        })->sortKeysUsing(function ($a, $b) {
            $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
            return ($days[$a] ?? 99) <=> ($days[$b] ?? 99);
        });

        return view('guru.jadwal_mengajar.index', compact('groupedJadwal', 'selectedDate'));
    }

    private function getMeetingStatus($jadwal, $selectedDate)
    {
        $now = now();
        
        // Create Carbon instances for the start and end times on the selected date
        $jadwalStart = \Carbon\Carbon::parse($selectedDate)
                                    ->setTime($jadwal->jam_mulai->hour, $jadwal->jam_mulai->minute, $jadwal->jam_mulai->second);
        $jadwalEnd = \Carbon\Carbon::parse($selectedDate)
                                  ->setTime($jadwal->jam_selesai->hour, $jadwal->jam_selesai->minute, $jadwal->jam_selesai->second);

        // Dapatkan tanggal hari ini tanpa waktu
        $today = now()->startOfDay();
        // Dapatkan tanggal yang dipilih tanpa waktu
        $selectedDay = \Carbon\Carbon::parse($selectedDate)->startOfDay();

        // Map hari dalam seminggu ke angka (1 untuk Senin, ..., 7 untuk Minggu)
        $daysOfWeekMap = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7
        ];
        $jadwalDayNumber = $daysOfWeekMap[$jadwal->hari] ?? null;
        $currentDayNumber = $now->dayOfWeekIso; // 1 (for Monday) through 7 (for Sunday)

        // Jika tanggal yang dipilih sudah lewat dari hari ini
        if ($selectedDay->lessThan($today)) {
            return 'berlalu';
        } 
        // Jika tanggal yang dipilih adalah di masa depan dari hari ini
        elseif ($selectedDay->greaterThan($today)) {
            return 'mendatang';
        } 
        // Jika tanggal yang dipilih adalah hari ini
        else { 
            // Periksa apakah hari jadwal cocok dengan hari ini
            if ($jadwalDayNumber !== $currentDayNumber) {
                // Jika hari jadwal tidak cocok dengan hari ini, maka jadwal ini sudah berlalu (atau tidak relevan untuk hari ini)
                return 'berlalu';
            }

            // Jika hari jadwal cocok dengan hari ini, baru bandingkan waktu
            if ($now->greaterThan($jadwalEnd)) {
                return 'berlalu';
            } elseif ($now->greaterThanOrEqualTo($jadwalStart) && $now->lessThanOrEqualTo($jadwalEnd)) {
                return 'berlangsung';
            } else {
                return 'mendatang';
            }
        }
    }

    public function storeAttendance(Request $request, JadwalAbsensi $jadwal)
    {
        $request->validate([
            'siswa_id' => 'required|exists:users,id',
            'status' => 'required|in:hadir,terlambat,sakit,izin,alpha',
            'keterangan' => 'nullable|string|max:255',
            'tanggal_absensi' => 'required|date',
            'waktu_masuk' => 'nullable|date_format:H:i',
        ]);

        if ($jadwal->guru_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola absensi jadwal ini.');
        }

        $siswa = User::find($request->siswa_id);
        if (!$siswa || ($siswa->siswaProfile && $siswa->siswaProfile->kelas_id !== $jadwal->kelas_id)) {
            abort(400, 'Siswa tidak terdaftar di kelas ini.');
        }

        $waktuMasuk = $request->filled('waktu_masuk')
                      ? \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->tanggal_absensi . ' ' . $request->waktu_masuk)
                      : now();

        try {
            Absensi::updateOrCreate(
                [
                    'jadwal_absensi_id' => $jadwal->id,
                    'user_id' => $request->siswa_id,
                    'tanggal_absensi' => $request->tanggal_absensi,
                ],
                [
                    'status' => $request->status,
                    'keterangan' => $request->keterangan,
                    'waktu_masuk' => $waktuMasuk,
                    'attendance_type' => 'manual',
                ]
            );

            return response()->json([
                'message' => 'Absensi berhasil diperbarui.',
                'status' => 'success',
                'data' => [
                    'siswa_id' => $request->siswa_id,
                    'status' => $request->status,
                    'keterangan' => $request->keterangan,
                    'waktu_masuk' => $request->waktu_masuk,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal: ' . $e->getMessage(),
                'errors' => $e->errors(),
                'status' => 'error'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan absensi: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function exportExcel()
    {
        $user = Auth::user();
        $guruProfile = GuruProfile::where('user_id', $user->id)->first();

        if (!$guruProfile) {
            abort(403, 'Profil guru tidak ditemukan.');
        }

        $jadwalMengajar = JadwalAbsensi::where('guru_id', $user->id)->with(['mataPelajaran', 'kelas'])->get();

        $jadwalPelajaran = $jadwalMengajar->groupBy('hari')->sortKeysUsing(function ($a, $b) {
            $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
            return ($days[$a] ?? 99) <=> ($days[$b] ?? 99);
        });

        $fileName = 'Jadwal_Mengajar_' . str_replace(' ', '_', $user->name) . '_' . date('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\GuruJadwalMengajarExport($jadwalPelajaran, $user->name), $fileName);
    }
}
