<?php

namespace Tests\Feature;

use App\Models\JadwalAbsensi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JadwalAbsensiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->guru = User::factory()->create(['role' => 'guru']);
        Excel::fake(); // Mock Excel facade for export tests
    }

    #[Test]
    public function an_admin_can_view_jadwal_index_page()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('jadwal.index'));

        $response->assertStatus(200);
        $response->assertViewIs('jadwal.index');
        $response->assertViewHas('jadwalAbsensis');
        $response->assertViewHas('kelas');
        $response->assertViewHas('mataPelajaran');
        $response->assertViewHas('hariOptions');
    }

    #[Test]
    public function an_admin_can_create_a_single_jadwal_absensi()
    {
        $this->actingAs($this->admin);

        $kelas = Kelas::factory()->create();
        $mataPelajaran = MataPelajaran::factory()->create();

        $response = $this->post(route('jadwal.store'), [
            'jadwal' => [
                [
                    'kelas_id' => [$kelas->id],
                    'mata_pelajaran_id' => [$mataPelajaran->id],
                    'hari' => 'Senin',
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '09:00',
                ]
            ]
        ]);

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal absensi berhasil ditambahkan.');

        $this->assertDatabaseHas('jadwal_absensis', [
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mataPelajaran->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'guru_id' => $this->admin->id,
        ]);
    }

    #[Test]
    public function an_admin_can_create_multiple_jadwal_absensi_with_multiple_classes_and_subjects()
    {
        $this->actingAs($this->admin);

        $kelas1 = Kelas::factory()->create();
        $kelas2 = Kelas::factory()->create();
        $mapel1 = MataPelajaran::factory()->create();
        $mapel2 = MataPelajaran::factory()->create();

        $response = $this->post(route('jadwal.store'), [
            'jadwal' => [
                [
                    'kelas_id' => [$kelas1->id, $kelas2->id],
                    'mata_pelajaran_id' => [$mapel1->id],
                    'hari' => 'Senin',
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '09:00',
                ],
                [
                    'kelas_id' => [$kelas1->id],
                    'mata_pelajaran_id' => [$mapel2->id],
                    'hari' => 'Selasa',
                    'jam_mulai' => '10:00',
                    'jam_selesai' => '11:00',
                ]
            ]
        ]);

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal absensi berhasil ditambahkan.');

        $this->assertDatabaseHas('jadwal_absensis', [
            'kelas_id' => $kelas1->id,
            'mata_pelajaran_id' => $mapel1->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'guru_id' => $this->admin->id,
        ]);
        $this->assertDatabaseHas('jadwal_absensis', [
            'kelas_id' => $kelas2->id,
            'mata_pelajaran_id' => $mapel1->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'guru_id' => $this->admin->id,
        ]);
        $this->assertDatabaseHas('jadwal_absensis', [
            'kelas_id' => $kelas1->id,
            'mata_pelajaran_id' => $mapel2->id,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'guru_id' => $this->admin->id,
        ]);
        $this->assertDatabaseCount('jadwal_absensis', 3);
    }

    #[Test]
    public function it_validates_jam_selesai_is_after_jam_mulai_on_store()
    {
        $this->actingAs($this->admin);

        $kelas = Kelas::factory()->create();
        $mataPelajaran = MataPelajaran::factory()->create();

        $response = $this->post(route('jadwal.store'), [
            'jadwal' => [
                [
                    'kelas_id' => [$kelas->id],
                    'mata_pelajaran_id' => [$mataPelajaran->id],
                    'hari' => 'Senin',
                    'jam_mulai' => '09:00',
                    'jam_selesai' => '08:00', // Invalid: jam_selesai before jam_mulai
                ]
            ]
        ]);

        $response->assertSessionHasErrors('jadwal.0.jam_selesai');
        $this->assertDatabaseCount('jadwal_absensis', 0);
    }

    #[Test]
    public function an_admin_can_view_jadwal_edit_page()
    {
        $this->actingAs($this->admin);
        $jadwal = JadwalAbsensi::factory()->create();

        $response = $this->get(route('jadwal.edit', $jadwal));

        $response->assertStatus(200);
        $response->assertViewIs('jadwal.edit');
        $response->assertViewHas('jadwal', $jadwal);
        $response->assertViewHas('kelas');
        $response->assertViewHas('mataPelajaran');
        $response->assertViewHas('hariOptions');
    }

    #[Test]
    public function an_admin_can_update_a_jadwal_absensi()
    {
        $this->actingAs($this->admin);
        $jadwal = JadwalAbsensi::factory()->create();
        $newKelas = Kelas::factory()->create();
        $newMapel = MataPelajaran::factory()->create();

        $response = $this->put(route('jadwal.update', $jadwal), [
            'kelas_id' => $newKelas->id,
            'mata_pelajaran_id' => $newMapel->id,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
        ]);

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal absensi berhasil diperbarui.');

        $this->assertDatabaseHas('jadwal_absensis', [
            'id' => $jadwal->id,
            'kelas_id' => $newKelas->id,
            'mata_pelajaran_id' => $newMapel->id,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
        ]);
    }

    #[Test]
    public function an_admin_can_delete_a_jadwal_absensi()
    {
        $this->actingAs($this->admin);
        $jadwal = JadwalAbsensi::factory()->create();

        $response = $this->delete(route('jadwal.destroy', $jadwal));

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal absensi berhasil dihapus.');

        $this->assertDatabaseMissing('jadwal_absensis', ['id' => $jadwal->id]);
    }

    #[Test]
    public function an_admin_can_bulk_delete_jadwal_absensi()
    {
        $this->actingAs($this->admin);
        $jadwal1 = JadwalAbsensi::factory()->create();
        $jadwal2 = JadwalAbsensi::factory()->create();
        $jadwal3 = JadwalAbsensi::factory()->create();

        $response = $this->delete(route('jadwal.bulkDelete'), [
            'ids' => [$jadwal1->id, $jadwal2->id],
        ]);

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal absensi terpilih berhasil dihapus.');

        $this->assertDatabaseMissing('jadwal_absensis', ['id' => $jadwal1->id]);
        $this->assertDatabaseMissing('jadwal_absensis', ['id' => $jadwal2->id]);
        $this->assertDatabaseHas('jadwal_absensis', ['id' => $jadwal3->id]); // This one should remain
    }

    #[Test]
    public function an_admin_can_delete_all_jadwal_absensi()
    {
        $this->actingAs($this->admin);
        JadwalAbsensi::factory()->count(5)->create();

        $response = $this->delete(route('jadwal.deleteAll'));

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Semua jadwal absensi berhasil dihapus.');

        $this->assertDatabaseCount('jadwal_absensis', 0);
    }

    #[Test]
    public function it_filters_jadwal_by_kelas()
    {
        $this->actingAs($this->admin);

        $kelas1 = Kelas::factory()->create();
        $kelas2 = Kelas::factory()->create();

        JadwalAbsensi::factory()->create(['kelas_id' => $kelas1->id]);
        JadwalAbsensi::factory()->create(['kelas_id' => $kelas1->id]);
        JadwalAbsensi::factory()->create(['kelas_id' => $kelas2->id]);

        $response = $this->get(route('jadwal.index', ['kelas_id' => $kelas1->id]));

        $response->assertStatus(200);
        $response->assertViewHas('jadwalAbsensis', function ($jadwalAbsensis) use ($kelas1) {
            return $jadwalAbsensis->count() === 2 &&
                   $jadwalAbsensis->every(fn ($j) => $j->kelas_id === $kelas1->id);
        });
    }

    #[Test]
    public function it_filters_jadwal_by_mata_pelajaran()
    {
        $this->actingAs($this->admin);

        $mapel1 = MataPelajaran::factory()->create();
        $mapel2 = MataPelajaran::factory()->create();

        JadwalAbsensi::factory()->create(['mata_pelajaran_id' => $mapel1->id]);
        JadwalAbsensi::factory()->create(['mata_pelajaran_id' => $mapel1->id]);
        JadwalAbsensi::factory()->create(['mata_pelajaran_id' => $mapel2->id]);

        $response = $this->get(route('jadwal.index', ['mata_pelajaran_id' => $mapel1->id]));

        $response->assertStatus(200);
        $response->assertViewHas('jadwalAbsensis', function ($jadwalAbsensis) use ($mapel1) {
            return $jadwalAbsensis->count() === 2 &&
                   $jadwalAbsensis->every(fn ($j) => $j->mata_pelajaran_id === $mapel1->id);
        });
    }

    #[Test]
    public function it_filters_jadwal_by_hari()
    {
        $this->actingAs($this->admin);

        JadwalAbsensi::factory()->create(['hari' => 'Senin']);
        JadwalAbsensi::factory()->create(['hari' => 'Senin']);
        JadwalAbsensi::factory()->create(['hari' => 'Selasa']);

        $response = $this->get(route('jadwal.index', ['hari' => 'Senin']));

        $response->assertStatus(200);
        $response->assertViewHas('jadwalAbsensis', function ($jadwalAbsensis) {
            return $jadwalAbsensis->count() === 2 &&
                   $jadwalAbsensis->every(fn ($j) => $j->hari === 'Senin');
        });
    }

    #[Test]
    public function an_admin_can_export_jadwal_absensi()
    {
        $this->actingAs($this->admin);
        JadwalAbsensi::factory()->count(3)->create();

        $response = $this->get(route('jadwal.export'));

        $response->assertStatus(200);
        Excel::assertDownloaded('jadwal_absensi.xlsx', function (\App\Exports\JadwalAbsensiExport $export) {
            // You can add more specific assertions about the export data here if needed
            return true;
        });
    }
}
