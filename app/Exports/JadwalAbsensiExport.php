<?php

namespace App\Exports;

use App\Models\JadwalAbsensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

class JadwalAbsensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithTitle
{
    protected $jadwalPelajaran;

    public function __construct(Collection $jadwalPelajaran)
    {
        $this->jadwalPelajaran = $jadwalPelajaran;
    }

    public function collection()
    {
        return $this->jadwalPelajaran;
    }

    public function headings(): array
    {
        return [
            'Hari',
            'Jam Mulai',
            'Jam Selesai',
            'Kode Mapel',
            'Mata Pelajaran',
            'Guru',
            'NIP',
            'Kelas',
        ];
    }

    public function map($jadwal): array
    {
        return [
            $jadwal->hari,
            Carbon::parse($jadwal->jam_mulai)->format('H:i'),
            Carbon::parse($jadwal->jam_selesai)->format('H:i'),
            $jadwal->mataPelajaran->kode_mapel,
            $jadwal->mataPelajaran->nama_mapel,
            $jadwal->guru->name,
            $jadwal->guru->identifier,
            $jadwal->kelas->nama_kelas,
        ];
    }

    public function title(): string
    {
        return 'Jadwal Pelajaran';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set the header style
                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF4F46E5'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Get the highest row and column
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Add borders to all cells
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Apply alternating row colors
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                    }
                }

                // Set alignment for columns
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B2:C' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Freeze the header row
                $sheet->freezePane('A2');

                // Add auto filter to the header row
                $sheet->setAutoFilter('A1:H1');
            },
        ];
    }
}
