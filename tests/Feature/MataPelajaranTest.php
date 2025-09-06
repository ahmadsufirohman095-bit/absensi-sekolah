<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MataPelajaranTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function an_admin_can_view_mata_pelajaran_index_page()
    {
        $this->actingAs($this->admin);
        MataPelajaran::factory()->count(3)->create();

        $response = $this->get(route('mata-pelajaran.index'));

        $response->assertOk();
        $response->assertViewIs('mapel.index');
        $response->assertViewHas('mapel');
    }

    /** @test */
    public function an_admin_can_create_a_new_mata_pelajaran()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('mata-pelajaran.store'), [
            'kode_mapel' => 'MP001',
            'nama_mapel' => 'Matematika',
            'deskripsi' => 'Mata pelajaran matematika dasar',
        ]);

        $response->assertRedirect(route('mata-pelajaran.index'));
        $response->assertSessionHas('success', 'Mata Pelajaran baru berhasil ditambahkan.');
        $this->assertDatabaseHas('mata_pelajarans', [
            'kode_mapel' => 'MP001',
            'nama_mapel' => 'Matematika',
        ]);
    }

    /** @test */
    public function it_validates_unique_kode_mapel_on_store()
    {
        $this->actingAs($this->admin);
        MataPelajaran::factory()->create(['kode_mapel' => 'MP001']);

        $response = $this->post(route('mata-pelajaran.store'), [
            'kode_mapel' => 'MP001',
            'nama_mapel' => 'Fisika',
            'deskripsi' => 'Mata pelajaran fisika',
        ]);

        $response->assertSessionHasErrors('kode_mapel');
        $this->assertCount(1, MataPelajaran::all());
    }

    /** @test */
    public function it_validates_unique_nama_mapel_on_store()
    {
        $this->actingAs($this->admin);
        MataPelajaran::factory()->create(['nama_mapel' => 'Matematika']);

        $response = $this->post(route('mata-pelajaran.store'), [
            'kode_mapel' => 'MP002',
            'nama_mapel' => 'Matematika',
            'deskripsi' => 'Mata pelajaran matematika lanjutan',
        ]);

        $response->assertSessionHasErrors('nama_mapel');
        $this->assertCount(1, MataPelajaran::all());
    }

    /** @test */
    public function an_admin_can_view_mata_pelajaran_edit_page()
    {
        $this->actingAs($this->admin);
        $mataPelajaran = MataPelajaran::factory()->create();

        $response = $this->get(route('mata-pelajaran.edit', $mataPelajaran));

        $response->assertOk();
        $response->assertViewIs('mapel.edit');
        $response->assertViewHas('mataPelajaran', $mataPelajaran);
        $response->assertViewHas('allGurus');
    }

    /** @test */
    public function an_admin_can_update_a_mata_pelajaran()
    {
        $this->actingAs($this->admin);
        $mataPelajaran = MataPelajaran::factory()->create(['nama_mapel' => 'Old Mapel Name']);

        $response = $this->put(route('mata-pelajaran.update', $mataPelajaran), [
            'nama_mapel' => 'New Mapel Name',
            'deskripsi' => 'Updated description',
            'guru_ids' => [],
        ]);

        $response->assertRedirect(route('mata-pelajaran.index'));
        $response->assertSessionHas('success', 'Mata Pelajaran berhasil diperbarui.');
        $this->assertDatabaseHas('mata_pelajarans', [
            'id' => $mataPelajaran->id,
            'nama_mapel' => 'New Mapel Name',
            'deskripsi' => 'Updated description',
        ]);
    }

    /** @test */
    public function it_validates_unique_nama_mapel_on_update()
    {
        $this->actingAs($this->admin);
        MataPelajaran::factory()->create(['nama_mapel' => 'Mapel 1']);
        $mataPelajaranToUpdate = MataPelajaran::factory()->create(['nama_mapel' => 'Mapel 2']);

        $response = $this->put(route('mata-pelajaran.update', $mataPelajaranToUpdate), [
            'nama_mapel' => 'Mapel 1',
            'deskripsi' => 'Some description',
            'guru_ids' => [],
        ]);

        $response->assertSessionHasErrors('nama_mapel');
        $this->assertDatabaseHas('mata_pelajarans', ['id' => $mataPelajaranToUpdate->id, 'nama_mapel' => 'Mapel 2']);
    }

    /** @test */
    public function an_admin_can_sync_gurus_for_a_mata_pelajaran()
    {
        $this->actingAs($this->admin);
        $mataPelajaran = MataPelajaran::factory()->create();
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);

        $response = $this->put(route('mata-pelajaran.update', $mataPelajaran), [
            'nama_mapel' => $mataPelajaran->nama_mapel,
            'deskripsi' => $mataPelajaran->deskripsi,
            'guru_ids' => [$guru1->id, $guru2->id],
        ]);

        $response->assertRedirect(route('mata-pelajaran.index'));
        $this->assertCount(2, $mataPelajaran->gurus);
        $this->assertTrue($mataPelajaran->gurus->contains($guru1));
        $this->assertTrue($mataPelajaran->gurus->contains($guru2));

        // Test removing one
        $response = $this->put(route('mata-pelajaran.update', $mataPelajaran), [
            'nama_mapel' => $mataPelajaran->nama_mapel,
            'deskripsi' => $mataPelajaran->deskripsi,
            'guru_ids' => [$guru1->id],
        ]);

        $response->assertRedirect(route('mata-pelajaran.index'));
        $mataPelajaran->refresh();
        $this->assertCount(1, $mataPelajaran->gurus);
        $this->assertTrue($mataPelajaran->gurus->contains($guru1));
        $this->assertFalse($mataPelajaran->gurus->contains($guru2));
    }

    /** @test */
    public function it_prevents_deleting_a_mata_pelajaran_with_associated_gurus_or_classes()
    {
        $this->actingAs($this->admin);
        $mataPelajaran = MataPelajaran::factory()->create();
        $guru = User::factory()->create(['role' => 'guru']);
        $kelas = Kelas::factory()->create();

        // Attach guru and kelas
        $mataPelajaran->gurus()->attach($guru);
        $mataPelajaran->kelas()->attach($kelas);

        $response = $this->delete(route('mata-pelajaran.destroy', $mataPelajaran));

        $response->assertRedirect(route('mata-pelajaran.index'));
        $response->assertSessionHas('error', 'Gagal menghapus! Mata pelajaran ini masih digunakan oleh 1 guru dan diajarkan di 1 kelas.');
        $this->assertDatabaseHas('mata_pelajarans', ['id' => $mataPelajaran->id]);
    }

    /** @test */
    public function an_admin_can_delete_a_mata_pelajaran_without_associations()
    {
        $this->actingAs($this->admin);
        $mataPelajaran = MataPelajaran::factory()->create();

        $response = $this->delete(route('mata-pelajaran.destroy', $mataPelajaran));

        $response->assertRedirect(route('mata-pelajaran.index'));
        $response->assertSessionHas('success', 'Mata Pelajaran berhasil dihapus.');
        $this->assertDatabaseMissing('mata_pelajarans', ['id' => $mataPelajaran->id]);
    }
}
