<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

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
            // Cari user yang sudah ada (termasuk yang soft deleted) berdasarkan username atau email
            $password = null;
            if (isset($row['password']) && !empty($row['password'])) {
                // Periksa apakah password sudah di-hash (misalnya bcrypt)
                if (str_starts_with($row['password'], '$2y$') || str_starts_with($row['password'], '$2a$') || str_starts_with($row['password'], '$2b$')) {
                    $password = $row['password']; // Sudah di-hash, simpan langsung
                } else {
                    $password = Hash::make($row['password']); // Belum di-hash, hash sekarang
                }
            }

            $user = User::withTrashed()
                        ->where('username', $row['username'])
                        ->orWhere('email', $row['email'])
                        ->first();

            if ($user) {
                // Jika user ditemukan, restore jika soft deleted dan update datanya
                if ($user->trashed()) {
                    $user->restore();
                }
                $user->update([
                    'name'     => $row['nama_lengkap'],
                    'email'    => $row['email'],
                    'role'     => $role,
                    'identifier' => (string) $nisNip,
                    'password' => $password ?? $user->password, // Gunakan password baru atau pertahankan yang lama
                    'is_active' => true, // Aktifkan kembali user saat diimpor/diperbarui
                ]);
                \Log::info('User diperbarui/direstore:', ['user_id' => $user->id, 'username' => $user->username]);
            } else {
                // Jika user tidak ditemukan, buat user baru
                $user = new User([
                    'name'     => $row['nama_lengkap'],
                    'username' => $row['username'],
                    'email'    => $row['email'],
                    'role'     => $role,
                    'identifier' => (string) $nisNip,
                    'password' => $password ?? Hash::make('password'), // Gunakan password baru atau default 'password'
                    'is_active' => true,
                ]);
                $user->save();
                \Log::info('User baru dibuat:', ['user_id' => $user->id, 'username' => $user->username]);
            }

            // Handle profile data based on role (create or update)
            $profileData = [
                'tempat_lahir' => $tempatLahir,
                'jenis_kelamin' => $jenisKelamin,
                'alamat' => $alamat,
            ];

            switch ($role) {
                case 'admin':
                    $user->adminProfile()->updateOrCreate(
                        [],
                        array_merge($profileData, [
                            'jabatan' => $row['jabatan'] ?? null,
                            'telepon' => $row['telepon'] ?? null,
                            'tanggal_bergabung' => $row['tanggal_bergabung'] ?? $user->created_at,
                        ])
                    );
                    break;
                case 'guru':
                    $guruProfile = $user->guruProfile()->updateOrCreate(
                        [],
                        array_merge($profileData, [
                            'jabatan' => $row['jabatan'] ?? null,
                            'telepon' => $row['telepon'] ?? null,
                            'tanggal_lahir' => $tanggalLahir,
                        ])
                    );
                    // Handle Mata Pelajaran (many-to-many)
                    if (isset($row['mata_pelajaran']) && !empty($row['mata_pelajaran'])) {
                        $mapelNames = explode(', ', $row['mata_pelajaran']);
                        $mapelIds = MataPelajaran::whereIn('nama_mapel', $mapelNames)->pluck('id');
                        $user->mataPelajarans()->sync($mapelIds);
                    } else {
                        $user->mataPelajarans()->detach(); // Hapus semua mata pelajaran jika tidak ada di impor
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
                    $user->siswaProfile()->updateOrCreate(
                        [],
                        array_merge($profileData, [
                            'kelas_id' => $kelasId,
                            'nis' => (string) $nisNip,
                            'nama_lengkap' => $row['nama_lengkap'],
                            'tanggal_lahir' => $tanggalLahir,
                            'nama_ayah' => $row['nama_ayah'] ?? null,
                            'nama_ibu' => $row['nama_ibu'] ?? null,
                            'telepon_ayah' => $row['telepon_ayah'] ?? null,
                            'telepon_ibu' => $row['telepon_ibu'] ?? null,
                        ])
                    );
                    break;
            }

            return $user; // Return the created/updated user model
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
            'username' => [
                'required',
                'string',
                Rule::unique('users', 'username')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'role' => 'required|in:admin,guru,siswa,Admin,Guru,Siswa', // Accept capitalized roles as well
            'nisnip' => [
                'nullable',
                Rule::unique('users', 'identifier')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
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
