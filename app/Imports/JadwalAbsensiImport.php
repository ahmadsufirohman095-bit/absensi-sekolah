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

    private $kelasMap;
    private $mapelMap;
    private $guruMap;

    public function __construct()
    {
        // Cache necessary data to avoid querying in a loop
        $this->kelasMap = Kelas::all()->keyBy('nama_kelas');
        $this->mapelMap = MataPelajaran::all()->keyBy('kode_mapel');
        $this->guruMap = User::where('role', 'guru')->get()->keyBy('identifier'); // Key by NIP (identifier)
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $kelas = $this->kelasMap->get($row['kelas']);
        $mapel = $this->mapelMap->get($row['kode_mapel']);
        $guru = $this->guruMap->get($row['nip']);

        // Validation handles non-existent relations. 
        // This method is only called for rows that pass validation.
        return new JadwalAbsensi([
            'hari'              => $row['hari'],
            'jam_mulai'         => $row['jam_mulai'],
            'jam_selesai'       => $row['jam_selesai'],
            'kelas_id'          => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id'           => $guru->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            
            'kelas' => 'required|exists:kelas,nama_kelas',
            'kode_mapel' => 'required|exists:mata_pelajarans,kode_mapel',
            'nip' => ['required', 'exists:users,identifier,role,guru'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kelas.exists' => 'Nama Kelas (:input) tidak ditemukan di database.',
            'kode_mapel.exists' => 'Kode Mata Pelajaran (:input) tidak ditemukan di database.',
            'nip.exists' => 'NIP Guru (:input) tidak ditemukan, atau pengguna dengan NIP tersebut bukan seorang guru.',
        ];
    }
}
