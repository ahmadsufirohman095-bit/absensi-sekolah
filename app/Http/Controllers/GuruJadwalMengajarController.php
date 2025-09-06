<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalAbsensi;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GuruJadwalMengajarExport;

class GuruJadwalMengajarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guruProfile = \App\Models\GuruProfile::where('user_id', $user->id)->first();

        if ($guruProfile) {
            $jadwalMengajar = JadwalAbsensi::where('guru_id', $guruProfile->id)
                ->where('hari', now()->translatedFormat('l'))
                ->with(['mataPelajaran', 'kelas'])
                ->get();
        } else {
            $jadwalMengajar = collect();
        }

        return view('guru.jadwal_mengajar.index', compact('jadwalMengajar'));
    }

    // Tambahkan method ini
    public function exportExcel()
    {
        $user = Auth::user();
        $guruProfile = \App\Models\GuruProfile::where('user_id', $user->id)->first();

        if (!$guruProfile) {
            abort(403, 'Profil guru tidak ditemukan.');
        }

        $jadwalMengajar = JadwalAbsensi::where('guru_id', $guruProfile->id)
            ->with(['mataPelajaran', 'kelas'])
            ->get();

        $jadwalPelajaran = $jadwalMengajar->groupBy('hari')->sortKeysUsing(function ($a, $b) {
            $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
            return ($days[$a] ?? 99) <=> ($days[$b] ?? 99);
        });

        $fileName = 'Jadwal_Mengajar_' . str_replace(' ', '_', $user->name) . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new GuruJadwalMengajarExport($jadwalPelajaran, $user->name), $fileName);
    }
}