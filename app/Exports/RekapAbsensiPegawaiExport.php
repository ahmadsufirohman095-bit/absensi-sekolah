<?php

namespace App\Exports;

use App\Models\AbsensiPegawai;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

class RekapAbsensiPegawaiExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithTitle
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = AbsensiPegawai::with([
            'user' => function ($query) {
                $query->withTrashed();
            },
            'jadwalAbsensiPegawai' => function ($query) {
                $query->withTrashed();
            }
        ]);

        // Filter by date range
        if ($this->request->filled('start_date')) {
            $query->whereDate('tanggal_absensi', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->whereDate('tanggal_absensi', '<=', $this->request->end_date);
        }

        // Filter by user (pegawai)
        if ($this->request->filled('user_id')) {
            $query->where('user_id', $this->request->user_id);
        }

        // Filter by user role (pegawai_role)
        if ($this->request->filled('pegawai_role')) {
            $query->whereHas('user', function ($q) {
                $q->where('role', $this->request->pegawai_role);
            });
        }

        // Filter by attendance type
        if ($this->request->filled('attendance_type')) {
            $query->where('attendance_type', $this->request->attendance_type);
        }

        // Filter by status
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        return $query->orderBy('tanggal_absensi', 'desc')->orderBy('waktu_masuk', 'desc');
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Waktu Masuk',
            'Nama Pegawai',
            'Jenis Pegawai',
            'Status',
            'Keterangan',
            'Tipe Absensi',
        ];
    }

    public function map($absensiPegawai): array
    {
        return [
            $absensiPegawai->tanggal_absensi->format('d M Y'),
            $absensiPegawai->waktu_masuk ? $absensiPegawai->waktu_masuk->format('H:i') : '-',
            $absensiPegawai->user->name ?? 'Pegawai Dihapus',
            ucfirst($absensiPegawai->user->role ?? '-'),
            ucfirst($absensiPegawai->status),
            $absensiPegawai->keterangan ?? '-',
            ucfirst($absensiPegawai->attendance_type),
        ];
    }

    public function title(): string
    {
        return 'Rekap Absensi Pegawai';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set the header style
                $sheet->getStyle('A1:G1')->applyFromArray([
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
                $highestColumn = 'G';

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
                $sheet->getStyle('A2:B' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Tanggal dan Waktu
                $sheet->getStyle('E2:G' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Status, Keterangan, Tipe

                // Freeze the header row
                $sheet->freezePane('A2');

                // Add auto filter to the header row
                $sheet->setAutoFilter('A1:G1');
            },
        ];
    }
}
