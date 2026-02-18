<?php
namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalAbsensiExport;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MataPelajaran;
use App\Models\Setting;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::with(['waliKelas' => function ($query) {
                $query->withTrashed();
            }, 'siswaProfiles.user'])
                      ->withCount(['siswaProfiles' => function ($query) {
                          $query->whereHas('user', function ($q) {
                              $q->whereNull('deleted_at');
                          });
                      }])
                      ->latest('created_at');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_kelas', 'like', '%' . $request->search . '%');
        }

        // Filter by Wali Kelas
        if ($request->has('wali_kelas_id') && $request->wali_kelas_id != '') {
            $query->where('wali_kelas_id', $request->wali_kelas_id);
        }

        $kelas = $query->paginate(10); // Paginate with 10 items per page

        $gurus = $this->getGurus(); // Get all gurus for the filter dropdown

        return view('kelas.index', compact('kelas', 'gurus'));
    }

    private function getGurus()
    {
        return User::where('role', 'guru')->orderBy('name')->get();
    }

    public function create()
    {
        $gurus = $this->getGurus();
        return view('kelas.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'wali_kelas_id' => 'nullable|exists:users,id|unique:kelas,wali_kelas_id',
        ], [
            'wali_kelas_id.unique' => 'Guru ini sudah menjadi wali kelas di kelas lain.',
        ]);

        Kelas::create($validated);
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kela)
    {
        $kela->load(['waliKelas' => function ($query) {
                $query->withTrashed();
            }, 'siswaProfiles.user', 'jadwalAbsensis.mataPelajaran', 'jadwalAbsensis.guru' => function ($query) {
                $query->withTrashed();
            }]);

        // Filter siswaProfiles untuk hanya menyertakan user yang tidak soft deleted
        $kela->siswaProfiles = $kela->siswaProfiles->filter(function ($siswaProfile) {
            return $siswaProfile->user && !$siswaProfile->user->trashed();
        });

        $jadwalPelajaran = $kela->jadwalAbsensis->groupBy('hari')->sortKeysUsing(function ($a, $b) {
            $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
            return ($days[$a] ?? 99) <=> ($days[$b] ?? 99);
        });

        return view('kelas.show', [
            'kelas' => $kela,
            'siswa' => $kela->siswaProfiles,
            'jadwalPelajaran' => $jadwalPelajaran,
        ]);
    }

    public function printSchedule(Kelas $kela)
    {
        $kela->load(['jadwalAbsensis.mataPelajaran', 'jadwalAbsensis.guru']);

        // Pass the flat collection of schedules, not the grouped one.
        $jadwalPelajaran = $kela->jadwalAbsensis;

        $fileName = 'Jadwal_Kelas_' . str_replace(' ', '_', $kela->nama_kelas) . '_' . date('Ymd_His') . '.xlsx';

        // The constructor only takes one argument.
        return Excel::download(new JadwalAbsensiExport($jadwalPelajaran), $fileName);
    }

    public function edit(Kelas $kela)
    {
        $gurus = $this->getGurus();

        // Ambil daftar siswa yang saat ini ada di kelas ini
        $siswaDiKelas = User::whereHas('siswaProfile', function ($query) use ($kela) {
            $query->where('kelas_id', $kela->id);
        })->orderBy('name')->get();

        // Ambil daftar siswa yang belum punya kelas (calon untuk ditambahkan)
        $siswaTanpaKelas = User::where('role', 'siswa')
                               ->whereDoesntHave('siswaProfile.kelas')
                               ->orderBy('name')
                               ->get();

        // --- LOGIKA BARU: Ambil data mata pelajaran ---
        $kela->load('mataPelajarans'); // Muat mapel yang sudah ada di kelas ini
        $allMataPelajarans = MataPelajaran::orderBy('nama_mapel')->get();

        // --- LOGIKA BARU: Ambil dan kelompokkan jadwal pelajaran ---
        $jadwalPelajaran = \App\Models\JadwalAbsensi::where('kelas_id', $kela->id)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

                $allJadwalByHari = $jadwalPelajaran->groupBy('hari')->sortKeysUsing(function ($a, $b) {
            $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
            return ($days[$a] ?? 99) <=> ($days[$b] ?? 99);
        });

        return view('kelas.edit', compact('kela', 'gurus', 'siswaDiKelas', 'siswaTanpaKelas', 'allMataPelajarans', 'allJadwalByHari'));
    }

    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255', Rule::unique('kelas')->ignore($kela->id)],
            'wali_kelas_id' => ['nullable', 'exists:users,id', Rule::unique('kelas', 'wali_kelas_id')->ignore($kela->id)],
            'add_siswa_ids' => 'nullable|json',
            'remove_siswa_ids' => 'nullable|json',
            'remove_jadwal_ids' => 'nullable|json', // NEW VALIDATION
        ], [
            'wali_kelas_id.unique' => 'Guru ini sudah menjadi wali kelas di kelas lain.',
        ]);

        // Update data dasar kelas
        $kela->update($request->only('nama_kelas', 'wali_kelas_id'));

        // Proses siswa yang akan ditambahkan
        $addSiswaIds = json_decode($request->input('add_siswa_ids', '[]'), true);
        if (!empty($addSiswaIds)) {
            User::whereIn('id', $addSiswaIds)->each(function ($user) use ($kela) {
                $user->siswaProfile()->update(['kelas_id' => $kela->id]);
            });
        }

        // Proses siswa yang akan dikeluarkan
        $removeSiswaIds = json_decode($request->input('remove_siswa_ids', '[]'), true);
        if (!empty($removeSiswaIds)) {
            User::whereIn('id', $removeSiswaIds)->each(function ($user) {
                $user->siswaProfile()->update(['kelas_id' => null]);
            });
        }

        // NEW: Proses jadwal yang akan dihapus
        $removeJadwalIds = json_decode($request->input('remove_jadwal_ids', '[]'), true);
        if (!empty($removeJadwalIds)) {
            // Ensure only schedules belonging to this class are deleted
            \App\Models\JadwalAbsensi::whereIn('id', $removeJadwalIds)
                ->where('kelas_id', $kela->id) // Security check: prevent deleting schedules from other classes
                ->delete();
        }

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        // Cek relasi siswa aktif.
        $kela->loadCount(['siswaProfiles' => function ($query) {
            $query->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            });
        }]);

        if ($kela->siswa_profiles_count > 0) {
            return redirect()->route('kelas.index')
                ->with('error', 'Gagal menghapus! Kelas masih memiliki ' . $kela->siswa_profiles_count . ' siswa aktif. Harap kosongkan kelas terlebih dahulu.');
        }

        // Jika tidak ada siswa, baru hapus kelas.
        $kela->delete();

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
