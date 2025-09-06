<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles; // Untuk styling
use Maatwebsite\Excel\Concerns\WithColumnWidths; // Untuk lebar kolom
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font; // Untuk mengatur font
use PhpOffice\PhpSpreadsheet\Style\Border; // Untuk mengatur border
use PhpOffice\PhpSpreadsheet\Style\Fill; // Untuk mengatur warna background

class AbsensiExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $tanggal;
    protected $kelasId;

    public function __construct(string $tanggal = null, string $kelasId = null)
    {
        $this->tanggal = $tanggal;
        $this->kelasId = $kelasId;
    }

    public function getTanggal(): ?string
    {
        return $this->tanggal;
    }

    public function getKelasId(): ?string
    {
        return $this->kelasId;
    }

    public function query()
    {
        // ... (Method query tidak berubah) ...
        $query = Absensi::query()
                    ->with(['user.siswaProfile.kelas'])
                    ->join('users', 'absensis.user_id', '=', 'users.id')
                    ->where('users.role', 'siswa');

        if ($this->tanggal) {
            $query->whereDate('absensis.tanggal_absensi', $this->tanggal);
        }

        if ($this->kelasId) {
            $query->whereHas('user.siswaProfile', function ($q) {
                $q->where('kelas_id', $this->kelasId);
            });
        }

        return $query->select('absensis.*')->latest('absensis.created_at');
    }

    public function headings(): array
    {
        // ... (Method headings tidak berubah) ...
        return [
            'Nama Siswa',
            'NIS',
            'Kelas',
            'Tanggal Absensi',
            'Waktu Masuk',
            'Status',
        ];
    }

    public function map($absensi): array
    {
        // ... (Method map tidak berubah) ...
        return [
            $absensi->user->name,
            $absensi->user->identifier,
            $absensi->user->siswaProfile->kelas->nama_kelas ?? '-',
            \Carbon\Carbon::parse($absensi->tanggal_absensi)->format('d-m-Y'),
            $absensi->waktu_masuk,
            ucfirst($absensi->status),
        ];
    }

    // METHOD BARU UNTUK LEBAR KOLOM
    public function columnWidths(): array
    {
        return [
            'A' => 30, // Nama Siswa
            'B' => 20, // NIS
            'C' => 15, // Kelas
            'D' => 20, // Tanggal Absensi
            'E' => 15, // Waktu Masuk
            'F' => 15, // Status
        ];
    }

    // METHOD BARU UNTUK STYLING
    public function styles(Worksheet $sheet)
    {
        // Mengatur style untuk baris header (baris ke-1)
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Warna teks putih
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F46E5'], // Warna background Indigo
            ],
        ]);

        // Menambahkan border ke semua sel yang berisi data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD1D5DB'], // Warna border abu-abu
                ],
            ],
        ]);
    }
}
