<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure storage disk is mocked for file uploads
        Storage::fake('public');
    }

    #[Test]
    public function an_admin_can_create_a_new_admin_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->post(route('users.store'), [
            'name' => 'New Admin',
            'username' => 'newadmin',
            'identifier' => 'admin_id_123',
            'email' => 'newadmin@example.com',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
            'admin_jabatan' => 'Kepala Sekolah',
            'admin_telepon' => '081234567890',
            'tanggal_bergabung' => '2024-01-01',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User baru berhasil ditambahkan.');

        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'role' => 'admin',
            'username' => 'newadmin',
            'identifier' => 'admin_id_123',
        ]);

        $newUser = User::where('email', 'newadmin@example.com')->first();
        $this->assertDatabaseHas('admin_profiles', [
            'user_id' => $newUser->id,
            'jabatan' => 'Kepala Sekolah',
            'telepon' => '081234567890',
            'tanggal_bergabung' => '2024-01-01',
        ]);
        $this->assertTrue(Hash::check('password', $newUser->password));
    }

    #[Test]
    public function an_admin_can_create_a_new_guru_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $mataPelajaran1 = MataPelajaran::factory()->create();
        $mataPelajaran2 = MataPelajaran::factory()->create();

        $response = $this->post(route('users.store'), [
            'name' => 'New Guru',
            'username' => 'newguru',
            'identifier' => 'guru_id_456',
            'email' => 'newguru@example.com',
            'role' => 'guru',
            'password' => 'password',
            'password_confirmation' => 'password',
            'guru_jabatan' => 'Guru Matematika',
            'guru_telepon' => '081234567891',
            'tanggal_lahir' => '1980-05-10',
            'alamat' => 'Jl. Pendidikan No. 10',
            'jenis_kelamin' => 'laki-laki',
            'tempat_lahir' => 'Jakarta',
            'mata_pelajaran_ids' => [$mataPelajaran1->id, $mataPelajaran2->id],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User baru berhasil ditambahkan.');

        $this->assertDatabaseHas('users', [
            'email' => 'newguru@example.com',
            'role' => 'guru',
            'username' => 'newguru',
            'identifier' => 'guru_id_456',
        ]);

        $newUser = User::where('email', 'newguru@example.com')->first();
        $this->assertDatabaseHas('guru_profiles', [
            'user_id' => $newUser->id,
            'jabatan' => 'Guru Matematika',
            'telepon' => '081234567891',
            'tanggal_lahir' => '1980-05-10',
            'alamat' => 'Jl. Pendidikan No. 10',
            'jenis_kelamin' => 'laki-laki',
            'tempat_lahir' => 'Jakarta',
        ]);
        $this->assertTrue(Hash::check('password', $newUser->password));
        $this->assertCount(2, $newUser->mataPelajarans);
        $this->assertTrue($newUser->mataPelajarans->contains($mataPelajaran1));
        $this->assertTrue($newUser->mataPelajarans->contains($mataPelajaran2));
    }

    #[Test]
    public function an_admin_can_create_a_new_siswa_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $kelas = Kelas::factory()->create();

        $response = $this->post(route('users.store'), [
            'name' => 'New Siswa',
            'username' => 'newsiswa',
            'identifier' => 'siswa_id_789',
            'email' => 'newsiswa@example.com',
            'role' => 'siswa',
            'password' => 'password',
            'password_confirmation' => 'password',
            'kelas_id' => $kelas->id,
            'tanggal_lahir' => '2008-01-15',
            'alamat' => 'Jl. Pelajar No. 5',
            'nama_ayah' => 'Ayah Siswa',
            'nama_ibu' => 'Ibu Siswa',
            'telepon_ayah' => '081234567892',
            'telepon_ibu' => '081234567893',
            'jenis_kelamin' => 'perempuan',
            'tempat_lahir' => 'Bandung',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User baru berhasil ditambahkan.');

        $this->assertDatabaseHas('users', [
            'email' => 'newsiswa@example.com',
            'role' => 'siswa',
            'username' => 'newsiswa',
            'identifier' => 'siswa_id_789',
        ]);

        $newUser = User::where('email', 'newsiswa@example.com')->first();
        $this->assertDatabaseHas('siswa_profiles', [
            'user_id' => $newUser->id,
            'kelas_id' => $kelas->id,
            'nis' => 'siswa_id_789', // NIS diambil dari identifier
            'nama_lengkap' => 'New Siswa',
            'tanggal_lahir' => '2008-01-15',
            'alamat' => 'Jl. Pelajar No. 5',
            'nama_ayah' => 'Ayah Siswa',
            'nama_ibu' => 'Ibu Siswa',
            'telepon_ayah' => '081234567892',
            'telepon_ibu' => '081234567893',
            'jenis_kelamin' => 'perempuan',
            'tempat_lahir' => 'Bandung',
        ]);
        $this->assertTrue(Hash::check('password', $newUser->password));
    }

    #[Test]
    public function it_requires_all_mandatory_fields_to_create_a_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->post(route('users.store'), []); // Empty data

        $response->assertSessionHasErrors(['name', 'username', 'identifier', 'email', 'role', 'password']);
        $this->assertDatabaseCount('users', 1); // Only the admin user exists
    }

    #[Test]
    public function it_prevents_duplicate_email_username_and_identifier()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        User::factory()->create([
            'email' => 'existing@example.com',
            'username' => 'existinguser',
            'identifier' => 'existing_id',
        ]);

        $response = $this->post(route('users.store'), [
            'name' => 'Duplicate User',
            'username' => 'existinguser', // Duplicate
            'identifier' => 'existing_id', // Duplicate
            'email' => 'existing@example.com', // Duplicate
            'role' => 'siswa',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['username', 'identifier', 'email']);
        $this->assertDatabaseCount('users', 2); // Admin + existing user
    }

    #[Test]
    public function it_handles_file_upload_for_admin_photo()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $file = UploadedFile::fake()->image('admin_photo.jpg');

        $response = $this->post(route('users.store'), [
            'name' => 'Admin With Photo',
            'username' => 'adminphoto',
            'identifier' => 'admin_photo_id',
            'email' => 'adminphoto@example.com',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
            'admin_foto' => $file,
        ]);

        $response->assertRedirect(route('users.index'));
        Storage::disk('public')->assertExists('fotos/' . $file->hashName());

        $newUser = User::where('email', 'adminphoto@example.com')->first();
        $this->assertDatabaseHas('admin_profiles', [
            'user_id' => $newUser->id,
            'foto' => 'fotos/' . $file->hashName(),
        ]);
    }

    #[Test]
    public function it_handles_database_transaction_failure_during_user_creation()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Mock the User model's create method to throw an exception
        // This will simulate a database error during user creation within the transaction
        \Mockery::mock('alias:App\Models\User')->shouldReceive('create')
            ->andThrow(new \Exception('Simulated database transaction error'));

        $response = $this->post(route('users.store'), [
            'name' => 'Failing User',
            'username' => 'failinguser',
            'identifier' => 'fail_id',
            'email' => 'failing@example.com',
            'role' => 'siswa',
            'password' => 'password',
            'password_confirmation' => 'password',
            'kelas_id' => Kelas::factory()->create()->id,
        ]);

        $response->assertSessionHas('error', 'Terjadi kesalahan saat menambahkan user: Simulated database transaction error');
        $response->assertStatus(302); // Redirect back with error

        // Assert that no new user was created
        $this->assertDatabaseMissing('users', ['email' => 'failing@example.com']);
        $this->assertDatabaseCount('users', 1); // Only the admin user should exist
    }
}
