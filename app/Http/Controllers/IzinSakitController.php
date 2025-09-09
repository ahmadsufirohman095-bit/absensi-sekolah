<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIzinSakitRequest;
use App\Models\User;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Illuminate\Support\Facades\Gate; // Tambahkan ini
use App\Models\Kelas; // Tambahkan ini

class IzinSakitController extends Controller
{
    /**
     * Menampilkan form untuk mencatat izin atau sakit.
     */
    public function create()
    {
        $siswaList = User::where('role', 'siswa')
            ->with('siswaProfile.kelas')
            ->orderBy('name')
            ->get();

        return view('izinsakit.create', compact('siswaList'));
    }

    /**
     * Menyimpan data izin atau sakit baru.
     */
    /**
     * Menampilkan daftar pengajuan izin atau sakit.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage-absensi'); // Hanya admin dan guru yang bisa melihat

        $user = Auth::user();
        $query = Absensi::whereIn('status', ['izin', 'sakit'])
                        ->with(['user.siswaProfile.kelas']);

        if ($user->role === 'guru') {
            $kelasDiampu = Kelas::where('wali_kelas_id', $user->id)->first();
            if ($kelasDiampu) {
                $query->whereHas('user.siswaProfile', function ($q) use ($kelasDiampu) {
                    $q->where('kelas_id', $kelasDiampu->id);
                });
            } else {
                // Jika guru tidak mengampu kelas, tampilkan kosong
                $query->whereRaw('1 = 0'); // Return empty result
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('identifier', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status_filter') && in_array($request->status_filter, ['izin', 'sakit'])) {
            $query->where('status', $request->status_filter);
        }

        $izinsakitList = $query->latest()->paginate(10);

        return view('izinsakit.index', compact('izinsakitList'));
    }

    /**
     * Menyimpan data izin atau sakit baru.
     */
    public function store(StoreIzinSakitRequest $request)
    {
        $validated = $request->validated();
        
        $siswa = User::findOrFail($validated['user_id']);

        // Cek apakah sudah ada catatan absensi untuk siswa di tanggal ini
        $sudahAdaCatatan = Absensi::where('user_id', $siswa->id)
            ->whereDate('tanggal_absensi', $validated['tanggal'])
            ->exists();

        if ($sudahAdaCatatan) {
            return back()->with('error', 'Sudah ada catatan kehadiran untuk siswa ini di tanggal yang dipilih.')->withInput();
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_absensi')) {
            $file = $request->file('bukti_absensi');
            $fileName = 'bukti_' . $validated['status'] . '_' . $siswa->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $buktiPath = $file->storeAs('bukti', $fileName, 'public');
        }

        // Buat catatan baru di tabel absensi
        Absensi::create([
            'user_id' => $siswa->id,
            'tanggal_absensi' => $validated['tanggal'],
            
            // PERUBAHAN DI SINI:
            // Secara eksplisit mengatur waktu masuk dan keluar menjadi null untuk
            // menghindari error "doesn't have a default value".
            'waktu_masuk' => null,
            'waktu_keluar' => null,
            
            'status' => $validated['status'],
            'keterangan' => $validated['keterangan'],
            
        ]);

        return redirect()->route('rekap_absensi.index')->with('success', 'Catatan ' . $validated['status'] . ' untuk siswa ' . $siswa->name . ' berhasil disimpan.');
    }

    /**
     * Menyetujui pengajuan izin/sakit.
     *
     * @param  \App\Models\Absensi  $absensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Absensi $absensi)
    {
        Gate::authorize('manage-absensi');

        // Pastikan ini adalah catatan izin/sakit
        if (!in_array($absensi->status, ['izin', 'sakit'])) {
            return back()->with('error', 'Catatan absensi ini bukan pengajuan izin atau sakit.');
        }

        // Perbarui status menjadi "disetujui" atau "hadir" jika diperlukan
        // Untuk kasus ini, kita asumsikan status 'izin' atau 'sakit' sudah final setelah disetujui.
        // Jika ada kebutuhan untuk mengubahnya menjadi 'hadir' setelah disetujui, logika perlu disesuaikan.
        // Misalnya, jika izin/sakit dianggap sebagai kehadiran yang sah.
        // Untuk saat ini, kita hanya menandainya sebagai "disetujui" secara implisit.

        // Tambahkan keterangan bahwa sudah disetujui oleh guru/admin
        $absensi->keterangan = ($absensi->keterangan ? $absensi->keterangan . ' - ' : '') . 'Disetujui oleh ' . Auth::user()->name;
        $absensi->save();

        return back()->with('success', 'Pengajuan ' . ucfirst($absensi->status) . ' untuk ' . $absensi->user->name . ' berhasil disetujui.');
    }

    /**
     * Menolak pengajuan izin/sakit.
     *
     * @param  \App\Models\Absensi  $absensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Absensi $absensi)
    {
        Gate::authorize('manage-absensi');

        // Pastikan ini adalah catatan izin/sakit
        if (!in_array($absensi->status, ['izin', 'sakit'])) {
            return back()->with('error', 'Catatan absensi ini bukan pengajuan izin atau sakit.');
        }

        // Ubah status menjadi 'alpha' atau 'ditolak'
        $absensi->status = 'alpha'; // Atau status lain yang menandakan ditolak
        $absensi->keterangan = ($absensi->keterangan ? $absensi->keterangan . ' - ' : '') . 'Ditolak oleh ' . Auth::user()->name;
        $absensi->save();

        return back()->with('success', 'Pengajuan ' . ucfirst($absensi->status) . ' untuk ' . $absensi->user->name . ' berhasil ditolak dan status diubah menjadi Alpha.');
    }
}
