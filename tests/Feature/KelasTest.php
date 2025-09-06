<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelasTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function an_admin_can_view_kelas_index_page()
    {
        $this->actingAs($this->admin);
        Kelas::factory()->count(3)->create();

        $response = $this->get(route('kelas.index'));

        $response->assertOk();
        $response->assertViewIs('kelas.index');
        $response->assertViewHas('kelas');
    }

    /** @test */
    public function an_admin_can_create_a_new_kelas()
    {
        $this->actingAs($this->admin);
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->post(route('kelas.store'), [
            'nama_kelas' => 'Kelas 10 IPA 1',
            'wali_kelas_id' => $guru->id,
        ]);

        $response->assertRedirect(route('kelas.index'));
        $response->assertSessionHas('success', 'Kelas berhasil ditambahkan.');
        $this->assertDatabaseHas('kelas', [
            'nama_kelas' => 'Kelas 10 IPA 1',
            'wali_kelas_id' => $guru->id,
        ]);
    }

    /** @test */
    public function it_validates_unique_nama_kelas_on_store()
    {
        $this->actingAs($this->admin);
        Kelas::factory()->create(['nama_kelas' => 'Kelas Existing']);

        $response = $this->post(route('kelas.store'), [
            'nama_kelas' => 'Kelas Existing',
            'wali_kelas_id' => null,
        ]);

        $response->assertSessionHasErrors('nama_kelas');
        $this->assertCount(1, Kelas::all());
    }

    /** @test */
    public function it_validates_unique_wali_kelas_id_on_store()
    {
        $this->actingAs($this->admin);
        $guru = User::factory()->create(['role' => 'guru']);
        Kelas::factory()->create(['wali_kelas_id' => $guru->id]);

        $response = $this->post(route('kelas.store'), [
            'nama_kelas' => 'Kelas Baru',
            'wali_kelas_id' => $guru->id,
        ]);

        $response->assertInvalid(['wali_kelas_id' => 'Guru ini sudah menjadi wali kelas di kelas lain.']);
        $this->assertCount(1, Kelas::all());
    }

    /** @test */
    public function an_admin_can_view_kelas_edit_page()
    {
        $this->actingAs($this->admin);
        $kelas = Kelas::factory()->create();

        $response = $this->get(route('kelas.edit', $kelas));

        $response->assertOk();
        $response->assertViewIs('kelas.edit');
        $response->assertViewHas('kela', $kelas);
        $response->assertViewHas('gurus');
        $response->assertViewHas('siswaDiKelas');
        $response->assertViewHas('siswaTanpaKelas');
        $response->assertViewHas('allMataPelajarans');
    }

    /** @test */
    public function an_admin_can_update_a_kelas()
    {
        $this->actingAs($this->admin);
        $kelas = Kelas::factory()->create(['nama_kelas' => 'Old Name']);
        $newGuru = User::factory()->create(['role' => 'guru']);

        $response = $this->put(route('kelas.update', $kelas), [
            'nama_kelas' => 'New Name',
            'wali_kelas_id' => $newGuru->id,
            'add_siswa_ids' => '[]',
            'remove_siswa_ids' => '[]',
            'mata_pelajaran_ids' => '[]',
        ]);

        $response->assertRedirect(route('kelas.index'));
        $response->assertSessionHas('success', 'Kelas berhasil diperbarui.');
        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'nama_kelas' => 'New Name',
            'wali_kelas_id' => $newGuru->id,
        ]);
    }

    /** @test */
    public function it_validates_unique_nama_kelas_on_update()
    {
        $this->actingAs($this->admin);
        Kelas::factory()->create(['nama_kelas' => 'Kelas 1']);
        $kelasToUpdate = Kelas::factory()->create(['nama_kelas' => 'Kelas 2']);

        $response = $this->put(route('kelas.update', $kelasToUpdate), [
            'nama_kelas' => 'Kelas 1',
            'wali_kelas_id' => null,
            'add_siswa_ids' => '[]',
            'remove_siswa_ids' => '[]',
            'mata_pelajaran_ids' => '[]',
        ]);

        $response->assertSessionHasErrors('nama_kelas');
        $this->assertDatabaseHas('kelas', ['id' => $kelasToUpdate->id, 'nama_kelas' => 'Kelas 2']);
    }

    /** @test */
    public function it_validates_unique_wali_kelas_id_on_update()
    {
        $this->actingAs($this->admin);
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        Kelas::factory()->create(['wali_kelas_id' => $guru1->id]);
        $kelasToUpdate = Kelas::factory()->create(['wali_kelas_id' => null]);

        $response = $this->put(route('kelas.update', $kelasToUpdate), [
            'nama_kelas' => 'Kelas Update',
            'wali_kelas_id' => $guru1->id,
            'add_siswa_ids' => '[]',
            'remove_siswa_ids' => '[]',
            'mata_pelajaran_ids' => '[]',
        ]);

        $response->assertInvalid(['wali_kelas_id' => 'Guru ini sudah menjadi wali kelas di kelas lain.']);
        $this->assertDatabaseHas('kelas', ['id' => $kelasToUpdate->id, 'wali_kelas_id' => null]);
    }

    /** @test */
    public function an_admin_can_delete_a_kelas()
    {
        $this->actingAs($this->admin);
        $kelas = Kelas::factory()->create();

        $response = $this->delete(route('kelas.destroy', $kelas));

        $response->assertRedirect(route('kelas.index'));
        $response->assertSessionHas('success', 'Kelas berhasil dihapus.');
        $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
    }

    /** @test */
    public function it_prevents_deleting_a_kelas_with_associated_students()
    {
        $this->actingAs($this->admin);
        $kelas = Kelas::factory()->create();
        SiswaProfile::factory()->create(['kelas_id' => $kelas->id]);

        $response = $this->delete(route('kelas.destroy', $kelas));

        $response->assertRedirect(route('kelas.index'));
        $response->assertSessionHas('error', 'Gagal menghapus! Kelas masih memiliki 1 siswa. Harap kosongkan kelas terlebih dahulu.');
        $this->assertDatabaseHas('kelas', ['id' => $kelas->id]);
    }

    /** @test */
    public function an_admin_can_add_students_to_a_class()
    {
        $this->actingAs($this->admin);
        $kelas = Kelas::factory()->create();
        $siswa1 = User::factory()->create(['role' => 'siswa']);
        $siswa2 = User::factory()->create(['role' => 'siswa']);
        SiswaProfile::factory()->create(['user_id' => $siswa1->id, 'kelas_id' => null]);
        SiswaProfile::factory()->create(['user_id' => $siswa2->id, 'kelas_id' => null]);

        $response = $this->put(route('kelas.update', $kelas), [
            'nama_kelas' => $kelas->nama_kelas,
            'wali_kelas_id' => $kelas->wali_kelas_id,
            'add_siswa_ids' => json_encode([$siswa1->id, $siswa2->id]),
            'remove_siswa_ids' => '[]',
            'mata_pelajaran_ids' => '[]',
        ]);

        $response->assertRedirect(route('kelas.index'));
        $this->assertDatabaseHas('siswa_profiles', ['user_id' => $siswa1->id, 'kelas_id' => $kelas->id]);
        $this->assertDatabaseHas('siswa_profiles', ['user_id' => $siswa2->id, 'kelas_id' => $kelas->id]);
    }

    /** @test */
    public function an_admin_can_remove_students_from_a_class()
    {
        $this->actingAs($this->admin);
        $kelas = Kelas::factory()->create();
        $siswa1 = User::factory()->create(['role' => 'siswa']);
        $siswa2 = User::factory()->create(['role' => 'siswa']);
        SiswaProfile::factory()->create(['user_id' => $siswa1->id, 'kelas_id' => $kelas->id]);
        SiswaProfile::factory()->create(['user_id' => $siswa2->id, 'kelas_id' => $kelas->id]);

        $response = $this->put(route('kelas.update', $kelas), [
            'nama_kelas' => $kelas->nama_kelas,
            'wali_kelas_id' => $kelas->wali_kelas_id,
            'add_siswa_ids' => '[]',
            'remove_siswa_ids' => json_encode([$siswa1->id, $siswa2->id]),
            'mata_pelajaran_ids' => '[]',
        ]);

        $response->assertRedirect(route('kelas.index'));
        $this->assertDatabaseHas('siswa_profiles', ['user_id' => $siswa1->id, 'kelas_id' => null]);
        $this->assertDatabaseHas('siswa_profiles', ['user_id' => $siswa2->id, 'kelas_id' => null]);
    }

    /** @test */
    public function an_admin_can_sync_mata_pelajaran_for_a_class()
    {
        $this->actingAs($this->admin);
        $kelas = Kelas::factory()->create();
        $mapel1 = MataPelajaran::factory()->create();
        $mapel2 = MataPelajaran::factory()->create();

        $response = $this->put(route('kelas.update', $kelas), [
            'nama_kelas' => $kelas->nama_kelas,
            'wali_kelas_id' => $kelas->wali_kelas_id,
            'add_siswa_ids' => '[]',
            'remove_siswa_ids' => '[]',
            'mata_pelajaran_ids' => json_encode([$mapel1->id, $mapel2->id]),
        ]);

        $response->assertRedirect(route('kelas.index'));
        $this->assertCount(2, $kelas->mataPelajarans);
        $this->assertTrue($kelas->mataPelajarans->contains($mapel1));
        $this->assertTrue($kelas->mataPelajarans->contains($mapel2));

        // Test removing one
        $response = $this->put(route('kelas.update', $kelas), [
            'nama_kelas' => $kelas->nama_kelas,
            'wali_kelas_id' => $kelas->wali_kelas_id,
            'add_siswa_ids' => '[]',
            'remove_siswa_ids' => '[]',
            'mata_pelajaran_ids' => json_encode([$mapel1->id]),
        ]);

        $response->assertRedirect(route('kelas.index'));
        $kelas->refresh();
        $this->assertCount(1, $kelas->mataPelajarans);
        $this->assertTrue($kelas->mataPelajarans->contains($mapel1));
        $this->assertFalse($kelas->mataPelajarans->contains($mapel2));
    }
}
