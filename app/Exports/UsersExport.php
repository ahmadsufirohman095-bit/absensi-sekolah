<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents
{
    use Exportable;

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        return $this->query->with([
            'adminProfile',
                        'guruProfile', // Load guruProfile
            'mataPelajarans', // Load mataPelajarans directly on User model // Load mataPelajarans for guru
            'siswaProfile.kelas' // Load kelas for siswa
        ]);
    }

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
            'Jabatan', // Admin/Guru
            'Telepon', // Admin/Guru
            'Tanggal Lahir', // Guru/Siswa
            'Tempat Lahir', // Admin/Guru/Siswa
            'Jenis Kelamin', // Admin/Guru/Siswa
            'Alamat', // Guru/Siswa
            'Nama Ayah', // Siswa
            'Nama Ibu', // Siswa
            'Telepon Ayah', // Siswa
            'Telepon Ibu', // Siswa
            'Kelas', // Siswa
            'Mata Pelajaran', // Guru
            'Tanggal Bergabung', // Admin
            'Password',
        ];
    }

    public function map($user): array
    {
        $jabatan = null;
        $telepon = null;
        $tanggalLahir = null;
        $tempatLahir = null;
        $jenisKelamin = null;
        $alamat = null;
        $namaAyah = null;
        $namaIbu = null;
        $teleponAyah = null;
        $teleponIbu = null;
        $kelas = null;
        $mataPelajaran = null;
        $tanggalBergabung = null;

        switch ($user->role) {
            case 'admin':
                if ($user->adminProfile) {
                    $jabatan = $user->adminProfile->jabatan;
                    $telepon = $user->adminProfile->telepon;
                    $tanggalBergabung = $user->adminProfile->tanggal_bergabung ? $user->adminProfile->tanggal_bergabung->format('d-m-Y') : null;
                    $tempatLahir = $user->adminProfile->tempat_lahir;
                    $jenisKelamin = $user->adminProfile->jenis_kelamin;
                }
                break;
            case 'guru':
                if ($user->guruProfile) {
                    $jabatan = $user->guruProfile->jabatan;
                    $telepon = $user->guruProfile->telepon;
                    $tanggalLahir = $user->guruProfile->tanggal_lahir ? $user->guruProfile->tanggal_lahir->format('d-m-Y') : null;
                    $tempatLahir = $user->guruProfile->tempat_lahir;
                    $jenisKelamin = $user->guruProfile->jenis_kelamin;
                    $alamat = $user->guruProfile->alamat;
                    $mataPelajaran = $user->mataPelajarans->pluck('nama_mapel')->implode(', ');
                }
                break;
            case 'siswa':
                if ($user->siswaProfile) {
                    $tanggalLahir = $user->siswaProfile->tanggal_lahir ? $user->siswaProfile->tanggal_lahir->format('d-m-Y') : null;
                    $tempatLahir = $user->siswaProfile->tempat_lahir;
                    $jenisKelamin = $user->siswaProfile->jenis_kelamin;
                    $alamat = $user->siswaProfile->alamat;
                    $namaAyah = $user->siswaProfile->nama_ayah;
                    $namaIbu = $user->siswaProfile->nama_ibu;
                    $teleponAyah = $user->siswaProfile->telepon_ayah;
                    $teleponIbu = $user->siswaProfile->telepon_ibu;
                    $kelas = $user->siswaProfile->kelas->nama_kelas ?? null;
                }
                break;
        }

        return [
            $user->id,
            $user->name,
            $user->username,
            $user->email,
            ucfirst($user->role),
            $user->identifier,
            $user->created_at->format('d-m-Y H:i:s'),
            $jabatan,
            $telepon,
            $tanggalLahir,
            $tempatLahir,
            $jenisKelamin,
            $alamat,
            $namaAyah,
            $namaIbu,
            $teleponAyah,
            $teleponIbu,
            $kelas,
            $mataPelajaran,
            $tanggalBergabung,
            $user->password,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:U1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4A90E2'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set default font for all cells
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        // Add new column widths
        $sheet->getColumnDimension('H')->setWidth(15); // Jabatan
        $sheet->getColumnDimension('I')->setWidth(15); // Telepon
        $sheet->getColumnDimension('J')->setWidth(18); // Tanggal Lahir
        $sheet->getColumnDimension('K')->setWidth(18); // Tempat Lahir
        $sheet->getColumnDimension('L')->setWidth(15); // Jenis Kelamin
        $sheet->getColumnDimension('M')->setWidth(30); // Alamat
        $sheet->getColumnDimension('N')->setWidth(25); // Nama Ayah
        $sheet->getColumnDimension('O')->setWidth(25); // Nama Ibu
        $sheet->getColumnDimension('P')->setWidth(18); // Telepon Ayah
        $sheet->getColumnDimension('Q')->setWidth(18); // Telepon Ibu
        $sheet->getColumnDimension('R')->setWidth(15); // Kelas
        $sheet->getColumnDimension('S')->setWidth(30); // Mata Pelajaran
        $sheet->getColumnDimension('T')->setWidth(18); // Tanggal Bergabung
        $sheet->getColumnDimension('U')->setWidth(40); // Password
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Apply autofilter to header row
                $sheet->setAutoFilter('A1:U1'); // Changed U1 to T1

                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Set alignment for data rows
                $sheet->getStyle('A2:U' . $sheet->getHighestRow())
                      ->getAlignment()
                      ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
