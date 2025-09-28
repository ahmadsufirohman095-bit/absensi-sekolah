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

            // Ambil daftar siswa yang dimiliki oleh wali kelas tersebut
            $daftarSiswaWaliKelas = collect();
            if ($kelasDiampu) {
                $daftarSiswaWaliKelas = User::with('siswaProfile') // Eager load the profile
                    ->whereHas('siswaProfile', function ($query) use ($kelasDiampu) {
                        $query->where('kelas_id', $kelasDiampu->id);
                    })
                    ->get();
            }

            return view('guru.dashboard', compact('kelasDiampu', 'jadwalMengajar', 'daftarSiswaWaliKelas'));
        } elseif ($user->hasRole('siswa')) {
            $riwayatTerbaru = Absensi::where('user_id', $user->id)
                ->with(['jadwalAbsensi' => function ($query) {
                    $query->withTrashed()->with(['mataPelajaran', 'kelas', 'guru']);
                }])
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

        $teachingSummaryData = JadwalAbsensi::where('guru_id', $guru->id)
            ->join('mata_pelajarans', 'jadwal_absensis.mata_pelajaran_id', '=', 'mata_pelajarans.id')
            ->select(
                'mata_pelajarans.nama_mapel',
                DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(jam_selesai, jam_mulai)) / 3600) as total_hours')
            )
            ->groupBy('mata_pelajarans.nama_mapel')
            ->get();

        $teachingSummaryLabels = $teachingSummaryData->pluck('nama_mapel')->toArray();
        $teachingSummaryValues = $teachingSummaryData->pluck('total_hours')->map(fn($hours) => round($hours, 1))->toArray();

        $data = [
            'teachingSummary' => [
                'labels' => $teachingSummaryLabels,
                'data' => $teachingSummaryValues,
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
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->whereHas('jadwalAbsensi', function ($query) use ($guru) {
                $query->where('guru_id', $guru->id);
            })
            ->whereYear('tanggal_absensi', now()->year)
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get();

        $monthsGuru = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthsGuru[] = \Carbon\Carbon::create(null, $i, 1)->translatedFormat('F');
        }

        $statuses = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];
        $datasets = [];

        foreach ($statuses as $status) {
            $dataForStatus = [];
            for ($i = 1; $i <= 12; $i++) {
                $count = $monthlyAbsenceDataGuru
                    ->where('month', $i)
                    ->where('status', $status)
                    ->sum('count');
                $dataForStatus[] = $count;
            }
            $datasets[] = [
                'label' => ucfirst($status),
                'data' => $dataForStatus,
                'backgroundColor' => $this->getStatusColor($status, 0.5),
                'borderColor' => $this->getStatusColor($status, 1),
                'borderWidth' => 1,
            ];
        }

        $data['monthlyAbsence'] = [
            'labels' => $monthsGuru,
            'datasets' => $datasets,
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

    /**
     * Get color for attendance status.
     *
     * @param string $status
     * @param float $alpha
     * @return string
     */
    private function getStatusColor(string $status, float $alpha = 1.0): string
    {
        switch ($status) {
            case 'hadir':
                return "rgba(52, 211, 153, {$alpha})"; // Green
            case 'terlambat':
                return "rgba(251, 191, 36, {$alpha})"; // Yellow
            case 'sakit':
                return "rgba(96, 165, 250, {$alpha})"; // Blue
            case 'izin':
                return "rgba(167, 139, 250, {$alpha})"; // Purple
            case 'alpha':
                return "rgba(248, 113, 113, {$alpha})"; // Red
            default:
                return "rgba(107, 114, 128, {$alpha})"; // Gray
        }
    }
}
