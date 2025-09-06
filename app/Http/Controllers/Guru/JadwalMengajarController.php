<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalAbsensi;
use App\Models\GuruProfile;

class JadwalMengajarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guruProfile = GuruProfile::where('user_id', $user->id)->first();

        if (!$guruProfile) {
            abort(403, 'Profil guru tidak ditemukan.');
        }

        $dayMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            0 => 'Minggu', // Minggu biasanya hari terakhir dalam seminggu
        ];
        $hariIni = $dayMap[now()->dayOfWeek];

        $jadwalMengajar = JadwalAbsensi::where('guru_id', $user->id)
            ->whereRaw('LOWER(hari) = ?', [mb_strtolower($hariIni)])
            ->with(['mataPelajaran', 'kelas'])
            ->get();

        return view('guru.jadwal_mengajar.index', compact('jadwalMengajar'));
        return view('guru.jadwal_mengajar.index', compact('jadwalMengajar'));
    }

    public function semua()
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

        return view('guru.jadwal_mengajar.semua', compact('jadwalPelajaran'));
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
