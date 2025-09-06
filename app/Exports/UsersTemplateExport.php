<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

class UsersTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    public function headings(): array
    {
        return [
            'ID',
            'Nama Lengkap',
            'Username',
            'Email',
            'Role',
            'NIS/NIP',
            'Tanggal Dibuat',
            'Jabatan',
            'Telepon',
            'Tanggal Lahir',
            'Tempat Lahir',
            'Jenis Kelamin',
            'Alamat',
            'Nama Ayah',
            'Nama Ibu',
            'Telepon Ayah',
            'Telepon Ibu',
            'Kelas',
            'Mata Pelajaran',
            'Tanggal Bergabung',
            'Password',
        ];
    }

    public function array(): array
    {
        return [
            [
                '', // ID
                'John Doe', // Nama Lengkap
                'johndoe', // Username
                'john.doe@example.com', // Email
                'siswa', // Role
                '1234567890', // NIS/NIP
                '', // Tanggal Dibuat
                '', // Jabatan
                '', // Telepon
                '10-05-2008', // Tanggal Lahir
                'Jakarta', // Tempat Lahir
                'laki-laki', // Jenis Kelamin
                'Jl. Merdeka No. 10', // Alamat
                'Budi Santoso', // Nama Ayah
                'Siti Aminah', // Nama Ibu
                '081234567890', // Telepon Ayah
                '081234567891', // Telepon Ibu
                'VII-A', // Kelas
                '', // Mata Pelajaran
                '', // Tanggal Bergabung
                '(Opsional) Kosongkan untuk password default "password"',
            ],
            [
                '', // ID
                'Jane Smith', // Nama Lengkap
                'janesmith', // Username
                'jane.smith@example.com', // Email
                'guru', // Role
                '198502022012022002', // NIS/NIP
                '', // Tanggal Dibuat
                'Guru Matematika', // Jabatan
                '081298765432', // Telepon
                '02-02-1985', // Tanggal Lahir
                'Bandung', // Tempat Lahir
                'perempuan', // Jenis Kelamin
                'Jl. Sudirman No. 15', // Alamat
                '', // Nama Ayah
                '', // Nama Ibu
                '', // Telepon Ayah
                '', // Telepon Ibu
                '', // Kelas
                'Matematika', // Mata Pelajaran
                '15-07-2020', // Tanggal Bergabung
                '(Opsional) Bisa diisi dengan password baru',
            ],
        ];
    }

    public function title(): string
    {
        return 'Template Impor Pengguna';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set the header style
                $sheet->getStyle('A1:U1')->applyFromArray([
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
                // Example: Center align specific columns if needed
                // $sheet->getStyle('B2:C' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Freeze the header row
                $sheet->freezePane('A2');

                // Add auto filter to the header row
                $sheet->setAutoFilter('A1:U1');
            },
        ];
    }
}

