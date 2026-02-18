<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiPegawai; // Menggunakan model AbsensiPegawai
use App\Models\JadwalAbsensiPegawai; // Menggunakan model JadwalAbsensiPegawai
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapAbsensiPegawaiExport; // Akan kita buat nanti
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RekapAbsensiPegawaiController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = AbsensiPegawai::with([
            'user' => function ($query) {
                $query->withTrashed();
            },
            'jadwalAbsensiPegawai' => function ($query) {
                $query->withTrashed();
            }
        ]);

        // Role-based access control
        // Admin bisa melihat semua, guru dan tu hanya melihat dirinya sendiri
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_absensi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_absensi', '<=', $request->end_date);
        }

        // Filter by user (pegawai: guru, tu, other)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by user role (pegawai_role)
        if ($request->filled('pegawai_role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->pegawai_role);
            });
        }

        // Filter by attendance type (manual/qr_code)
        if ($request->filled('attendance_type')) {
            $query->where('attendance_type', $request->attendance_type);
        }

        // Search term for employee name
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm);
            });
        }

        $absensisPegawai = $query->orderBy('tanggal_absensi', 'desc')
                          ->orderBy('waktu_masuk', 'desc')
                          ->paginate(100); // Paginate the results

        // Calculate summary statistics
        $summaryQuery = clone $query; // Clone the query to avoid interfering with pagination
        $summary = $summaryQuery->reorder()->select('status', DB::raw('count(*) as total'))
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
        if ($user->isAdmin()) {
            $allPegawai = User::whereIn('role', ['guru', 'tu', 'other'])->orderBy('name')->get();
        } else { // Guru or TU, only show themselves
            $allPegawai = User::where('id', $user->id)->get();
        }

        return view('rekap_absensi_pegawai.index', compact(
            'absensisPegawai', // Mengganti absensis menjadi absensisPegawai
            'allPegawai',
            'request',
            'summary' // Pass summary data to the view
        ));
    }

    public function create()
    {
        $this->authorize('create', AbsensiPegawai::class);

        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $allUsers = User::whereIn('role', ['guru', 'tu', 'other'])->orderBy('name')->get();
            $allJadwalPegawai = JadwalAbsensiPegawai::with('user')->get();
        } else { // Guru or TU can only create for themselves
            $allUsers = User::where('id', $user->id)->get();
            $allJadwalPegawai = JadwalAbsensiPegawai::where('user_id', $user->id)->with('user')->get();
        }
        
        $statusOptions = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];

        return view('rekap_absensi_pegawai.create', compact('allUsers', 'allJadwalPegawai', 'statusOptions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AbsensiPegawai::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'jadwal_absensi_pegawai_id' => 'nullable|exists:jadwal_absensi_pegawais,id', // Make nullable
            'tanggal_absensi' => 'required|date',
            'status' => 'required|in:hadir,terlambat,sakit,izin,alpha',
            'waktu_masuk' => 'nullable|date_format:H:i',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Check for duplicate entry
        $existing = AbsensiPegawai::where('user_id', $validated['user_id'])
                             ->where('tanggal_absensi', $validated['tanggal_absensi'])
                             ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan. Data absensi untuk pegawai pada tanggal yang sama sudah ada.');
        }

        AbsensiPegawai::create([
            'user_id' => $validated['user_id'],
            'jadwal_absensi_pegawai_id' => $validated['jadwal_absensi_pegawai_id'],
            'tanggal_absensi' => $validated['tanggal_absensi'],
            'status' => $validated['status'],
            'waktu_masuk' => $request->filled('waktu_masuk') ? Carbon::parse($validated['waktu_masuk'])->format('H:i:s') : null,
            'keterangan' => $validated['keterangan'],
            'attendance_type' => 'manual', // Set attendance type to manual
        ]);

        return redirect()->route('rekap_absensi_pegawai.index')->with('success', 'Data absensi manual pegawai berhasil ditambahkan.');
    }

    public function edit(AbsensiPegawai $absensiPegawai)
    {
        $this->authorize('update', $absensiPegawai);

        // Load relationships for display
        $absensiPegawai->load([
            'user' => function ($query) {
                $query->withTrashed();
            },
            'jadwalAbsensiPegawai' => function ($query) {
                $query->withTrashed();
            }
        ]);

        $statusOptions = ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'];
        $attendanceTypeOptions = ['manual', 'qr_code'];

        return view('rekap_absensi_pegawai.edit', compact(
            'absensiPegawai',
            'statusOptions',
            'attendanceTypeOptions'
        ));
    }

    public function update(Request $request, AbsensiPegawai $absensiPegawai)
    {
        $this->authorize('update', $absensiPegawai);

        $validated = $request->validate([
            'status' => 'required|in:hadir,terlambat,sakit,izin,alpha',
            'keterangan' => 'nullable|string|max:255',
            'waktu_masuk' => 'nullable|date_format:H:i',
            'attendance_type' => 'required|in:manual,qr_code',
        ]);

        $absensiPegawai->status = $validated['status'];
        $absensiPegawai->keterangan = $validated['keterangan'];
        $absensiPegawai->attendance_type = $validated['attendance_type'];

        // Update waktu_masuk only if provided, otherwise keep existing
        if ($request->filled('waktu_masuk')) {
            $absensiPegawai->waktu_masuk = Carbon::parse($validated['waktu_masuk'])->format('H:i:s');
        } else {
            $absensiPegawai->waktu_masuk = null; // Or keep existing if that's the desired behavior
        }

        $absensiPegawai->save();

        return redirect()->route('rekap_absensi_pegawai.index')->with('success', 'Data absensi pegawai berhasil diperbarui.');
    }

    public function destroy(AbsensiPegawai $absensiPegawai)
    {
        $this->authorize('delete', $absensiPegawai);

        $absensiPegawai->delete();

        return response()->json(['success' => true, 'message' => 'Data absensi pegawai berhasil dihapus.']);
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('bulkDelete', AbsensiPegawai::class);

        $request->validate([
            'absensi_pegawai_ids' => 'required|array',
            'absensi_pegawai_ids.*' => 'exists:absensi_pegawais,id',
        ]);

        AbsensiPegawai::whereIn('id', $request->absensi_pegawai_ids)->delete();

        return response()->json(['success' => true, 'message' => 'Data absensi pegawai terpilih berhasil dihapus.']);
    }

    public function export(Request $request)
    {
        $this->authorize('export', AbsensiPegawai::class);

        return Excel::download(new RekapAbsensiPegawaiExport($request), 'rekap_absensi_pegawai_' . Carbon::now()->format('Ymd') . '.xlsx');
    }
}
