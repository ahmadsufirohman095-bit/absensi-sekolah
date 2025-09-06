<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Setting;
use App\Models\User;
use App\Models\SiswaProfile;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ScanAbsensiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        \Mockery::close(); // Close any existing mocks
        parent::setUp();

        // Set timezone for consistent testing
        config(['app.timezone' => 'Asia/Jakarta']);

        // Seed a default 'jam_masuk' setting
        Setting::create(['key' => 'jam_masuk', 'value' => '07:00']);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_record_on_time_attendance_successfully()
    {
        // Mock Carbon to be before the cut-off time
        Carbon::setTestNow(Carbon::parse('2025-01-01 06:50:00', 'Asia/Jakarta'));

        $kelas = Kelas::factory()->create(['nama_kelas' => '10 IPA 1']);
        $user = User::factory()->create(['role' => 'siswa', 'identifier' => 'siswa123']);
        SiswaProfile::factory()->create(['user_id' => $user->id, 'kelas_id' => $kelas->id]);

        // Act as an authenticated user (e.g., an admin or guru who can manage absensi)
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->postJson('/scan-absensi', ['identifier' => 'siswa123']);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => "Absensi berhasil: {$user->name} tercatat hadir.",
                     'data' => [
                         'name' => $user->name,
                         'identifier' => 'siswa123',
                         'kelas' => '10 IPA 1',
                         'status' => 'hadir',
                     ]
                 ]);

        $this->assertDatabaseHas('absensis', [
            'user_id' => $user->id,
            'tanggal_absensi' => Carbon::now('Asia/Jakarta')->startOfDay()->toDateTimeString(),
            'waktu_masuk' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
            'status' => 'hadir',
        ]);

        Carbon::setTestNow(); // Reset Carbon mock
    }

    #[Test]
    public function it_can_record_late_attendance_successfully()
    {
        // Mock Carbon to be after the cut-off time
        Carbon::setTestNow(Carbon::parse('2025-01-01 07:15:00', 'Asia/Jakarta'));

        $kelas = Kelas::factory()->create(['nama_kelas' => '10 IPA 2']);
        $user = User::factory()->create(['role' => 'siswa', 'identifier' => 'siswa456']);
        SiswaProfile::factory()->create(['user_id' => $user->id, 'kelas_id' => $kelas->id]);

        // Act as an authenticated user
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->postJson('/scan-absensi', ['identifier' => 'siswa456']);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => "Absensi berhasil: {$user->name} tercatat terlambat.",
                     'data' => [
                         'name' => $user->name,
                         'identifier' => 'siswa456',
                         'kelas' => '10 IPA 2',
                         'status' => 'terlambat',
                     ]
                 ]);

        $this->assertDatabaseHas('absensis', [
            'user_id' => $user->id,
            'tanggal_absensi' => Carbon::now('Asia/Jakarta')->startOfDay()->toDateTimeString(),
            'waktu_masuk' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
            'status' => 'terlambat',
        ]);

        Carbon::setTestNow(); // Reset Carbon mock
    }

    #[Test]
    public function it_returns_error_for_invalid_identifier()
    {
        // Act as an authenticated user
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->postJson('/scan-absensi', ['identifier' => 'invalid_id']);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'QR Code tidak valid atau bukan milik siswa.'
                 ]);

        $this->assertDatabaseCount('absensis', 0);
    }

    #[Test]
    public function it_returns_error_if_user_is_not_a_student()
    {
        $user = User::factory()->create(['role' => 'guru', 'identifier' => 'guru123']);

        // Act as an authenticated user
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->postJson('/scan-absensi', ['identifier' => 'guru123']);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'QR Code tidak valid atau bukan milik siswa.'
                 ]);

        $this->assertDatabaseCount('absensis', 0);
    }

    #[Test]
    public function it_returns_error_if_student_already_absent_today()
    {
        Carbon::setTestNow(Carbon::parse('2025-01-01 07:00:00', 'Asia/Jakarta'));

        $kelas = Kelas::factory()->create();
        $user = User::factory()->create(['role' => 'siswa', 'identifier' => 'siswa789']);
        SiswaProfile::factory()->create(['user_id' => $user->id, 'kelas_id' => $kelas->id]);

        Absensi::create([
            'user_id' => $user->id,
            'tanggal_absensi' => Carbon::now('Asia/Jakarta')->startOfDay(),
            'waktu_masuk' => Carbon::now('Asia/Jakarta'),
            'status' => 'hadir',
        ]);

        // Act as an authenticated user
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->postJson('/scan-absensi', ['identifier' => 'siswa789']);

        $response->assertStatus(200) // Laravel returns 200 for custom error messages
                 ->assertJson([
                     'success' => false,
                     'message' => "Sudah Absen: {$user->name} telah tercatat hadir hari ini."
                 ]);

        $this->assertDatabaseCount('absensis', 1); // Still only one record

        Carbon::setTestNow(); // Reset Carbon mock
    }

    #[Test]
    public function it_handles_missing_jam_masuk_setting_gracefully()
    {
        // Remove the default jam_masuk setting
        Setting::where('key', 'jam_masuk')->delete();

        Carbon::setTestNow(Carbon::parse('2025-01-01 07:15:00', 'Asia/Jakarta')); // Should default to 07:00

        $kelas = Kelas::factory()->create();
        $user = User::factory()->create(['role' => 'siswa', 'identifier' => 'siswa_no_setting']);
        SiswaProfile::factory()->create(['user_id' => $user->id, 'kelas_id' => $kelas->id]);

        // Act as an authenticated user
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->postJson('/scan-absensi', ['identifier' => 'siswa_no_setting']);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => "Absensi berhasil: {$user->name} tercatat terlambat.",
                     'data' => [
                         'status' => 'terlambat',
                     ]
                 ]);

        $this->assertDatabaseHas('absensis', [
            'user_id' => $user->id,
            'status' => 'terlambat',
        ]);

        Carbon::setTestNow(); // Reset Carbon mock
    }

    #[Test]
    public function it_returns_error_on_database_failure()
    {
        // Mock the Absensi model to throw an exception on create
        // Using Mockery::mock('alias:...') to mock static methods
        $absensiMock = \Mockery::mock('alias:App\Models\Absensi');
        $absensiMock->shouldReceive('create')
            ->andThrow(new \Exception('Simulated database error'));

        $kelas = Kelas::factory()->create();
        $user = User::factory()->create(['role' => 'siswa', 'identifier' => 'siswa_db_fail']);
        SiswaProfile::factory()->create(['user_id' => $user->id, 'kelas_id' => $kelas->id]);

        // Act as an authenticated user
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->postJson('/scan-absensi', ['identifier' => 'siswa_db_fail']);

        $response->assertStatus(500)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Terjadi kesalahan internal. Gagal menyimpan data absensi.'
                 ]);

        $this->assertDatabaseCount('absensis', 0);
    }
}
