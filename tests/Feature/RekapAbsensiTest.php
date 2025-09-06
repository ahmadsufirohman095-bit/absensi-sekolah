<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\User;
use App\Models\SiswaProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use App\Exports\RekapAbsensiExport;

class RekapAbsensiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        Excel::fake(); // Mock the Excel facade for testing exports
    }

    /** @test */
    public function an_admin_can_view_rekap_absensi_index_page()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('rekap.index'));

        $response->assertOk();
        $response->assertViewIs('rekap.index');
        $response->assertViewHas('kelasList');
        $response->assertViewHas('startDate');
        $response->assertViewHas('endDate');
    }

    /** @test */
    public function it_can_filter_rekap_data_by_kelas_id()
    {
        $this->actingAs($this->admin);
        $kelas1 = Kelas::factory()->create();
        $kelas2 = Kelas::factory()->create();

        $siswa1 = User::factory()->create(['role' => 'siswa']);
        SiswaProfile::factory()->create(['user_id' => $siswa1->id, 'kelas_id' => $kelas1->id]);
        Absensi::factory()->create(['user_id' => $siswa1->id, 'tanggal_absensi' => Carbon::now()->toDateString(), 'status' => 'hadir']);

        $siswa2 = User::factory()->create(['role' => 'siswa']);
        SiswaProfile::factory()->create(['user_id' => $siswa2->id, 'kelas_id' => $kelas2->id]);
        Absensi::factory()->create(['user_id' => $siswa2->id, 'tanggal_absensi' => Carbon::now()->toDateString(), 'status' => 'hadir']);

        $response = $this->get(route('rekap.index', ['kelas_id' => $kelas1->id, 'start_date' => Carbon::now()->startOfMonth()->toDateString(), 'end_date' => Carbon::now()->endOfMonth()->toDateString()]));

        $response->assertOk();
        $response->assertViewHas('selectedKelas', function ($selectedKelas) use ($kelas1) {
            return $selectedKelas->id === $kelas1->id;
        });
        $response->assertViewHas('studentsInClass', function ($studentsInClass) use ($siswa1, $siswa2) {
            return $studentsInClass->contains($siswa1) && !$studentsInClass->contains($siswa2);
        });
    }

    /** @test */
    public function it_can_filter_rekap_data_by_nis()
    {
        $this->actingAs($this->admin);
        $siswa = User::factory()->create(['role' => 'siswa', 'identifier' => '12345']);
        SiswaProfile::factory()->create(['user_id' => $siswa->id]);
        Absensi::factory()->create(['user_id' => $siswa->id, 'tanggal_absensi' => Carbon::now()->toDateString(), 'status' => 'hadir']);

        $response = $this->get(route('rekap.index', ['nis' => '12345', 'start_date' => Carbon::now()->startOfMonth()->toDateString(), 'end_date' => Carbon::now()->endOfMonth()->toDateString()]));

        $response->assertOk();
        $response->assertViewHas('selectedSiswa', function ($selectedSiswa) use ($siswa) {
            return $selectedSiswa->id === $siswa->id;
        });
        $response->assertViewHas('rekapData');
        $this->assertArrayHasKey('totalHadir', $response->viewData('rekapData'));
    }

    /** @test */
    public function it_handles_nis_not_found()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('rekap.index', ['nis' => '99999', 'start_date' => Carbon::now()->startOfMonth()->toDateString(), 'end_date' => Carbon::now()->endOfMonth()->toDateString()]));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Siswa dengan NIS 99999 tidak ditemukan.');
    }

    /** @test */
    public function it_can_filter_rekap_data_by_date_range()
    {
        $this->actingAs($this->admin);
        $siswa = User::factory()->create(['role' => 'siswa']);
        SiswaProfile::factory()->create(['user_id' => $siswa->id]);

        Absensi::factory()->create(['user_id' => $siswa->id, 'tanggal_absensi' => Carbon::now()->subDays(5)->toDateString(), 'status' => 'hadir']);
        Absensi::factory()->create(['user_id' => $siswa->id, 'tanggal_absensi' => Carbon::now()->subDays(10)->toDateString(), 'status' => 'sakit']);
        Absensi::factory()->create(['user_id' => $siswa->id, 'tanggal_absensi' => Carbon::now()->addDays(5)->toDateString(), 'status' => 'izin']); // Outside range

        $startDate = Carbon::now()->subDays(7)->toDateString();
        $endDate = Carbon::now()->toDateString();

        $response = $this->get(route('rekap.index', ['nis' => $siswa->identifier, 'start_date' => $startDate, 'end_date' => $endDate]));

        $response->assertOk();
        $rekapData = $response->viewData('rekapData');
        $this->assertEquals(1, $rekapData['totalHadir']);
        $this->assertEquals(0, $rekapData['totalSakit']); // Changed to 0 as it's outside the date range
        $this->assertEquals(0, $rekapData['totalIzin']); // Should not count the one outside range
    }

    /** @test */
    public function an_admin_can_export_rekap_absensi_data()
    {
        $this->actingAs($this->admin);
        
        $response = $this->get(route('rekap.export', [
            'start_date' => Carbon::now()->startOfMonth()->toDateString(),
            'end_date' => Carbon::now()->endOfMonth()->toDateString(),
        ]));

        Excel::assertDownloaded('Rekap_Absensi_Semua_Siswa_' . Carbon::now()->startOfMonth()->format('d-m-Y') . '_sampai_' . Carbon::now()->endOfMonth()->format('d-m-Y') . '.xlsx', function(RekapAbsensiExport $export) {
            // The header row is 5, so data starts from row 6.
            // We need to account for the header rows added in the export's registerEvents method.
            // The actual data rows will be the total rows minus the header rows (5 rows).
            $expectedRowCount = Absensi::whereBetween('tanggal_absensi', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
            return $export->query()->count() === $expectedRowCount;
        });
    }

    /** @test */
    public function an_admin_can_export_rekap_absensi_data_filtered_by_kelas_id()
    {
        $this->actingAs($this->admin);
        $kelas = Kelas::factory()->create();
        $siswaInKelas = User::factory()->create(['role' => 'siswa']);
        SiswaProfile::factory()->create(['user_id' => $siswaInKelas->id, 'kelas_id' => $kelas->id]);
        Absensi::factory()->count(2)->create(['user_id' => $siswaInKelas->id]);

        $siswaNotInKelas = User::factory()->create(['role' => 'siswa']);
        SiswaProfile::factory()->create(['user_id' => $siswaNotInKelas->id, 'kelas_id' => null]);
        Absensi::factory()->count(1)->create(['user_id' => $siswaNotInKelas->id]);

        $response = $this->get(route('rekap.export', [
            'kelas_id' => $kelas->id,
            'start_date' => Carbon::now()->startOfMonth()->toDateString(),
            'end_date' => Carbon::now()->endOfMonth()->toDateString(),
        ]));

        Excel::assertDownloaded('Rekap_Absensi_' . str_replace(' ', '_', $kelas->nama_kelas) . '_' . Carbon::now()->startOfMonth()->format('d-m-Y') . '_sampai_' . Carbon::now()->endOfMonth()->format('d-m-Y') . '.xlsx', function(RekapAbsensiExport $export) use ($siswaInKelas, $kelas) {
            $expectedRowCount = Absensi::whereHas('user.siswaProfile', function ($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            })->whereBetween('tanggal_absensi', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
            return $export->query()->count() === $expectedRowCount;
        });
    }

    /** @test */
    public function an_admin_can_export_rekap_absensi_data_filtered_by_nis()
    {
        $this->actingAs($this->admin);
        $siswa1 = User::factory()->create(['role' => 'siswa', 'identifier' => 'NIS001']);
        SiswaProfile::factory()->create(['user_id' => $siswa1->id]);
        Absensi::factory()->count(3)->create(['user_id' => $siswa1->id]);

        $siswa2 = User::factory()->create(['role' => 'siswa', 'identifier' => 'NIS002']);
        SiswaProfile::factory()->create(['user_id' => $siswa2->id]);
        Absensi::factory()->count(1)->create(['user_id' => $siswa2->id]);

        $response = $this->get(route('rekap.export', [
            'nis' => 'NIS001',
            'start_date' => Carbon::now()->startOfMonth()->toDateString(),
            'end_date' => Carbon::now()->endOfMonth()->toDateString(),
        ]));

        Excel::assertDownloaded('Rekap_Absensi_' . str_replace(' ', '_', $siswa1->name) . '_' . Carbon::now()->startOfMonth()->format('d-m-Y') . '_sampai_' . Carbon::now()->endOfMonth()->format('d-m-Y') . '.xlsx', function(RekapAbsensiExport $export) use ($siswa1) {
            $expectedRowCount = Absensi::whereHas('user', function ($q) use ($siswa1) {
                $q->where('identifier', $siswa1->identifier);
            })->whereBetween('tanggal_absensi', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
            return $export->query()->count() === $expectedRowCount;
        });
    }
}
