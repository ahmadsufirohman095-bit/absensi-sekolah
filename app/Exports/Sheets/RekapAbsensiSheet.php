<?php
namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapAbsensiSheet implements WithTitle, WithEvents
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Ringkasan Laporan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                // 1. Main Title
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'REKAPITULASI KEHADIRAN SISWA');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(25);

                // 2. Periode Laporan
                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('A2', 'Periode: ' . $this->data['startDate']->translatedFormat('d F Y') . ' - ' . $this->data['endDate']->translatedFormat('d F Y'));
                $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 3. Info Box (Sekolah & Siswa)
                $infoBoxRange = 'A4:D8';
                $sheet->getStyle($infoBoxRange)->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFF2F2F2'); // Light gray background
                $sheet->getStyle($infoBoxRange)->getBorders()->getOutline()
                      ->setBorderStyle(Border::BORDER_MEDIUM)
                      ->getColor()->setARGB('FFC9C9C9');

                // Info Sekolah
                $sheet->mergeCells('A5:B5');
                $sheet->setCellValue('A5', 'Informasi Sekolah');
                $sheet->getStyle('A5')->getFont()->setBold(true);
                $sheet->setCellValue('A6', 'Nama Sekolah:');
                $sheet->setCellValue('B6', $this->data['namaSekolah']);
                
                // Info Siswa
                $sheet->mergeCells('C5:D5');
                $sheet->setCellValue('C5', 'Informasi Siswa');
                $sheet->getStyle('C5')->getFont()->setBold(true);
                $sheet->setCellValue('C6', 'Nama:');
                $sheet->setCellValue('D6', $this->data['user']->name);
                $sheet->setCellValue('C7', 'NIS:');
                $sheet->setCellValue('D7', $this->data['user']->identifier);
                $sheet->setCellValue('C8', 'Kelas:');
                $sheet->setCellValue('D8', $this->data['user']->siswaProfile->kelas->nama_kelas ?? '-');

                // 4. Summary Table
                $startRow = 10;
                $sheet->setCellValue('A' . $startRow, 'Ringkasan Absensi');
                $sheet->getStyle('A' . $startRow)->getFont()->setBold(true)->setSize(12);
                $sheet->mergeCells('A' . $startRow . ':B' . $startRow);

                $headerRow = $startRow + 1;
                $sheet->setCellValue('A' . $headerRow, 'Status Kehadiran');
                $sheet->setCellValue('B' . $headerRow, 'Jumlah');
                $sheet->getStyle('A' . $headerRow . ':B' . $headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4A90E2']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(20);

                // Data Statistik
                $hadir = $this->data['absensi']->where('status', 'hadir')->count();
                $terlambat = $this->data['absensi']->where('status', 'terlambat')->count();
                $sakit = $this->data['absensi']->where('status', 'sakit')->count();
                $izin = $this->data['absensi']->where('status', 'izin')->count();
                $totalHariSekolah = $this->data['startDate']->diffInWeekdays($this->data['endDate']) + 1;
                $alpha = max(0, $totalHariSekolah - ($hadir + $terlambat + $sakit + $izin));

                $dataRows = [
                    ['Hadir', $hadir, 'FFC6EFCE'],
                    ['Terlambat', $terlambat, 'FFFFC7CE'],
                    ['Sakit', $sakit, 'FFFFEB9C'],
                    ['Izin', $izin, 'FFB4C6E7'],
                    ['Alpha', $alpha, 'FFE7E6E6'],
                ];

                $currentRow = $headerRow + 1;
                foreach ($dataRows as $rowData) {
                    $sheet->setCellValue('A' . $currentRow, $rowData[0]);
                    $sheet->setCellValue('B' . $currentRow, $rowData[1]);
                    $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($rowData[2]);
                    $currentRow++;
                }

                // Border untuk tabel ringkasan
                $tableRange = 'A' . $headerRow . ':B' . ($currentRow - 1);
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Set Column Widths
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
            }
        ];
    }
}
