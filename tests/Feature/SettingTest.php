<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function an_admin_can_view_the_settings_page()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('pengaturan.index'));

        $response->assertOk();
        $response->assertViewIs('pengaturan.index');
        $response->assertViewHas('settings');
    }

    /** @test */
    public function an_admin_can_update_settings()
    {
        $this->actingAs($this->admin);

        // Create some initial settings
        // Create some initial settings
        Setting::create(['key' => 'nama_sekolah', 'value' => 'Old School Name']);
        Setting::create(['key' => 'alamat_sekolah', 'value' => 'Old School Address']);
        Setting::create(['key' => 'max_absent_days', 'value' => 10]);

        $response = $this->post(route('pengaturan.update'), [
            'nama_sekolah' => 'New School Name',
            'alamat_sekolah' => 'New School Address',
            'telepon_sekolah' => '1234567890',
            'email_sekolah' => 'new@example.com',
            'kepala_sekolah' => 'New Headmaster',
            'tahun_ajaran' => '2024/2025',
        ]);

        $response->assertRedirect(); // Controller uses back()
        $response->assertSessionHas('success', 'Pengaturan berhasil diperbarui.');

        $this->assertDatabaseHas('settings', ['key' => 'nama_sekolah', 'value' => 'New School Name']);
        $this->assertDatabaseHas('settings', ['key' => 'alamat_sekolah', 'value' => 'New School Address']);
        $this->assertDatabaseHas('settings', ['key' => 'telepon_sekolah', 'value' => '1234567890']);
        $this->assertDatabaseHas('settings', ['key' => 'email_sekolah', 'value' => 'new@example.com']);
        $this->assertDatabaseHas('settings', ['key' => 'kepala_sekolah', 'value' => 'New Headmaster']);
        $this->assertDatabaseHas('settings', ['key' => 'tahun_ajaran', 'value' => '2024/2025']);
    }

    /** @test */
    public function it_validates_settings_update_request()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('pengaturan.update'), [
            'nama_sekolah' => null, // Nullable, so no error expected for empty
            'email_sekolah' => 'invalid-email', // Invalid: should be email
            'telepon_sekolah' => str_repeat('a', 21), // Invalid: max 20
            'tahun_ajaran' => str_repeat('a', 16), // Invalid: max 15
        ]);

        $response->assertSessionHasErrors(['email_sekolah', 'telepon_sekolah', 'tahun_ajaran']);
    }

    /** @test */
    public function an_admin_can_upload_and_update_school_logo_and_favicon()
    {
        $this->actingAs($this->admin);
        Storage::fake('public');

        // Create initial logo and favicon
        $oldLogoPath = 'logos/old_logo.png';
        $oldFaviconPath = 'favicons/old_favicon.png';
        Storage::disk('public')->put($oldLogoPath, 'old logo content');
        Storage::disk('public')->put($oldFaviconPath, 'old favicon content');

        Setting::create(['key' => 'logo_sekolah', 'value' => $oldLogoPath]);
        Setting::create(['key' => 'favicon_sekolah', 'value' => $oldFaviconPath]);

        // Simulate new logo upload
        $newLogo = UploadedFile::fake()->image('new_logo.png', 100, 100)->size(500);

        $response = $this->post(route('pengaturan.update'), [
            'logo_sekolah' => $newLogo,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Pengaturan berhasil diperbarui.');

        // Assert old files are deleted
        Storage::disk('public')->assertMissing($oldLogoPath);
        Storage::disk('public')->assertMissing($oldFaviconPath);

        // Assert new files exist and settings are updated
        $updatedLogoSetting = Setting::where('key', 'logo_sekolah')->first();
        $updatedFaviconSetting = Setting::where('key', 'favicon_sekolah')->first();

        $this->assertNotNull($updatedLogoSetting);
        $this->assertNotNull($updatedFaviconSetting);

        Storage::disk('public')->assertExists($updatedLogoSetting->value);
        Storage::disk('public')->assertExists($updatedFaviconSetting->value);

        // Ensure the new favicon path is different from the new logo path (copied)
        $this->assertNotEquals($updatedLogoSetting->value, $updatedFaviconSetting->value);
    }
}
