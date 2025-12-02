<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inisialisasi Faker dengan lokal Indonesia
        $faker = Faker::create('id_ID');
        $schoolDomain = 'gmail.com';

        // Nonaktifkan foreign key checks untuk pembersihan total
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tabel-tabel terkait untuk memulai dari awal
        DB::table('guru_mata_pelajaran')->truncate();
        DB::table('kelas_mata_pelajaran')->truncate();
        DB::table('siswa_profiles')->truncate();
        DB::table('guru_profiles')->truncate();
        DB::table('admin_profiles')->truncate();
        DB::table('users')->truncate();
        DB::table('kelas')->truncate();
        DB::table('mata_pelajarans')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // --- 1. MEMBUAT MATA PELAJARAN ---
        $this->command->info('Membuat data mata pelajaran...');
        $mapels = [
            ['kode' => 'PAI', 'nama' => 'Pendidikan Agama Islam'],
            ['kode' => 'B IND', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'B ING', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'MTK', 'nama' => 'Matematika'],
            ['kode' => 'IPA', 'nama' => 'Ilmu Pengetahuan Alam'],
            ['kode' => 'IPS', 'nama' => 'Ilmu Pengetahuan Sosial'],
            ['kode' => 'PKN', 'nama' => 'Pendidikan Kewarganegaraan'],
            ['kode' => 'SB', 'nama' => 'Seni Budaya'],
            ['kode' => 'PJOK', 'nama' => 'Pendidikan Jasmani'],
            ['kode' => 'B ARB', 'nama' => 'Bahasa Arab'],
            ['kode' => 'INF', 'nama' => 'Informatika'],
        ];

        $mapelList = collect($mapels)->map(function ($mapel) {
            return MataPelajaran::create([
                'kode_mapel' => $mapel['kode'],
                'nama_mapel' => $mapel['nama']
            ]);
        });


        // --- 2. MEMBUAT KELAS ---
        $this->command->info('Membuat data kelas...');
        $kelasNames = ['VII-A', 'VII-B', 'VIII-A', 'VIII-B', 'IX-A', 'IX-B'];
        $kelasList = collect($kelasNames)->map(fn ($nama) => Kelas::create(['nama_kelas' => $nama]));

        // --- 3. MEMBUAT ADMIN ---
        $this->command->info('Membuat akun Admin...');
        $admin = User::create([
            'name' => 'Admin Sekolah',
            'username' => 'admin',
            'identifier' => 'admin001',
            'email' => 'admin@' . $schoolDomain,
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $admin->adminProfile()->create([
            'telepon' => $faker->phoneNumber,
            'jabatan' => 'Administrator Sistem',
            'tanggal_lahir' => $faker->date('Y-m-d', '1980-01-01'), // Tambahkan tanggal_lahir
            'tempat_lahir' => $faker->city,
            'jenis_kelamin' => $faker->randomElement(['laki-laki', 'perempuan']),
            'tanggal_bergabung' => $admin->created_at,
            'foto' => null,
        ]);

        $this->command->info('Membuat akun Admin Kedua...');
        $admin2 = User::create([
            'name' => 'Admin Dua',
            'username' => 'admin2',
            'identifier' => 'admin002',
            'email' => 'admin2@' . $schoolDomain,
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $admin2->adminProfile()->create([
            'telepon' => $faker->phoneNumber,
            'jabatan' => 'Administrator Cadangan',
            'tanggal_lahir' => $faker->date('Y-m-d', '1982-01-01'), // Tambahkan tanggal_lahir
            'tempat_lahir' => $faker->city,
            'jenis_kelamin' => $faker->randomElement(['laki-laki', 'perempuan']),
            'tanggal_bergabung' => $admin2->created_at,
            'foto' => null,
        ]);

        // --- 4. MEMBUAT TU ---
        $this->command->info('Membuat 5 akun TU...');
        for ($i = 1; $i <= 5; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $faker->firstName($gender);
            $lastName = $faker->lastName();
            $fullName = $firstName . ' ' . $lastName;
            $username = strtolower(str_replace([' ', '.'], '', $firstName . $lastName)) . '_tu' . $i;

            $tu = User::create([
                'name' => $fullName,
                'username' => $username,
                'identifier' => 'tu' . $faker->unique()->numerify('###'),
                'email' => $username . '@' . $schoolDomain,
                'password' => Hash::make('password'),
                'role' => 'tu',
            ]);
            $tu->tuProfile()->create([
                'telepon' => $faker->phoneNumber,
                'jabatan' => 'Staff Tata Usaha ' . $i,
                'tanggal_lahir' => $faker->date('Y-m-d', '1990-01-01'), // Tambahkan tanggal_lahir
                'tempat_lahir' => $faker->city,
                'jenis_kelamin' => ($gender == 'male') ? 'laki-laki' : 'perempuan',
                'tanggal_bergabung' => $tu->created_at,
                'foto' => null,
            ]);
        }

        // --- 5. MEMBUAT LAINNYA ---
        $this->command->info('Membuat 10 akun Lainnya dengan custom role berbeda...');
        $customRoles = [
            'Bendahara', 'Pustakawan', 'Koordinator Ekstrakurikuler', 'Nurs',
            'Staff IT', 'Petugas UKS', 'Petugas Kebersihan', 'Satpam Sekolah',
            'Koordinator Kesiswaan', 'Staff Administrasi Umum'
        ];
        for ($i = 0; $i < 10; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $faker->firstName($gender);
            $lastName = $faker->lastName();
            $fullName = $firstName . ' ' . $lastName;
            $username = strtolower(str_replace([' ', '.'], '', $firstName . $lastName)) . '_other' . $i;
            $roleName = $customRoles[$i];

            $other = User::create([
                'name' => $fullName,
                'username' => $username,
                'identifier' => 'other' . $faker->unique()->numerify('###'),
                'email' => $username . '@' . $schoolDomain,
                'password' => Hash::make('password'),
                'role' => 'other',
                'custom_role' => $roleName,
            ]);
            $other->otherProfile()->create([
                'custom_role_name' => $roleName,
                'telepon' => $faker->phoneNumber,
                'jabatan' => $roleName,
                'tanggal_lahir' => $faker->date('Y-m-d', '1985-01-01'), // Tambahkan tanggal_lahir
                'tempat_lahir' => $faker->city,
                'jenis_kelamin' => ($gender == 'male') ? 'laki-laki' : 'perempuan',
                'tanggal_bergabung' => $other->created_at,
                'foto' => null,
            ]);
        }

        // --- 6. MEMBUAT GURU ---
        $this->command->info('Membuat data guru...');
        $guruList = collect();
        for ($i = 0; $i < 10; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $faker->firstName($gender);
            $lastName = $faker->lastName();
            $fullName = $firstName . ' ' . $lastName;
            $username = strtolower(str_replace([' ', '.'], '', $firstName . $lastName)) . $faker->unique()->randomNumber(3);
            
            $guru = User::create([
                'name' => $fullName,
                'username' => $username,
                'identifier' => 'NIP' . $faker->unique()->numerify('##########'),
                'email' => $username . '@' . $schoolDomain,
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]);

            $guru->guruProfile()->create([
                'telepon' => $faker->phoneNumber,
                'jabatan' => 'Guru Mata Pelajaran',
                'jenis_kelamin' => ($gender == 'male') ? 'laki-laki' : 'perempuan',
                'alamat' => $faker->address,
                'tanggal_lahir' => $faker->date('Y-m-d', '1995-01-01'),
                'tempat_lahir' => $faker->city,
            ]);

            // Penugasan mata pelajaran akan dilakukan setelah ini
            $guruList->push($guru);
        }

        // --- 7. MENETAPKAN GURU PENGAMPU & WALI KELAS ---
        $this->command->info('Menetapkan guru pengampu dan wali kelas...');

        // Menetapkan satu guru pengampu untuk setiap mata pelajaran
        if ($guruList->isNotEmpty()) {
            $guruIterator = 0;
            foreach ($mapelList as $mapel) {
                // Ambil satu guru dari daftar, lalu lanjut ke guru berikutnya untuk mapel berikutnya
                $guruToAssign = $guruList[$guruIterator % $guruList->count()];
                $mapel->gurus()->sync([$guruToAssign->id]);
                $guruIterator++;
            }
        }

        // Menetapkan guru sebagai wali kelas
        $guruList->shuffle()->take($kelasList->count())->each(function ($guru, $index) use ($kelasList) {
            $kelas = $kelasList->get($index);
            if ($kelas) {
                $kelas->update(['wali_kelas_id' => $guru->id]);
            }
        });

        // --- 8. MEMBUAT SISWA ---
        $this->command->info('Membuat data siswa...');
        $nisCounter = (int) date('y') * 10000 + 1;
        foreach ($kelasList as $kelas) {
            $this->command->warn("   -> Mengisi kelas {$kelas->nama_kelas}...");
            for ($i = 0; $i < 20; $i++) {
                $gender = $faker->randomElement(['male', 'female']);
                $firstName = $faker->firstName($gender);
                $lastName = $faker->lastName();
                $fullName = $firstName . ' ' . $lastName;
                $username = strtolower(str_replace([' ', '.'], '', $firstName . $lastName)) . $faker->unique()->randomNumber(3);

                $siswa = User::create([
                    'name' => $fullName,
                    'username' => $username,
                    'identifier' => $nisCounter,
                    'email' => $username . '@' . $schoolDomain,
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                ]);

                $siswa->siswaProfile()->create([
                    'nis' => $nisCounter,
                    'kelas_id' => $kelas->id,
                    'tanggal_lahir' => $faker->date('Y-m-d', '2010-01-01'),
                    'alamat' => $faker->address,
                    'tempat_lahir' => $faker->city,
                    'jenis_kelamin' => ($gender == 'male') ? 'laki-laki' : 'perempuan',
                    'nama_ayah' => $faker->name('male'),
                    'nama_ibu' => $faker->name('female'),
                    'telepon_ayah' => $faker->phoneNumber,
                    'telepon_ibu' => $faker->phoneNumber,
                ]);
                $nisCounter++;
            }
        }
        
        

        $this->command->info('Database seeding selesai!');
    }
}
