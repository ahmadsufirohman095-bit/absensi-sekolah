<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Models\AdminProfile;
use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\Kelas;
use App\Models\MataPelajaran;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        set_time_limit(0); // Remove execution time limit for import
        \Log::info('Importing user row:', $row);
        $role = strtolower(trim($row['role']));
        \Log::info('Processed role value:', ['role' => $role]);
        $nisNip = $row['nisnip'] ?? null;
        \Log::info('Processed nis_nip value:', ['nis_nip' => $nisNip]);

        // Common profile fields that might be present in the row
        $tanggalLahir = $row['tanggal_lahir'] ?? null;
        $tempatLahir = $row['tempat_lahir'] ?? null;
        $jenisKelamin = $row['jenis_kelamin'] ?? null;
        $alamat = $row['alamat'] ?? null;

        try {
            // 1. Create the User model
            $user = new User([
                'name'     => $row['nama_lengkap'],
                'username' => $row['username'],
                'email'    => $row['email'],
                'role'     => $role,
                'identifier' => (string) $nisNip,
                'password' => isset($row['password']) && !empty($row['password']) ? $row['password'] : Hash::make('password'),
            ]);
            $user->save(); // Save the user first to get an ID

            // 2. Handle profile data based on role
            switch ($role) {
                case 'admin':
                    AdminProfile::create([
                        'user_id' => $user->id,
                        'jabatan' => $row['jabatan'] ?? null,
                        'telepon' => $row['telepon'] ?? null,
                        'tanggal_bergabung' => $row['tanggal_bergabung'] ?? null,
                        'tempat_lahir' => $tempatLahir,
                        'jenis_kelamin' => $jenisKelamin,
                    ]);
                    break;
                case 'guru':
                    $guruProfile = GuruProfile::create([
                        'user_id' => $user->id,
                        'jabatan' => $row['jabatan'] ?? null,
                        'telepon' => $row['telepon'] ?? null,
                        'tanggal_lahir' => $tanggalLahir,
                        'tempat_lahir' => $tempatLahir,
                        'jenis_kelamin' => $jenisKelamin,
                        'alamat' => $alamat,
                    ]);
                    // Handle Mata Pelajaran (many-to-many)
                    if (isset($row['mata_pelajaran']) && !empty($row['mata_pelajaran'])) {
                        $mapelNames = explode(', ', $row['mata_pelajaran']);
                        $mapelIds = MataPelajaran::whereIn('nama_mapel', $mapelNames)->pluck('id');
                        $user->mataPelajarans()->sync($mapelIds);
                    }
                    break;
                case 'siswa':
                    $kelasId = null;
                    if (isset($row['kelas']) && !empty($row['kelas'])) {
                        $kelas = Kelas::where('nama_kelas', $row['kelas'])->first();
                        if ($kelas) {
                            $kelasId = $kelas->id;
                        } else {
                            \Log::warning('Kelas not found during import:', ['kelas_name' => $row['kelas'], 'user_id' => $user->id]);
                            // Optionally, handle error or create new class
                        }
                    }
                    SiswaProfile::create([
                        'user_id' => $user->id,
                        'kelas_id' => $kelasId,
                        'nis' => (string) $nisNip, // NIS is identifier for siswa
                        'nama_lengkap' => $row['nama_lengkap'],
                        'tanggal_lahir' => $tanggalLahir,
                        'tempat_lahir' => $tempatLahir,
                        'jenis_kelamin' => $jenisKelamin,
                        'alamat' => $alamat,
                        'nama_ayah' => $row['nama_ayah'] ?? null,
                        'nama_ibu' => $row['nama_ibu'] ?? null,
                        'telepon_ayah' => $row['telepon_ayah'] ?? null,
                        'telepon_ibu' => $row['telepon_ibu'] ?? null,
                    ]);
                    break;
            }

            return $user; // Return the created user model
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error during user import:', [
                'exception' => $e->getMessage(),
                'row_data' => $row,
                'processed_role' => $role,
                'processed_nis_nip' => $nisNip,
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('General error during user import:', [
                'exception' => $e->getMessage(),
                'row_data' => $row,
                'processed_role' => $role,
                'processed_nis_nip' => $nisNip,
            ]);
            throw $e;
        }
    }

    /**
     * Tentukan baris mana yang menjadi heading (kepala kolom).
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Aturan validasi untuk setiap baris.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,guru,siswa,Admin,Guru,Siswa', // Accept capitalized roles as well
            'nisnip' => 'nullable|unique:users,identifier', // Removed 'string' validation, and made nullable
            'password' => 'nullable|string',

            // Common profile fields
            'tanggal_lahir' => 'nullable|date_format:d-m-Y',
            'tempat_lahir' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'alamat' => 'nullable|string',

            // Admin specific fields
            'jabatan' => 'nullable|string', // Used for admin and guru
            'telepon' => 'nullable|string', // Used for admin and guru
            'tanggal_bergabung' => 'nullable|date_format:d-m-Y', // Admin specific

            // Guru specific fields
            'mata_pelajaran' => 'nullable|string', // Comma-separated string of mapel names

            // Siswa specific fields
            'kelas' => 'nullable|string', // Class name
            'nama_ayah' => 'nullable|string',
            'nama_ibu' => 'nullable|string',
            'telepon_ayah' => 'nullable|string',
            'telepon_ibu' => 'nullable|string',
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
