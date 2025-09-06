<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeachingScheduleExport;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalAbsensi;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $totalUsers = User::count();
            $totalKelas = Kelas::count();
            $totalMataPelajaran = MataPelajaran::count();
            $totalJadwalAbsensi = JadwalAbsensi::count();

            return view('admin.dashboard', compact('totalUsers', 'totalKelas', 'totalMataPelajaran', 'totalJadwalAbsensi'));
        } elseif ($user->hasRole('guru')) {
            $guruProfile = \App\Models\GuruProfile::where('user_id', $user->id)->first();
            $kelasDiampu = Kelas::where('wali_kelas_id', $user->id)->first();

            if ($guruProfile) {
            $dayMap = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
            ];
            $hariIni = $dayMap[now()->dayOfWeek];

            $jadwalMengajar = JadwalAbsensi::where('guru_id', $user->id)
                ->whereRaw('LOWER(hari) = ?', [mb_strtolower($hariIni)])
                ->with(['mataPelajaran', 'kelas'])
                ->orderBy('jam_mulai')
                ->get();
            } else {
                $jadwalMengajar = collect(); // Empty collection if no profile
            }

            // Ambil daftar siswa yang belum hadir hari ini di kelas yang diampu
            $siswaAbsen = collect();
            if ($kelasDiampu) {
                // Query yang dioptimalkan: Mengambil siswa di kelas yang diampu 
                // yang TIDAK memiliki record absensi pada hari ini.
                                $siswaAbsen = User::with('siswaProfile') // Eager load the profile
                    ->whereHas('siswaProfile', function ($query) use ($kelasDiampu) {
                        $query->where('kelas_id', $kelasDiampu->id);
                    })
                    ->whereDoesntHave('absensi', function ($query) {
                        $query->whereDate('tanggal_absensi', today());
                    })
                    ->get();
            }

            return view('guru.dashboard', compact('kelasDiampu', 'jadwalMengajar', 'siswaAbsen'));
        } elseif ($user->hasRole('siswa')) {
            $riwayatTerbaru = Absensi::where('user_id', $user->id)
                ->with(['jadwalAbsensi.mataPelajaran', 'jadwalAbsensi.kelas', 'jadwalAbsensi.guru'])
                ->orderBy('tanggal_absensi', 'desc')
                ->orderBy('waktu_masuk', 'desc')
                ->take(10)
                ->get();

            return view('siswa.dashboard', compact('riwayatTerbaru'));
        }

        return view('dashboard'); // Default dashboard jika peran tidak dikenali
    }

    public function chartData()
    {
        try {
            $totalAdmin = User::where('role', 'admin')->count();
            $totalGuru = User::where('role', 'guru')->count();
            $totalSiswa = User::where('role', 'siswa')->count();

            $dailySummary = $this->getDailyAttendanceSummary();

            $hadir = $dailySummary['hadir'] ?? 0;
            $terlambat = $dailySummary['terlambat'] ?? 0;
            $izin = $dailySummary['izin'] ?? 0;
            $sakit = $dailySummary['sakit'] ?? 0;
            $alpha = $dailySummary['alpha'] ?? 0;

            $data = [
                'totalUsers' => [
                    'labels' => ['Admin', 'Guru', 'Siswa'],
                    'data' => [$totalAdmin, $totalGuru, $totalSiswa],
                ],
                'kehadiran' => [
                    'labels' => ['Hadir', 'Terlambat', 'Izin & Sakit', 'Alpha'],
                    'data' => [$hadir, $terlambat, $izin + $sakit, $alpha],
                ],
            ];

            // Data Absensi Bulanan (untuk diagram batang)
            $monthlyAbsenceData = Absensi::select(
                    DB::raw('MONTH(tanggal_absensi) as month'),
                    DB::raw('COUNT(*) as total_absensi')
                )
                ->whereYear('tanggal_absensi', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $months = [];
            $absences = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthName = \Carbon\Carbon::create(null, $i, 1)->translatedFormat('F');
                $months[] = $monthName;
                $absences[] = $monthlyAbsenceData->where('month', $i)->first()->total_absensi ?? 0;
            }

            $data['monthlyAbsence'] = [
                'labels' => $months,
                'data' => $absences,
            ];

            // Data Jumlah Siswa per Kelas (untuk diagram lingkaran)
            $studentsPerClass = \App\Models\Kelas::withCount('siswaProfiles')->get();
            $classLabels = $studentsPerClass->pluck('nama_kelas')->toArray();
            $classData = $studentsPerClass->pluck('siswa_profiles_count')->toArray();

            $data['studentsPerClass'] = [
                'labels' => $classLabels,
                'data' => $classData,
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Error fetching chart data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load chart data.', 'details' => $e->getMessage()], 500);
        }
    }

    public function guruChartData()
    {
        $guru = auth()->user();
        $kelasDiampu = \App\Models\Kelas::where('wali_kelas_id', $guru->id)->first();

        $totalMataPelajaranDiampu = $guru->mataPelajarans()->count();
        $totalKelasDiajar = \App\Models\JadwalAbsensi::where('guru_id', $guru->id)->distinct('kelas_id')->count();
        $absensiTercatatHariIni = Absensi::whereHas('jadwalAbsensi', function ($query) use ($guru) {
            $query->where('guru_id', $guru->id);
        })->whereDate('tanggal_absensi', today())->count();

        $data = [
            'teachingSummary' => [
                'labels' => ['Mata Pelajaran', 'Kelas Diajar', 'Absensi Tercatat'],
                'data' => [$totalMataPelajaranDiampu, $totalKelasDiajar, $absensiTercatatHariIni],
            ],
        ];

        if ($kelasDiampu) {
            // Get total number of students in the class
            $totalSiswaDiKelas = $kelasDiampu->siswaProfiles()->count();

            // Get the number of students who were present today in that class
            $totalHadir = Absensi::whereHas('jadwalAbsensi', function($query) use ($kelasDiampu) {
                $query->where('kelas_id', $kelasDiampu->id);
            })
            ->whereDate('tanggal_absensi', today())
            ->whereIn('status', ['hadir', 'terlambat']) // Count both 'hadir' and 'terlambat' as present
            ->distinct('user_id')
            ->count();
            
            $totalAbsen = $totalSiswaDiKelas - $totalHadir;

            $data['rekapKehadiran'] = [
                'labels' => ['Hadir', 'Absen'],
                'data' => [$totalHadir, $totalAbsen],
            ];
        }

        $monthlyAbsenceDataGuru = Absensi::select(
                DB::raw('MONTH(tanggal_absensi) as month'),
                DB::raw('COUNT(*) as total_absensi')
            )
            ->whereHas('jadwalAbsensi', function ($query) use ($guru) {
                $query->where('guru_id', $guru->id);
            })
            ->whereYear('tanggal_absensi', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthsGuru = [];
        $absencesGuru = [];
        $monthlyDataMap = $monthlyAbsenceDataGuru->keyBy('month');

        for ($i = 1; $i <= 12; $i++) {
            $monthName = \Carbon\Carbon::create(null, $i, 1)->translatedFormat('F');
            $monthsGuru[] = $monthName;
            $absencesGuru[] = $monthlyDataMap->get($i)->total_absensi ?? 0;
        }

        $data['monthlyAbsence'] = [
            'labels' => $monthsGuru,
            'data' => $absencesGuru,
        ];

        return response()->json($data);
    }

    public function siswaChartData()
    {
        $siswa = auth()->user();
        
        // Data for Pie Chart (This Month)
        $rekapBulanIni = Absensi::where('user_id', $siswa->id)
            ->whereMonth('tanggal_absensi', now()->month)
            ->whereYear('tanggal_absensi', now()->year)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Data for Bar Chart (By Subject)
        $rekapPerMapel = Absensi::where('user_id', $siswa->id)
            ->whereYear('tanggal_absensi', now()->year) // Limit to current year
            ->join('jadwal_absensis', 'absensis.jadwal_absensi_id', '=', 'jadwal_absensis.id')
            ->join('mata_pelajarans', 'jadwal_absensis.mata_pelajaran_id', '=', 'mata_pelajarans.id')
            ->select('mata_pelajarans.nama_mapel', DB::raw('count(absensis.id) as total'))
            ->groupBy('mata_pelajarans.nama_mapel')
            ->pluck('total', 'mata_pelajarans.nama_mapel');

        $data = [
            'rekapBulanIni' => [
                'labels' => $rekapBulanIni->keys()->map(fn($status) => ucfirst($status))->toArray(),
                'data' => $rekapBulanIni->values()->toArray(),
            ],
            'rekapPerMapel' => [
                'labels' => $rekapPerMapel->keys()->toArray(),
                'data' => $rekapPerMapel->values()->toArray(),
            ],
        ];

        return response()->json($data);
    }

    /**
     * Get daily attendance summary.
     *
     * @return array
     */
    private function getDailyAttendanceSummary(): array
    {
        $summary = Absensi::whereDate('tanggal_absensi', today())
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $allStatuses = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];
        foreach ($allStatuses as $status) {
            if (!isset($summary[$status])) {
                $summary[$status] = 0;
            }
        }

        return $summary;
    }
}
