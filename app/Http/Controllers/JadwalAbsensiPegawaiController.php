<?php

namespace App\Http\Controllers;

use App\Models\JadwalAbsensiPegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalAbsensiPegawaiExport;
use App\Exports\JadwalAbsensiPegawaiTemplateExport;
use App\Imports\JadwalAbsensiPegawaiImport;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JadwalAbsensiPegawaiController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // Kebijakan otorisasi dapat ditambahkan di sini jika diperlukan
        // $this->authorizeResource(JadwalAbsensiPegawai::class, 'jadwalAbsensiPegawai');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Hanya admin yang bisa mengakses halaman ini
        $this->authorize('isAdmin', User::class); // Asumsi Anda memiliki policy isAdmin

        $query = JadwalAbsensiPegawai::query()->with(['user']);

        $userId = $request->query('user_id');
        $userName = null;

        if ($userId) {
            $query->where('user_id', $userId);
            $user = User::find($userId);
            if ($user) {
                $userName = $user->name;
            }
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($qr) use ($search) {
                    $qr->where('name', 'like', '%' . $search . '%');
                })->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        $allJadwalAbsensiPegawai = $query->orderBy('hari')->orderBy('jam_mulai')->get();

        $timeSlots = $allJadwalAbsensiPegawai->map(function ($jadwal) {
            return [
                'jam_mulai' => Carbon::parse($jadwal->jam_mulai)->format('H:i'),
                'jam_selesai' => Carbon::parse($jadwal->jam_selesai)->format('H:i'),
            ];
        })->unique()->sortBy('jam_mulai')->values();

        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        $allUsers = User::whereIn('role', ['admin', 'guru', 'tu', 'other'])
                        ->orderBy('name')
                        ->get();

        return view('jadwal-absensi-pegawai.index', [
            'timeSlots' => $timeSlots,
            'hariOrder' => $hariOrder,
            'userId' => $userId,
            'userName' => $userName,
            'allUsers' => $allUsers,
            'hariOptions' => $hariOrder,
            'currentFilters' => [
                'user_id' => $request->query('user_id'),
                'hari' => $request->query('hari'),
            ],
            'allJadwalAbsensiPegawai' => $allJadwalAbsensiPegawai
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('isAdmin', User::class);

        $users = User::whereIn('role', ['admin', 'guru', 'tu', 'other'])
                            ->orderBy('name')->get();
        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return view('jadwal-absensi-pegawai.create', compact('users', 'hariOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('isAdmin', User::class);

        $validatedData = $request->validate([
            'jadwal_absensi_Pegawai' => 'required|array',
            'jadwal_absensi_Pegawai.*.user_id' => 'required|exists:users,id',
            'jadwal_absensi_Pegawai.*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jadwal_absensi_Pegawai.*.jam_mulai' => 'required|date_format:H:i',
            'jadwal_absensi_Pegawai.*.jam_selesai' => [
                'required',
                'date_format:H:i',
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    $jamMulaiAttribute = str_replace('jam_selesai', 'jam_mulai', $attribute);
                    $jamMulai = $request->input($jamMulaiAttribute);
                    if ($jamMulai && strtotime($value) <= strtotime($jamMulai)) {
                        $fail('Jam selesai pada baris ' . (explode('.', $attribute)[1] + 1) . ' harus setelah jam mulai.');
                    }
                },
            ],
            'jadwal_absensi_Pegawai.*.keterangan' => 'nullable|string|max:255',
        ], [
            'jadwal_absensi_Pegawai.*.jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'jadwal_absensi_Pegawai.*.user_id.required' => 'Pegawai wajib dipilih.',
        ]);

        Log::info('Validated Jadwal Absensi Pegawai Data:', $validatedData);

        try {
            DB::transaction(function () use ($validatedData) {
                foreach ($validatedData['jadwal_absensi_Pegawai'] as $jadwalData) {
                    JadwalAbsensiPegawai::create([
                        'user_id' => $jadwalData['user_id'],
                        'hari' => $jadwalData['hari'],
                        'jam_mulai' => $jadwalData['jam_mulai'],
                        'jam_selesai' => $jadwalData['jam_selesai'],
                        'keterangan' => $jadwalData['keterangan'] ?? null,
                    ]);
                }
            });
            Log::info('Jadwal Absensi Pegawai berhasil disimpan.');
        } catch (\Throwable $e) {
            Log::error('Error storing Jadwal Absensi Pegawai: ' . $e->getMessage(), ['exception' => $e]);
            return back()
                ->with('error', 'Terjadi kesalahan server saat menyimpan jadwal absensi Pegawai. Silakan coba lagi.')
                ->withInput();
        }

        return redirect()->route('jadwal-absensi-pegawai.index')->with('success', 'Jadwal absensi Pegawai berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalAbsensiPegawai $jadwalAbsensiPegawai)
    {
        $this->authorize('isAdmin', User::class); // Admin only

        $users = User::whereIn('role', ['admin', 'guru', 'tu', 'other'])
                            ->orderBy('name')->get();
        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return view('jadwal-absensi-pegawai.edit', compact('jadwalAbsensiPegawai', 'users', 'hariOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalAbsensiPegawai $jadwalAbsensiPegawai)
    {
        $this->authorize('isAdmin', User::class); // Admin only

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => [
                'required',
                'date_format:H:i',
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    if (strtotime($value) <= strtotime($request->jam_mulai)) {
                        $fail('Jam selesai harus setelah jam mulai.');
                    }
                },
            ],
            'keterangan' => 'nullable|string|max:255',
        ]);

        Log::info('Jadwal Absensi Pegawai Update Validated Data:', $validatedData);

        try {
            $jadwalAbsensiPegawai->update($validatedData);
            Log::info('Jadwal Absensi Pegawai Update Success for ID: ' . $jadwalAbsensiPegawai->id, $validatedData);
        } catch (\Throwable $e) {
            Log::error('Error updating Jadwal Absensi Pegawai: ' . $e->getMessage());
            return back()
                ->with('error', 'Terjadi kesalahan server saat memperbarui jadwal absensi Pegawai. Silakan coba lagi.')
                ->withInput();
        }

        return redirect()->route('jadwal-absensi-pegawai.index')->with('success', 'Jadwal absensi Pegawai berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalAbsensiPegawai $jadwalAbsensiPegawai)
    {
        $this->authorize('isAdmin', User::class); // Admin only

        try {
            $jadwalAbsensiPegawai->delete();
        } catch (\Throwable $e) {
            Log::error('Error deleting Jadwal Absensi Pegawai: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan server saat menghapus jadwal absensi Pegawai. Silakan coba lagi.');
        }

        return back()->with('success', 'Jadwal absensi Pegawai berhasil dihapus.');
    }

    /**
     * Remove multiple specified resources from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $this->authorize('isAdmin', User::class); // Admin only

        $validated = $request->validate([
            'jadwal_absensi_pegawai_ids' => 'required|array',
            'jadwal_absensi_pegawai_ids.*' => 'exists:jadwal_absensi_pegawais,id',
        ]);

        $jadwalIds = $validated['jadwal_absensi_pegawai_ids'];

        try {
            DB::transaction(function () use ($jadwalIds) {
                foreach ($jadwalIds as $id) {
                    $jadwal = JadwalAbsensiPegawai::findOrFail($id);
                    $jadwal->delete();
                }
            });
            return response()->json(['success' => true, 'message' => 'Jadwal absensi Pegawai terpilih berhasil dihapus.']);
        } catch (\Throwable $e) {
            Log::error('Error deleting Jadwal Absensi Pegawai in bulk: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus jadwal absensi Pegawai. Silakan coba lagi.'], 500);
        }
    }

    /**
     * Export jadwal absensi pegawai to Excel.
     */
    public function exportExcel(Request $request)
    {
        $this->authorize('isAdmin', User::class);

        $query = JadwalAbsensiPegawai::query()->with(['user']);

        $userId = $request->query('user_id');
        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }
        
        $jadwalAbsensiPegawai = $query->orderBy('hari')->orderBy('jam_mulai')->get();

        $fileName = 'jadwal_absensi_pegawai_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new JadwalAbsensiPegawaiExport($jadwalAbsensiPegawai), $fileName);
    }

    /**
     * Download template for importing jadwal absensi pegawai.
     */
    public function downloadTemplate()
    {
        $this->authorize('isAdmin', User::class);
        $fileName = 'template_jadwal_absensi_pegawai_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new JadwalAbsensiPegawaiTemplateExport(), $fileName);
    }

    /**
     * Import jadwal absensi pegawai from Excel.
     */
    public function importExcel(Request $request)
    {
        $this->authorize('isAdmin', User::class);

        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new JadwalAbsensiPegawaiImport(), $request->file('file'));
            return back()->with('success', 'Jadwal absensi pegawai berhasil diimpor.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Gagal mengimpor jadwal absensi pegawai: ' . implode('; ', $errors));
        } catch (\Exception $e) {
            Log::error('Error importing Jadwal Absensi Pegawai: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Terjadi kesalahan saat mengimpor jadwal absensi pegawai. Silakan coba lagi.');
        }
    }
}
