<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SiswaLaporanAbsensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithStyles
{
    protected $request;
    protected $user;
    protected $rowNumber = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->user = auth()->user();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Absensi::where('user_id', $this->user->id)
                        ->with(['user', 'jadwalAbsensi.kelas', 'jadwalAbsensi.mataPelajaran', 'jadwalAbsensi.guru'])
                        ->orderBy('tanggal_absensi', 'desc')
                        ->orderBy('waktu_masuk', 'desc');

        // Apply date range filters from the request
        if ($this->request->filled('start_date')) {
            $query->whereDate('tanggal_absensi', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->whereDate('tanggal_absensi', '<=', $this->request->end_date);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Waktu',
            'Siswa',
            'Kelas',
            'Mata Pelajaran',
            'Guru',
            'Status',
            'Keterangan',
            'Tipe',
        ];
    }

    public function map($absensi): array
    {
        // No need for rowNumber here as 'No' column is removed
        return [
            \Carbon\Carbon::parse($absensi->tanggal_absensi)->format('d-m-Y'),
            $absensi->waktu_masuk ? \Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') : '-',
            $absensi->user->name,
            $absensi->jadwalAbsensi->kelas->nama_kelas ?? '-',
            $absensi->jadwalAbsensi->mataPelajaran->nama_mapel ?? '-',
            $absensi->jadwalAbsensi->guru->name ?? '-',
            ucfirst($absensi->status),
            $absensi->keterangan ?? '-',
            ucfirst(str_replace('_', ' ', $absensi->attendance_type ?? 'N/A')),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:I' . ($event->sheet->getDelegate()->getHighestRow()); // Adjust to I for 9 columns
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $event->sheet->getDelegate()->freezePane('A2');
                $event->sheet->getDelegate()->setAutoFilter('A1:I1'); // Adjust to I1
            },
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Apply header style
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFD3D3D3', // Light gray
                ],
            ],
        ]);
    }
}