<?php

namespace App\Exports;

use App\Models\JadwalAbsensiPegawai;
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

class JadwalAbsensiPegawaiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithTitle
{
    protected $jadwalAbsensiPegawai;

    public function __construct(Collection $jadwalAbsensiPegawai)
    {
        $this->jadwalAbsensiPegawai = $jadwalAbsensiPegawai;
    }

    public function collection()
    {
        return $this->jadwalAbsensiPegawai;
    }

    public function headings(): array
    {
        return [
            'user_id',
            'Nama Pegawai',
            'Hari',
            'Jam Mulai',
            'Jam Selesai',
            'Keterangan',
            'Role',
        ];
    }

    public function map($jadwal): array
    {
        return [
            $jadwal->user_id,
            $jadwal->user->name, // Menambahkan nama pegawai
            $jadwal->hari,
            Carbon::parse($jadwal->jam_mulai)->format('H:i'),
            Carbon::parse($jadwal->jam_selesai)->format('H:i'),
            $jadwal->keterangan,
            $jadwal->user->role,
        ];
    }

    public function title(): string
    {
        return 'Jadwal Absensi Pegawai';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set the header style
                $sheet->getStyle('A1:G1')->applyFromArray([ // Mengubah rentang kolom menjadi A1:G1
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

                // Get the highest row and column (sekarang G setelah penambahan kolom Role)
                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'G'; // Kolom terakhir adalah G

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
                $sheet->getStyle('C2:F' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Rentang kolom untuk alignment (Nama Pegawai di B, Role di G)

                // Freeze the header row
                $sheet->freezePane('A2');

                // Add auto filter to the header row
                $sheet->setAutoFilter('A1:G1'); // Mengubah rentang kolom menjadi A1:G1
            },
        ];
    }
}
