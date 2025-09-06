<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class GuruJadwalMengajarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $jadwalPelajaran;
    protected $userName;
    protected $currentRow = 1;

    public function __construct($jadwalPelajaran, $userName)
    {
        $this->jadwalPelajaran = $jadwalPelajaran;
        $this->userName = $userName;
    }

    public function collection()
    {
        $data = collect();
        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function map($row): array
    {
        return $row;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Judul Utama Laporan
                $sheet->mergeCells('A' . $this->currentRow . ':E' . $this->currentRow);
                $sheet->setCellValue('A' . $this->currentRow, 'JADWAL MENGAJAR GURU: ' . strtoupper($this->userName));
                $sheet->getStyle('A' . $this->currentRow)->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A' . $this->currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // $sheet->getStyle('A' . $this->currentRow . ':E' . $this->currentRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4'); // Medium blue background
                // $sheet->getStyle('A' . $this->currentRow . ':E' . $this->currentRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Add border
                $sheet->getRowDimension($this->currentRow)->setRowHeight(30); // Set row height
                $this->currentRow++;
                $this->currentRow++; // Spasi setelah judul

                // Loop melalui jadwal pelajaran per hari
                foreach ($this->jadwalPelajaran as $hari => $jadwals) {
                    // Judul Hari
                    $sheet->mergeCells('A' . $this->currentRow . ':E' . $this->currentRow);
                    $sheet->setCellValue('A' . $this->currentRow, strtoupper($hari));
                    $sheet->getStyle('A' . $this->currentRow)->getFont()->setBold(true)->setSize(14);
                    // $sheet->getStyle('A' . $this->currentRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF8EA9DB'); // Lighter blue background
                    $sheet->getStyle('A' . $this->currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getRowDimension($this->currentRow)->setRowHeight(25); // Set row height
                    $this->currentRow++;

                    // Header Kolom untuk setiap hari
                    $headerRow = $this->currentRow;
                    $sheet->setCellValue('A' . $headerRow, 'Mata Pelajaran');
                    $sheet->setCellValue('B' . $headerRow, 'Kelas');
                    $sheet->setCellValue('C' . $headerRow, 'Jam Mulai');
                    $sheet->setCellValue('D' . $headerRow, 'Jam Selesai');
                    $sheet->setCellValue('E' . $headerRow, 'Waktu');
                    $sheet->getStyle('A' . $headerRow . ':E' . $headerRow)->getFont()->setBold(true);
                    // $sheet->getStyle('A' . $headerRow . ':E' . $headerRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFB4C6E7'); // Even lighter blue background
                    $sheet->getStyle('A' . $headerRow . ':E' . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Center align headers
                    $sheet->getRowDimension($headerRow)->setRowHeight(20); // Set row height
                    $this->currentRow++;

                    // Data Jadwal
                    $dataStartRow = $this->currentRow;
                    foreach ($jadwals->sortBy('jam_mulai') as $jadwal) {
                        $sheet->setCellValue('A' . $this->currentRow, $jadwal->mataPelajaran->nama_mapel);
                        $sheet->setCellValue('B' . $this->currentRow, $jadwal->kelas->nama_kelas);
                        $sheet->setCellValue('C' . $this->currentRow, Carbon::parse($jadwal->jam_mulai)->format('H:i'));
                        $sheet->setCellValue('D' . $this->currentRow, Carbon::parse($jadwal->jam_selesai)->format('H:i'));
                        $sheet->setCellValue('E' . $this->currentRow, Carbon::parse($jadwal->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($jadwal->jam_selesai)->format('H:i'));
                        
                        // Center align for time columns
                        $sheet->getStyle('C' . $this->currentRow . ':E' . $this->currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $this->currentRow++;
                    }
                    $dataEndRow = $this->currentRow - 1;

                    // Apply borders to data and headers
                    if ($dataEndRow >= $dataStartRow) {
                        $sheet->getStyle('A' . $headerRow . ':E' . $dataEndRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    }

                    // Alternating row colors for data
                    // for ($i = $dataStartRow; $i <= $dataEndRow; $i++) {
                    //     if (($i - $dataStartRow) % 2 == 0) {
                    //         $sheet->getStyle('A' . $i . ':E' . $i)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2'); // Light gray
                    //     }
                    // }
                    
                    // Hapus freeze pane per hari
                    // $sheet->freezePane('A' . ($headerRow + 1));

                    $this->currentRow++; // Spasi setelah setiap hari
                }

                // Atur lebar kolom 'Waktu' secara manual (Kolom E)
                $sheet->getColumnDimension('E')->setWidth(15); // Sesuaikan nilai ini sesuai kebutuhan
            },
        ];
    }
}
