<?php

namespace App\Http\Controllers;

use App\Models\JadwalAbsensi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalAbsensiExport;
use App\Exports\JadwalAbsensiTemplateExport;
use App\Imports\JadwalAbsensiImport;
use App\Jobs\ExportJadwalAbsensiJob; // Import the job
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class JadwalAbsensiController extends Controller
{
    use AuthorizesRequests; // Dan tambahkan ini
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', JadwalAbsensi::class);

        $query = JadwalAbsensi::query()->with(['kelas', 'mataPelajaran', 'guru']);

        $kelasId = $request->query('kelas_id');
        $kelasName = null;

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
            $kelas = Kelas::find($kelasId);
            if ($kelas) {
                $kelasName = $kelas->nama_kelas;
            }
        }

        if ($request->filled('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
        }

        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('kelas', function ($qr) use ($search) {
                    $qr->where('nama_kelas', 'like', '%' . $search . '%');
                })->orWhereHas('mataPelajaran', function ($qr) use ($search) {
                    $qr->where('nama_mapel', 'like', '%' . $search . '%');
                })->orWhereHas('guru', function ($qr) use ($search) {
                    $qr->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $allJadwal = $query->orderBy('jam_mulai')->orderBy('jam_selesai')->get();

        // Get unique time slots and sort them
        $timeSlots = $allJadwal->map(function ($jadwal) {
            return [
                'jam_mulai' => $jadwal->jam_mulai->format('H:i'), // Format here
                'jam_selesai' => $jadwal->jam_selesai->format('H:i'), // Format here
            ];
        })->unique()->sortBy('jam_mulai')->values();

        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Fetch data for filters
        $allKelas = Kelas::orderBy('nama_kelas')->get();
        $allMataPelajaran = MataPelajaran::orderBy('nama_mapel')->get();
        $allGurus = \App\Models\User::where('role', 'guru')->orderBy('name')->get();

        return view('jadwal.index', [
            'timeSlots' => $timeSlots,
            'hariOrder' => $hariOrder,
            'kelasId' => $kelasId,
            'kelasName' => $kelasName,
            'allKelas' => $allKelas,
            'allMataPelajaran' => $allMataPelajaran,
            'allGurus' => $allGurus,
            'hariOptions' => $hariOrder,
            'currentFilters' => [
                'kelas_id' => $request->query('kelas_id'),
                'mata_pelajaran_id' => $request->query('mata_pelajaran_id'),
                'guru_id' => $request->query('guru_id'),
                'hari' => $request->query('hari'),
            ],
            'allJadwal' => $allJadwal // Pass all jadwal for Alpine.js filtering
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', JadwalAbsensi::class);

        $kelas = Kelas::all();
        $mataPelajaran = MataPelajaran::all();
        $gurus = \App\Models\User::where('role', 'guru')->orderBy('name')->get();
        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return view('jadwal.create', compact('kelas', 'mataPelajaran', 'gurus', 'hariOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', JadwalAbsensi::class);

        $validatedData = $request->validate([
            'jadwal' => 'required|array',
            'jadwal.*.kelas_id' => 'required|array',
            'jadwal.*.kelas_id.*' => 'required|exists:kelas,id',
            'jadwal.*.mata_pelajaran_id' => 'required|array',
            'jadwal.*.mata_pelajaran_id.*' => 'required|exists:mata_pelajarans,id',
            'jadwal.*.guru_id' => 'required|exists:users,id', // Validasi guru_id
            'jadwal.*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jadwal.*.jam_mulai' => 'required|date_format:H:i',
            'jadwal.*.jam_selesai' => [
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
            'jadwal.*.tanggal' => 'nullable|date', // Tambahkan validasi untuk tanggal
        ], [
            'jadwal.*.jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'jadwal.*.guru_id.required' => 'Guru pengampu wajib dipilih.',
        ]);

        Log::info('Validated Jadwal Data:', $validatedData); // Tambahkan logging untuk data yang divalidasi

        try {
            DB::transaction(function () use ($validatedData) {
                foreach ($validatedData['jadwal'] as $jadwalData) {
                    $hari = $jadwalData['hari'];
                    $jamMulai = $jadwalData['jam_mulai'];
                    $jamSelesai = $jadwalData['jam_selesai'];
                    $guruId = $jadwalData['guru_id']; // Ambil guru_id dari data

                    foreach ($jadwalData['kelas_id'] as $kelasId) {
                        foreach ($jadwalData['mata_pelajaran_id'] as $mapelId) {
                            JadwalAbsensi::create([
                                'kelas_id' => $kelasId,
                                'mata_pelajaran_id' => $mapelId,
                                'guru_id' => $guruId, // Gunakan guru_id yang dipilih
                                'hari' => $hari,
                                'tanggal' => $jadwalData['tanggal'] ?? null, // Gunakan tanggal jika ada, jika tidak null
                                'jam_mulai' => $jamMulai,
                                'jam_selesai' => $jamSelesai,
                            ]);
                        }
                    }
                }
            });
            Log::info('Jadwal Absensi berhasil disimpan.'); // Tambahkan logging sukses
        } catch (\Throwable $e) {
            Log::error('Error storing schedule: ' . $e->getMessage(), ['exception' => $e]); // Perbarui logging error
            return back()
                ->with('error', 'Terjadi kesalahan server saat menyimpan jadwal. Silakan coba lagi.')
                ->withInput();
        }

        // Check if there's a kelas_id in the first schedule item to redirect back
        $firstKelasId = $validatedData['jadwal'][0]['kelas_id'][0] ?? null;

        if ($firstKelasId) {
            // Redirect to the general schedule index
            return redirect()->route('jadwal.index')
                         ->with('success', 'Jadwal absensi berhasil ditambahkan.');
        }

        // Fallback redirect to the general schedule index
        return redirect()->route('jadwal.index')->with('success', 'Jadwal absensi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JadwalAbsensi $jadwalAbsensi)
    {
        // Tidak ada tampilan show yang spesifik, mungkin bisa diarahkan ke edit atau index
        return redirect()->route('jadwal.edit', $jadwalAbsensi);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalAbsensi $jadwal)
    {
        $this->authorize('update', $jadwal);

        $kelas = Kelas::all();
        $mataPelajaran = MataPelajaran::all();
        $gurus = \App\Models\User::where('role', 'guru')->orderBy('name')->get();
        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return view('jadwal.edit', compact('jadwal', 'kelas', 'mataPelajaran', 'gurus', 'hariOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalAbsensi $jadwal)
    {
        $this->authorize('update', $jadwal);

        $validatedData = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'guru_id' => 'required|exists:users,id',
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
        ]);

        Log::info('JadwalAbsensi Update Validated Data:', $validatedData);

        try {
            $jadwal->update($validatedData);
            Log::info('JadwalAbsensi Update Success for ID: ' . $jadwal->id, $validatedData);
        } catch (\Throwable $e) {
            Log::error('Error updating schedule: ' . $e->getMessage());
            return back()
                ->with('error', 'Terjadi kesalahan server saat memperbarui jadwal. Silakan coba lagi.')
                ->withInput();
        }

        return redirect()->route('jadwal.index', ['kelas_id' => $jadwal->kelas_id])->with('success', 'Jadwal absensi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalAbsensi $jadwal)
    {
        $this->authorize('delete', $jadwal);

        try {
            $jadwal->delete();
        } catch (\Throwable $e) {
            Log::error('Error deleting schedule: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan server saat menghapus jadwal. Silakan coba lagi.');
        }

        return back()->with('success', 'Jadwal absensi berhasil dihapus.');
    }

    /**
     * Show the form for creating attendance for a specific schedule.
     */
    public function showAttendanceSheet(JadwalAbsensi $jadwal)
    {
        $this->authorize('update', $jadwal); // Teachers can only create attendance for their own schedule

        $today = Carbon::today();
        $students = \App\Models\User::where('role', 'siswa')
                        ->whereHas('siswaProfile', fn($q) => $q->where('kelas_id', $jadwal->kelas_id))
                        ->orderBy('name')
                        ->get();

        // Eager load existing attendance for today to avoid N+1 queries
        $existingAbsensi = Absensi::where('jadwal_absensi_id', $jadwal->id)
                                  ->whereDate('tanggal_absensi', $today)
                                  ->get()
                                  ->keyBy('user_id');

        $students->each(function ($student) use ($existingAbsensi) {
            $absensi = $existingAbsensi->get($student->id);
            $student->status_hari_ini = $absensi ? $absensi->status : 'hadir'; // Default to 'hadir'
            $student->waktu_absensi = $absensi ? $absensi->waktu_masuk : null;
        });

        return view('jadwal.absensi-sheet', compact('jadwal', 'students'));
    }

    /**
     * Store attendance for a specific schedule.
     */
    public function storeAttendanceSheet(Request $request, JadwalAbsensi $jadwal)
    {
        $this->authorize('update', $jadwal);

        $validated = $request->validate([
            'absensi' => 'required|array',
            'absensi.*.status' => 'required|string|in:hadir,sakit,izin,alpha',
            'absensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        $today = Carbon::today();

        DB::transaction(function () use ($validated, $jadwal, $today) {
            foreach ($validated['absensi'] as $studentId => $data) {
                Absensi::updateOrCreate(
                    [
                        'user_id' => $studentId,
                        'jadwal_absensi_id' => $jadwal->id,
                        'tanggal_absensi' => $today,
                    ],
                    [
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'],
                        'waktu_masuk' => ($data['status'] === 'hadir') ? now() : null,
                    ]
                );
            }
        });

        return redirect()->route('dashboard')->with('success', 'Absensi untuk kelas ' . $jadwal->kelas->nama_kelas . ' berhasil disimpan.');
    }

    /**
     * Remove multiple specified resources from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'jadwal_ids' => 'required|array',
            'jadwal_ids.*' => 'exists:jadwal_absensis,id', // Validate each ID in the array
        ]);

        $jadwalIds = $validated['jadwal_ids'];

        try {
            DB::transaction(function () use ($jadwalIds) {
                foreach ($jadwalIds as $id) {
                    $jadwal = JadwalAbsensi::findOrFail($id);
                    $this->authorize('delete', $jadwal);
                    $jadwal->delete();
                }
            });
            return response()->json(['success' => true, 'message' => 'Jadwal terpilih berhasil dihapus.']);
        } catch (\Throwable $e) {
            Log::error('Error deleting schedules in bulk: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus jadwal. Silakan coba lagi.'], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('viewAny', JadwalAbsensi::class);

        $query = JadwalAbsensi::query()->with(['kelas', 'mataPelajaran', 'guru']);

        $fileNameParts = ['jadwal-pelajaran'];

        if ($request->filled('kelas_id')) {
            $kelasId = $request->kelas_id;
            $query->where('kelas_id', $kelasId);
            $kelas = Kelas::find($kelasId);
            if ($kelas) {
                $fileNameParts[] = 'kelas-' . str_replace(' ', '-', $kelas->nama_kelas);
            }
        }

        if ($request->filled('hari')) {
            $hari = $request->hari;
            $query->where('hari', $hari);
            $fileNameParts[] = 'hari-' . $hari;
        }
        
        if ($request->filled('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
        }

        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('kelas', function ($qr) use ($search) {
                    $qr->where('nama_kelas', 'like', '%' . $search . '%');
                })->orWhereHas('mataPelajaran', function ($qr) use ($search) {
                    $qr->where('nama_mapel', 'like', '%' . $search . '%');
                })->orWhereHas('guru', function ($qr) use ($search) {
                    $qr->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $jadwal = $query->get();
        
        $fileNameParts[] = Carbon::now()->format('Y-m-d');
        $fileName = implode('-', $fileNameParts) . '.xlsx';

        return Excel::download(new JadwalAbsensiExport($jadwal), $fileName);
    }

    public function importExcel(Request $request)
    {
        $this->authorize('create', JadwalAbsensi::class);

        $request->validate([
            'import_file' => 'required|file|mimes:xlsx'
        ]);

        $import = new JadwalAbsensiImport;

        try {
            Excel::import($import, $request->file('import_file'));
            
            $failures = $import->failures();

            if ($failures->isNotEmpty()) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . $failure->values()[$failure->attribute()] . ')';
                }
                return back()->with('error', 'Impor selesai, tetapi beberapa baris gagal diimpor. Silakan periksa kesalahan berikut:')->with('validation_errors', $errorMessages);
            }

            return back()->with('success', 'Jadwal berhasil diimpor.');

        } catch (\Exception $e) {
            Log::error('Error importing schedule: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Terjadi kesalahan tak terduga saat mengimpor file. Pesan: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $this->authorize('viewAny', JadwalAbsensi::class);
        return Excel::download(new JadwalAbsensiTemplateExport(), 'template_impor_jadwal.xlsx');
    }
}
