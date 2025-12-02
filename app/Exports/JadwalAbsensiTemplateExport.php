<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

class JadwalAbsensiTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
{
    private $isSiswaSchedule;

    public function __construct(bool $isSiswaSchedule = true)
    {
        $this->isSiswaSchedule = $isSiswaSchedule;
    }

    public function headings(): array
    {
        $headings = [
            'Hari',
            'Jam Mulai',
            'Jam Selesai',
            'Kode Mapel', // Optional for non-siswa
            'Mata Pelajaran', // Optional for non-siswa
            'Nama Penanggung Jawab', // Common for both
            'Identifier Penanggung Jawab', // Common for both (NIP for Guru, Identifier for Admin/TU/Other)
        ];

        if ($this->isSiswaSchedule) {
            $headings[] = 'Kelas';
        }

        return $headings;
    }

    public function array(): array
    {
        if ($this->isSiswaSchedule) {
            return [
                ['Senin', '07:00', '08:00', 'MTK-01', 'Matematika', 'Nama Guru Lengkap', '198001012010011001', 'VII-A'],
                ['Selasa', '08:00', '09:00', 'IPA-01', 'Ilmu Pengetahuan Alam', 'Nama Guru Lain', '198502022012022002', 'VII-B'],
            ];
        } else {
            return [
                // Contoh untuk jadwal non-siswa (Guru, TU, Lainnya)
                ['Senin', '09:00', '10:00', 'UMUM-01', 'Rapat Staf', 'Nama Admin', 'ADM-001'],
                ['Rabu', '13:00', '14:00', null, 'Pembinaan Pegawai', 'Nama TU', 'TU-002'], // Mata pelajaran bisa null
            ];
        }
    }

    public function title(): string
    {
        return $this->isSiswaSchedule ? 'Template Impor Jadwal Siswa' : 'Template Impor Jadwal Non-Siswa';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Determine the highest column based on schedule type
                $highestColumn = $this->isSiswaSchedule ? 'H' : 'G'; // H for siswa, G for non-siswa

                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
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

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                    }
                }

                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B2:C' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:' . $highestColumn . '1');
            },
        ];
    }
}
