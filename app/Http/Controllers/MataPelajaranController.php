<?php
namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataPelajaranController extends Controller
{
    private function getGurus()
    {
        return User::where('role', 'guru')->orderBy('name')->get();
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'nama_mapel'); // Default sort by nama_mapel
        $sortDirection = $request->input('sort_direction', 'asc'); // Default sort direction asc

        // Validate sortable columns to prevent SQL injection
        $sortableColumns = ['kode_mapel', 'nama_mapel', 'gurus_count', 'kelas_count'];
        if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = 'nama_mapel'; // Fallback to default
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc'; // Fallback to default
        }

        $mapelQuery = MataPelajaran::with('gurus')->withCount(['kelas']);

        if ($search) {
            $mapelQuery->where(function ($query) use ($search) {
                $query->where('nama_mapel', 'like', "%{$search}%")
                      ->orWhere('kode_mapel', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $mapel = $mapelQuery->orderBy($sortBy, $sortDirection)->paginate(20)->withQueryString();

        return view('mapel.index', compact('mapel', 'search', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $mataPelajaran = new MataPelajaran();
        $allGurus = $this->getGurus(); // Ambil daftar guru
        $allKelas = \App\Models\Kelas::orderBy('nama_kelas')->get(); // Ambil daftar kelas
        return view('mapel.create', compact('mataPelajaran', 'allGurus', 'allKelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:50|unique:mata_pelajarans,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'guru_id' => 'nullable|exists:users,id',
        ]);

        $mataPelajaran = MataPelajaran::create($validated);
        
        // Sync the single teacher
        $guruId = $request->input('guru_id');
        $mataPelajaran->gurus()->sync($guruId ? [$guruId] : []);

        

        return redirect()->route('mata-pelajaran.index')->with('success', 'Mata Pelajaran baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->load('gurus', 'kelas');
        return view('mapel.show', compact('mataPelajaran'));
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        // Ambil semua guru untuk pilihan
        $allGurus = $this->getGurus();
        // Ambil semua kelas untuk pilihan
        $allKelas = \App\Models\Kelas::orderBy('nama_kelas')->get();
        // Muat guru dan kelas yang saat ini mengajar/diajarkan mapel ini
        $mataPelajaran->load('gurus', 'kelas');
        
        return view('mapel.edit', compact('mataPelajaran', 'allGurus', 'allKelas'));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'kode_mapel' => ['required','string','max:50',Rule::unique('mata_pelajarans')->ignore($mataPelajaran->id)],
            'nama_mapel' => ['required','string','max:255'],
            'deskripsi' => 'nullable|string',
            'guru_id' => 'nullable|exists:users,id',
        ]);

        $mataPelajaran->update($request->only(['kode_mapel', 'nama_mapel', 'deskripsi']));
        
        // Sync the single teacher
        $guruId = $request->input('guru_id');
        $mataPelajaran->gurus()->sync($guruId ? [$guruId] : []);

        return redirect()->route('mata-pelajaran.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        // Eager load hitungan relasi
        $mataPelajaran->loadCount(['gurus', 'kelas']);

        if ($mataPelajaran->gurus_count > 0 || $mataPelajaran->kelas_count > 0) {
            return redirect()->route('mata-pelajaran.index')
                ->with('error', 'Gagal menghapus! Mata pelajaran ini masih digunakan oleh ' . $mataPelajaran->gurus_count . ' guru dan diajarkan di ' . $mataPelajaran->kelas_count . ' kelas.');
        }

        $mataPelajaran->delete();
        return redirect()->route('mata-pelajaran.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}
