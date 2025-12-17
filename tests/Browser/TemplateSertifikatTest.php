<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use App\Models\InfoOr;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;

class TemplateSertifikatTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $admin;
    protected InfoOr $infoOr;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        DB::table('users')->insert([
            'nama_lengkap' => 'Admin Test',
            'nim' => null,
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'no_telp' => '082558080440',
            'tanggal_daftar' => now()->toDateString(),
            'status' => 'aktif',
            'dinas_id' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->admin = User::where('email', 'admin@test.com')->first();

        $this->infoOr = InfoOr::create([
            'judul' => 'OR Sinergi Muda',
            'deskripsi' => 'Deskripsi test',
            'persyaratan_umum' => null,
            'tanggal_buka' => now(),
            'tanggal_tutup' => now()->addDays(30),
            'periode' => '2024/2025',
            'gambar' => null,
            'status' => 'buka',
        ]);

        if (!file_exists(base_path('tests/browser/files'))) {
            mkdir(base_path('tests/browser/files'), 0777, true);
        }
        file_put_contents(
            base_path('tests/browser/files/template.html'),
            '<html><body><h1>Sertifikat</h1></body></html>'
        );
    }

    /** @test */
    public function admin_can_access_template_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/upload-template')
                ->assertSee('Upload Template Sertifikat Magang');
        });
    }

    /** @test */
    public function page_displays_info_or_dropdown()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/upload-template')
                ->assertVisible('select[name="info_or_id"]');
        });
    }

    /** @test */
    public function upload_template_sertifikat_success()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/upload-template')
                ->type('nama_template', 'Sertifikat 2025')
                ->select('info_or_id', (string) $this->infoOr->id)
                ->attach('file_template', base_path('tests/browser/files/template.html'))
                ->press('Upload Template')
                ->pause(2000)
                ->assertSee('Template sertifikat berhasil diupload');
        });

        $this->assertDatabaseHas('template_sertifikat', [
            'nama_template' => 'Sertifikat 2025',
            'info_or_id' => $this->infoOr->id,
            'status' => 'aktif',
        ]);
    }
}