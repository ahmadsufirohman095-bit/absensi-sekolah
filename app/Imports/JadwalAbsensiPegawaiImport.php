<?php

namespace App\Imports;

use App\Models\JadwalAbsensiPegawai;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Validation\Rule;

class JadwalAbsensiPegawaiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;

    private $userMap;

    public function __construct()
    {
        // Cache necessary data to avoid querying in a loop
        $this->userMap = User::whereIn('role', ['admin', 'guru', 'tu', 'other'])->get()->keyBy('id');
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $userId = $row['user_id'];
        
        return new JadwalAbsensiPegawai([
            'user_id'           => $userId,
            'hari'              => $row['hari'],
            'jam_mulai'         => $row['jam_mulai'],
            'jam_selesai'       => $row['jam_selesai'],
            'keterangan'        => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->whereIn('role', ['admin', 'guru', 'tu', 'other']);
                }),
            ],
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'user_id.exists' => 'ID Pegawai (:input) tidak ditemukan atau bukan merupakan Admin, Guru, TU, atau Other.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ];
    }
}
