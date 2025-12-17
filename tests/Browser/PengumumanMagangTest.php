<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\TemplateSertifikatModel;
use App\Models\EvaluasiMagangModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PengumumanMagangTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;
    protected $dinas;
    protected $infoOr;
    protected $templateSertifikat;
    protected $evaluasiMagang;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->dinas = Dinas::first() ?? Dinas::factory()->create([
            'nama_dinas' => 'Dinas PSDM'
        ]);

        $this->admin = User::factory()->create([
            'dinas_id' => $this->dinas->id,
            'email' => 'admin_test@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'nama_lengkap' => 'Admin Test'
        ]);

        $this->infoOr = InfoOr::first() ?? InfoOr::factory()->aktif()->create();

        $this->templateSertifikat = TemplateSertifikatModel::factory()->create([
            'nama_template' => 'Template Standar 2025',
            'info_or_id' => $this->infoOr->id
        ]);

        // Buat 1 mahasiswa dengan evaluasi untuk testing
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'nama_lengkap' => 'Mahasiswa Test',
            'dinas_id' => $this->dinas->id
        ]);

        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $student->id,
            'info_or_id' => $this->infoOr->id,
            'dinas_diterima_id' => $this->dinas->id,
            'status_pendaftaran' => 'lulus_wawancara',
            'motivasi' => 'Ingin belajar'
        ]);

       $this->evaluasiMagang = EvaluasiMagangModel::factory()->create([
    'pendaftaran_id' => $pendaftaran->id,
    'template_sertifikat_id' => $this->templateSertifikat->id,
    'nilai_total' => 80,
    'hasil_evaluasi' => 'lulus'
]);

    }

    /**
     * Test halaman pengumuman menampilkan daftar evaluasi
     */
    public function test_halaman_menampilkan_daftar_evaluasi()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/pengumuman-kelulusan')
                    ->pause(2000)
                    ->waitFor('#evaluasi_id', 10)
                    ->assertVisible('#evaluasi_id')
                    ->assertSee('Mahasiswa Test');
        });
    }

    /**
     * Test halaman menampilkan daftar template sertifikat
     */
    public function test_halaman_menampilkan_daftar_template()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/pengumuman-kelulusan')
                    ->pause(2000)
                    ->waitFor('#template_id', 10)
                    ->assertVisible('#template_id')
                    ->assertSee('Template Standar 2025');
        });
    }

   public function test_form_pengumuman_tersedia_dan_bisa_diisi()
{
    $evaluasi = $this->evaluasiMagang;

    $this->browse(function (Browser $browser) use ($evaluasi) {
        $browser->loginAs($this->admin)
            ->visit('/pengumuman-kelulusan')
            ->waitFor('#evaluasi_id', 10)

            // form & field ada
            ->assertPresent('form#form-pengumuman')
            ->assertPresent('select[name="evaluasi_id"]')
            ->assertPresent('select[name="template_id"]')
            ->assertPresent('input[name="nomor_sertifikat"]')

            // bisa diisi
            ->select('evaluasi_id', (string) $evaluasi->id)
            ->select('template_id', (string) $this->templateSertifikat->id)
            ->type('nomor_sertifikat', 'M/2025/')
            ->assertInputValue('nomor_sertifikat', 'M/2025/');
    });
}

public function test_form_kosong_saat_halaman_dimuat()
{
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/pengumuman-kelulusan')
            ->waitFor('#evaluasi_id', 10)

            // Pastikan input nomor sertifikat kosong
            ->assertInputValue('nomor_sertifikat', '');
    });
}

public function test_dropdown_evaluasi_bisa_dipilih()
{
    $evaluasi = $this->evaluasiMagang;

    $this->browse(function (Browser $browser) use ($evaluasi) {
        $browser->loginAs($this->admin)
            ->visit('/pengumuman-kelulusan')
            ->waitFor('#evaluasi_id', 10)

            ->select('evaluasi_id', (string) $evaluasi->id)
            ->assertSelected('evaluasi_id', (string) $evaluasi->id);
    });
}

}