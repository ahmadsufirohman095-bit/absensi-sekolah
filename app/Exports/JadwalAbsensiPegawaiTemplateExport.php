<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class JadwalAbsensiPegawaiTemplateExport implements WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    public function headings(): array
    {
        return [
            'user_id',
            'hari',
            'jam_mulai',
            'jam_selesai',
            'keterangan',
        ];
    }

    public function title(): string
    {
        return 'Template Jadwal Absensi Pegawai';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set the header style
                $sheet->getStyle('A1:E1')->applyFromArray([
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

                // Add borders to all cells
                $sheet->getStyle('A1:E1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Set example data for guidance
                $sheet->setCellValue('A2', '1');
                $sheet->setCellValue('B2', 'Senin');
                $sheet->setCellValue('C2', '08:00');
                $sheet->setCellValue('D2', '10:00');
                $sheet->setCellValue('E2', 'Rapat Pagi');

                $sheet->getStyle('A2:E2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Add data validation for 'hari' column
                $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                $hariValidation = implode(',', $hariOptions);
                for ($i = 2; $i <= 1000; $i++) { // Apply to first 1000 rows
                    $validation = $sheet->getCell('B' . $i)->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input Tidak Valid');
                    $validation->setError('Pilih dari daftar drop-down.');
                    $validation->setPromptTitle('Pilih Hari');
                    $validation->setPrompt('Silakan pilih salah satu hari dari daftar.');
                    $validation->setFormula1('"' . $hariValidation . '"');
                }
            },
        ];
    }
}
