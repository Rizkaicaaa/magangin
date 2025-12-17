<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\EvaluasiMagangModel;
use App\Models\TemplateSertifikatModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class KelulusanMagangTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_dapat_mengakses_halaman_kelulusan_magang()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/kelulusan-magang')
                ->waitForLocation('/kelulusan-magang', 10)
                ->assertPathIs('/kelulusan-magang');
        });
    }

    public function test_guest_diarahkan_ke_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/kelulusan-magang')
                ->waitForLocation('/login', 10)
                ->assertPathIs('/login');
        });
    }

    public function test_user_dapat_melihat_hasil_kelulusan_magang()
    {
        $user = User::factory()->create();
        $template = TemplateSertifikatModel::factory()->create();

        $infoOr = \App\Models\InfoOr::factory()->create();
        $dinas = \App\Models\Dinas::factory()->create();

        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $user->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'dinas_diterima_id' => $dinas->id,
        ]);

        EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $pendaftaran->id,
            'template_sertifikat_id' => $template->id,
            'nilai_total' => 88.75,
            'hasil_evaluasi' => 'lulus',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/kelulusan-magang')
                ->assertSee('88.75');
        });
    }

    public function test_user_tidak_melihat_kelulusan_user_lain()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $template = TemplateSertifikatModel::factory()->create();

        $infoOr = \App\Models\InfoOr::factory()->create();
        $dinas = \App\Models\Dinas::factory()->create();

        $p1 = Pendaftaran::factory()->create([
            'user_id' => $user1->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);

        EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $p1->id,
            'template_sertifikat_id' => $template->id,
            'nilai_total' => 85.00,
            'hasil_evaluasi' => 'lulus',
        ]);

        $p2 = Pendaftaran::factory()->create([
            'user_id' => $user2->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);

        EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $p2->id,
            'template_sertifikat_id' => $template->id,
            'nilai_total' => 55.00,
            'hasil_evaluasi' => 'tidak_lulus',
        ]);

        $this->browse(function (Browser $browser) use ($user1) {
            $browser->loginAs($user1)
                ->visit('/kelulusan-magang')
                ->assertSee('85.00')
                ->assertDontSee('55.00');
        });
    }

    public function test_menampilkan_status_tidak_lulus()
    {
        $user = User::factory()->create();
        $template = TemplateSertifikatModel::factory()->create();

        $infoOr = \App\Models\InfoOr::factory()->create();
        $dinas = \App\Models\Dinas::factory()->create();

        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $user->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);

        EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $pendaftaran->id,
            'template_sertifikat_id' => $template->id,
            'nilai_total' => 61.75,
            'hasil_evaluasi' => 'tidak_lulus',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/kelulusan-magang')
                ->assertSee('61.75');
        });
    }
}
