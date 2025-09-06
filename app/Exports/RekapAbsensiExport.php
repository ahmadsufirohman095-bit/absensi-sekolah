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
use \PhpOffice\PhpSpreadsheet\Style\Border;
use \PhpOffice\PhpSpreadsheet\Style\Fill;
use \PhpOffice\PhpSpreadsheet\Style\Color;

class RekapAbsensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithStyles
{
    protected $request;
    protected $rowNumber = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Absensi::with([
            'user',
            'jadwalAbsensi.kelas',
            'jadwalAbsensi.mataPelajaran',
            'jadwalAbsensi.guru'
        ]);

        // Apply filters from the request
        if ($this->request->filled('start_date')) {
            $query->whereDate('tanggal_absensi', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->whereDate('tanggal_absensi', '<=', $this->request->end_date);
        }
        if ($this->request->filled('kelas_id')) {
            $query->whereHas('jadwalAbsensi.kelas', function ($q) {
                $q->where('id', $this->request->kelas_id);
            });
        }
        if ($this->request->filled('mata_pelajaran_id')) {
            $query->whereHas('jadwalAbsensi.mataPelajaran', function ($q) {
                $q->where('id', $this->request->mata_pelajaran_id);
            });
        }
        if ($this->request->filled('guru_id')) {
            $query->whereHas('jadwalAbsensi.guru', function ($q) {
                $q->where('id', $this->request->guru_id);
            });
        }
        if ($this->request->filled('user_id')) {
            $query->where('user_id', $this->request->user_id);
        }
        if ($this->request->filled('attendance_type')) {
            $query->where('attendance_type', $this->request->attendance_type);
        }
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        return $query->orderBy('tanggal_absensi', 'desc')
                      ->orderBy('waktu_masuk', 'desc')
                      ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Absensi',
            'Waktu Masuk',
            'Nama Siswa',
            'Kelas',
            'Mata Pelajaran',
            'Guru Pengampu',
            'Status',
            'Keterangan',
            'Tipe Absensi',
        ];
    }

    public function map($absensi): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $absensi->tanggal_absensi->format('d-m-Y'),
            $absensi->waktu_masuk ? $absensi->waktu_masuk->format('H:i') : '-',
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
                $cellRange = 'A1:J' . ($this->rowNumber + 1);
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $event->sheet->getDelegate()->freezePane('A2');
                $event->sheet->getDelegate()->setAutoFilter('A1:J1');
            },
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $styles = [
            'hadir' => ['bgColor' => '#0E9F6E', 'fontColor' => '#FFFFFF'], // Green 500
            'terlambat' => ['bgColor' => '#C27803', 'fontColor' => '#FFFFFF'], // Yellow 500
            'sakit' => ['bgColor' => '#F97316', 'fontColor' => '#FFFFFF'], // Orange 500
            'izin' => ['bgColor' => '#3F83F8', 'fontColor' => '#FFFFFF'], // Blue 500
            'alpha' => ['bgColor' => '#F05252', 'fontColor' => '#FFFFFF'], // Red 500
        ];

        // Apply header style
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFE0E0E0', // Light gray
                ],
            ],
        ]);

        // Apply row styles based on status
        foreach ($sheet->getRowIterator() as $row) {
            $rowIndex = $row->getRowIndex();
            if ($rowIndex === 1) { // Skip header row
                continue;
            }

            $statusCell = $sheet->getCell('H' . $rowIndex); // Assuming Status is in column H
            $status = strtolower($statusCell->getValue());

            if (isset($styles[$status])) {
                $sheet->getStyle('H' . $rowIndex)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => str_replace('#', '', $styles[$status]['bgColor']),
                        ],
                    ],
                    'font' => [
                        'color' => [
                            'argb' => str_replace('#', '', $styles[$status]['fontColor']),
                        ],
                    ],
                ]);
            }
        }
    }
}