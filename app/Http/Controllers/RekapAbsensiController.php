<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\JadwalAbsensi;
use App\Models\MataPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapAbsensiExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RekapAbsensiController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Absensi::with([
            'user',
            'jadwalAbsensi.kelas',
            'jadwalAbsensi.mataPelajaran',
            'jadwalAbsensi.guru'
        ]);

        // Role-based access control
        if ($user->role === 'guru') {
            $query->whereHas('jadwalAbsensi', function ($q) use ($user) {
                $q->where('guru_id', $user->id);
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_absensi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_absensi', '<=', $request->end_date);
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('jadwalAbsensi.kelas', function ($q) use ($request) {
                $q->where('id', $request->kelas_id);
            });
        }

        // Filter by mata pelajaran
        if ($request->filled('mata_pelajaran_id')) {
            $query->whereHas('jadwalAbsensi.mataPelajaran', function ($q) use ($request) {
                $q->where('id', $request->mata_pelajaran_id);
            });
        }

        // Filter by guru (only for admin)
        if ($user->role === 'admin' && $request->filled('guru_id')) {
            $query->whereHas('jadwalAbsensi.guru', function ($q) use ($request) {
                $q->where('id', $request->guru_id);
            });
        }

        // Filter by student (user_id)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by attendance type (manual/qr_code)
        if ($request->filled('attendance_type')) {
            $query->where('attendance_type', $request->attendance_type);
        }

        // Search term for student name, teacher name, subject name, class name
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($qr) use ($searchTerm) {
                    $qr->where('name', 'like', $searchTerm);
                })
                ->orWhereHas('jadwalAbsensi.guru', function ($qr) use ($searchTerm) {
                    $qr->where('name', 'like', $searchTerm);
                })
                ->orWhereHas('jadwalAbsensi.mataPelajaran', function ($qr) use ($searchTerm) {
                    $qr->where('nama_mapel', 'like', $searchTerm);
                })
                ->orWhereHas('jadwalAbsensi.kelas', function ($qr) use ($searchTerm) {
                    $qr->where('nama_kelas', 'like', $searchTerm);
                });
            });
        }

        $absensis = $query->orderBy('tanggal_absensi', 'desc')
                          ->orderBy('waktu_masuk', 'desc')
                          ->paginate(100); // Paginate the results

        // Calculate summary statistics
        $summaryQuery = clone $query; // Clone the query to avoid interfering with pagination
        $summary = $summaryQuery->select('status', DB::raw('count(*) as total'))
                                ->groupBy('status')
                                ->pluck('total', 'status')
                                ->toArray();

        // Ensure all statuses are present in summary with default 0
        $allStatuses = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];
        foreach ($allStatuses as $status) {
            if (!isset($summary[$status])) {
                $summary[$status] = 0;
            }
        }

        // Get data for filter dropdowns based on role
        if ($user->isGuru()) {
            $guruId = $user->id;
            $kelasIds = JadwalAbsensi::where('guru_id', $guruId)->distinct()->pluck('kelas_id');
            $mapelIds = JadwalAbsensi::where('guru_id', $guruId)->distinct()->pluck('mata_pelajaran_id');
            
            $allKelas = Kelas::whereIn('id', $kelasIds)->orderBy('nama_kelas')->get();
            $allMataPelajaran = MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama_mapel')->get();
            $allGurus = User::where('id', $guruId)->get();
            
            $siswaIds = \App\Models\SiswaProfile::whereIn('kelas_id', $kelasIds)->pluck('user_id');
            $allSiswa = User::whereIn('id', $siswaIds)->with('siswaProfile')->orderBy('name')->get();
        } else { // For admin
            $allKelas = Kelas::orderBy('nama_kelas')->get();
            $allMataPelajaran = MataPelajaran::orderBy('nama_mapel')->get();
            $allGurus = User::where('role', 'guru')->orderBy('name')->get();
            $allSiswa = User::where('role', 'siswa')->with('siswaProfile')->orderBy('name')->get();
        }

        return view('rekap_absensi.index', compact(
            'absensis',
            'allKelas',
            'allMataPelajaran',
            'allGurus',
            'allSiswa',
            'request',
            'summary' // Pass summary data to the view
        ));
    }

    public function create()
    {
        $this->authorize('create', Absensi::class);

        $user = auth()->user();
        $jadwalQuery = JadwalAbsensi::with(['kelas', 'mataPelajaran', 'guru']);

        if ($user->isGuru()) {
            $jadwalQuery->where('guru_id', $user->id);
        }

        $allJadwal = $jadwalQuery->get();
        $allSiswa = User::where('role', 'siswa')->with('siswaProfile')->orderBy('name')->get();
        $statusOptions = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];

        return view('rekap_absensi.create', compact('allSiswa', 'allJadwal', 'statusOptions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Absensi::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'jadwal_absensi_id' => 'required|exists:jadwal_absensis,id',
            'tanggal_absensi' => 'required|date',
            'status' => 'required|in:hadir,terlambat,sakit,izin,alpha',
            'waktu_masuk' => 'nullable|date_format:H:i',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Check for duplicate entry
        $existing = Absensi::where('user_id', $validated['user_id'])
                             ->where('jadwal_absensi_id', $validated['jadwal_absensi_id'])
                             ->where('tanggal_absensi', $validated['tanggal_absensi'])
                             ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan. Data absensi untuk siswa pada jadwal dan tanggal yang sama sudah ada.');
        }

        Absensi::create([
            'user_id' => $validated['user_id'],
            'jadwal_absensi_id' => $validated['jadwal_absensi_id'],
            'tanggal_absensi' => $validated['tanggal_absensi'],
            'status' => $validated['status'],
            'waktu_masuk' => $request->filled('waktu_masuk') ? Carbon::parse($validated['waktu_masuk'])->format('H:i:s') : null,
            'keterangan' => $validated['keterangan'],
            'attendance_type' => 'manual', // Set attendance type to manual
        ]);

        return redirect()->route('rekap_absensi.index')->with('success', 'Data absensi manual berhasil ditambahkan.');
    }

    public function edit(Absensi $absensi)
    {
        $this->authorize('update', $absensi);

        // Load relationships for display
        $absensi->load('user', 'jadwalAbsensi.kelas', 'jadwalAbsensi.mataPelajaran', 'jadwalAbsensi.guru');

        $statusOptions = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];
        $attendanceTypeOptions = ['manual', 'qr_code'];

        return view('rekap_absensi.edit', compact(
            'absensi',
            'statusOptions',
            'attendanceTypeOptions'
        ));
    }

    public function update(Request $request, Absensi $absensi)
    {
        $this->authorize('update', $absensi);

        $validated = $request->validate([
            'status' => 'required|in:hadir,terlambat,sakit,izin,alpha',
            'keterangan' => 'nullable|string|max:255',
            'waktu_masuk' => 'nullable|date_format:H:i',
            'attendance_type' => 'required|in:manual,qr_code',
        ]);

        $absensi->status = $validated['status'];
        $absensi->keterangan = $validated['keterangan'];
        $absensi->attendance_type = $validated['attendance_type'];

        // Update waktu_masuk only if provided, otherwise keep existing
        if ($request->filled('waktu_masuk')) {
            $absensi->waktu_masuk = Carbon::parse($validated['waktu_masuk'])->format('H:i:s');
        } else {
            $absensi->waktu_masuk = null; // Or keep existing if that's the desired behavior
        }

        $absensi->save();

        return redirect()->route('rekap_absensi.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy(Absensi $absensi)
    {
        $this->authorize('delete', $absensi);

        $absensi->delete();

        return response()->json(['success' => true, 'message' => 'Data absensi berhasil dihapus.']);
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('bulkDelete', Absensi::class);

        $request->validate([
            'absensi_ids' => 'required|array',
            'absensi_ids.*' => 'exists:absensis,id',
        ]);

        Absensi::whereIn('id', $request->absensi_ids)->delete();

        return response()->json(['success' => true, 'message' => 'Data absensi terpilih berhasil dihapus.']);
    }

    public function export(Request $request)
    {
        $this->authorize('export', Absensi::class);

        return Excel::download(new RekapAbsensiExport($request), 'rekap_absensi_' . Carbon::now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Menampilkan laporan absensi untuk siswa yang sedang login.
     */
    public function laporanSiswa(Request $request)
    {
        $user = auth()->user();

        // Pastikan user adalah siswa
        if ($user->role !== 'siswa') {
            abort(403, 'Hanya siswa yang dapat mengakses halaman ini.');
        }

        $query = Absensi::where('user_id', $user->id)
                        ->with(['jadwalAbsensi.mataPelajaran', 'jadwalAbsensi.guru'])
                        ->orderBy('tanggal_absensi', 'desc')
                        ->orderBy('waktu_masuk', 'desc');

        // Filter berdasarkan rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_absensi', [$request->start_date, $request->end_date]);
        }

        $absensiSiswa = $query->paginate(50);

        // Hitung rekapitulasi status absensi dari seluruh data yang difilter (sebelum pagination)
        $summaryQuery = clone $query;
        $summary = $summaryQuery->select('status', DB::raw('count(*) as total'))
                                ->groupBy('status')
                                ->pluck('total', 'status');

        // Pastikan semua status ada di summary
        $allStatuses = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];
        foreach ($allStatuses as $status) {
            if (!$summary->has($status)) {
                $summary->put($status, 0);
            }
        }

        return view('rekap_absensi.laporan_siswa', compact('absensiSiswa', 'summary'));
    }

    /**
     * Handle the export of student's attendance report to Excel.
     */
    public function exportLaporanSiswa(Request $request)
    {
        $user = auth()->user();
        $fileName = 'laporan_absensi_' . str_replace(' ', '_', strtolower($user->name)) . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new \App\Exports\SiswaLaporanAbsensiExport($request), $fileName);
    }
}

