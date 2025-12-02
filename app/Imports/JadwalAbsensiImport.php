<?php

namespace App\Imports;

use App\Models\JadwalAbsensi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class JadwalAbsensiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;

    private $isSiswaSchedule;
    private $kelasMap;
    private $mapelMap;
    private $userMap; // Mengganti guruMap menjadi userMap untuk menampung semua role yang mungkin

    public function __construct(bool $isSiswaSchedule = true)
    {
        $this->isSiswaSchedule = $isSiswaSchedule;

        // Cache necessary data to avoid querying in a loop
        if ($this->isSiswaSchedule) {
            $this->kelasMap = Kelas::all()->keyBy('nama_kelas');
            $this->mapelMap = MataPelajaran::all()->keyBy('kode_mapel');
            $this->userMap = User::where('role', 'guru')->get()->keyBy('identifier'); // Hanya guru untuk jadwal siswa
        } else {
            // Untuk jadwal non-siswa, kelas_id null, mata pelajaran bisa null, guru_id bisa dari berbagai role
            $this->kelasMap = collect(); // Tidak perlu kelas map
            $this->mapelMap = MataPelajaran::all()->keyBy('kode_mapel');
            $this->userMap = User::whereIn('role', ['admin', 'guru', 'tu', 'other'])->get()->keyBy('identifier');
        }
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $kelasId = null;
        if ($this->isSiswaSchedule) {
            $kelas = $this->kelasMap->get($row['kelas']);
            $kelasId = $kelas->id ?? null; // Should be valid due to validation rules
        }

        $mapelId = null;
        if (isset($row['kode_mapel']) && $row['kode_mapel']) {
            $mapel = $this->mapelMap->get($row['kode_mapel']);
            $mapelId = $mapel->id ?? null;
        }
        
        $user = $this->userMap->get($row['identifier_penanggung_jawab']); // Menggunakan identifier_penanggung_jawab
        $userId = $user->id ?? null; // Should be valid due to validation rules

        return new JadwalAbsensi([
            'hari'              => $row['hari'],
            'jam_mulai'         => $row['jam_mulai'],
            'jam_selesai'       => $row['jam_selesai'],
            'kelas_id'          => $kelasId, // Nullable for non-siswa
            'mata_pelajaran_id' => $mapelId, // Nullable for non-siswa
            'guru_id'           => $userId, // Bisa jadi ID admin/tu/other juga
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ];

        if ($this->isSiswaSchedule) {
            $rules['kelas'] = 'required|exists:kelas,nama_kelas';
            $rules['kode_mapel'] = 'required|exists:mata_pelajarans,kode_mapel';
            $rules['identifier_penanggung_jawab'] = ['required', 'exists:users,identifier,role,guru'];
        } else {
            $rules['kelas'] = 'nullable'; // Tidak diperlukan untuk non-siswa
            $rules['kode_mapel'] = 'nullable|exists:mata_pelajarans,kode_mapel'; // Bisa null untuk jadwal umum
            $rules['identifier_penanggung_jawab'] = ['required', 'exists:users,identifier']; // Bisa admin, guru, tu, other
        }
        return $rules;
    }

    public function customValidationMessages()
    {
        $messages = [
            'kelas.exists' => 'Nama Kelas (:input) tidak ditemukan di database.',
            'kode_mapel.exists' => 'Kode Mata Pelajaran (:input) tidak ditemukan di database.',
            'identifier_penanggung_jawab.exists' => 'Identifier Penanggung Jawab (:input) tidak ditemukan.',
        ];

        if ($this->isSiswaSchedule) {
            $messages['identifier_penanggung_jawab.exists'] = 'NIP Guru (:input) tidak ditemukan, atau pengguna dengan NIP tersebut bukan seorang guru.';
        }
        return $messages;
    }
}
