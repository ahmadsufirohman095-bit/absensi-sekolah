<?php
namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class DetailAbsensiSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithStartRow
{
    private $data;
    private $startRow = 10;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['absensi']->sortBy('tanggal_absensi');
    }

    public function title(): string
    {
        return "Detail Log Kehadiran";
    }

    public function headings(): array
    {
        return ['Tanggal', 'Waktu Masuk', 'Status', 'Keterangan'];
    }

    public function map($row): array
    {
        $keterangan = $row->keterangan ?? '-';
        if (!in_array($row->status, ['sakit', 'izin'])) {
            $keterangan = '-';
        }

        return [
            Carbon::parse($row->tanggal_absensi)->format('d-m-Y'),
            $row->waktu_masuk ? Carbon::parse($row->waktu_masuk)->format('H:i:s') : '-',
            ucfirst($row->status),
            $keterangan
        ];
    }

    public function startRow(): int
    {
        return $this->startRow;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Style header row
        $headerRange = 'A' . $this->startRow . ':D' . $this->startRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4A90E2']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($this->startRow)->setRowHeight(25);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(35);

        // Set alignment and borders for the entire table
        $highestRow = $sheet->getHighestRow();
        $tableRange = 'A' . $this->startRow . ':D' . $highestRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($tableRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // Center align specific columns
        $sheet->getStyle('A' . ($this->startRow + 1) . ':C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $headerRange = 'A' . $this->startRow . ':D' . $this->startRow;
                $sheet->setAutoFilter($headerRange);

                // 1. Main Title
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'DETAIL LOG KEHADIRAN SISWA');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(25);

                // 2. Periode Laporan
                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('A2', 'Periode: ' . $this->data['startDate']->translatedFormat('d F Y') . ' - ' . $this->data['endDate']->translatedFormat('d F Y'));
                $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 3. Info Box
                $infoBoxRange = 'A4:D8';
                $sheet->getStyle($infoBoxRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                $sheet->getStyle($infoBoxRange)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setARGB('FFC9C9C9');
                
                $sheet->mergeCells('A5:B5');
                $sheet->setCellValue('A5', 'Informasi Sekolah');
                $sheet->getStyle('A5')->getFont()->setBold(true);
                $sheet->setCellValue('A6', 'Nama Sekolah:');
                $sheet->setCellValue('B6', $this->data['namaSekolah']);
                
                $sheet->mergeCells('C5:D5');
                $sheet->setCellValue('C5', 'Informasi Siswa');
                $sheet->getStyle('C5')->getFont()->setBold(true);
                $sheet->setCellValue('C6', 'Nama:');
                $sheet->setCellValue('D6', $this->data['user']->name);
                $sheet->setCellValue('C7', 'NIS:');
                $sheet->setCellValue('D7', $this->data['user']->identifier);
                $sheet->setCellValue('C8', 'Kelas:');
                $sheet->setCellValue('D8', $this->data['user']->siswaProfile->kelas->nama_kelas ?? '-');

                // 4. Conditional Row Styling
                $startDataRow = $this->startRow + 1;
                $highestRow = $sheet->getHighestRow();
                for ($rowNum = $startDataRow; $rowNum <= $highestRow; $rowNum++) {
                    $status = strtolower($sheet->getCell('C' . $rowNum)->getValue());
                    $fillColor = null;
                    switch ($status) {
                        case 'hadir': $fillColor = 'FFC6EFCE'; break;
                        case 'terlambat': $fillColor = 'FFFFC7CE'; break;
                        case 'sakit': $fillColor = 'FFFFEB9C'; break;
                        case 'izin': $fillColor = 'FFB4C6E7'; break;
                        case 'alpha': $fillColor = 'FFE7E6E6'; break;
                    }
                    if ($fillColor) {
                        $sheet->getStyle('A' . $rowNum . ':D' . $rowNum)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($fillColor);
                    }
                }
            },
        ];
    }
}
