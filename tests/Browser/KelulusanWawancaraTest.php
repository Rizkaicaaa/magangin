<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Pendaftaran;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class KelulusanWawancaraTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * User login dapat mengakses halaman kelulusan wawancara
     */
    public function test_user_dapat_mengakses_halaman_kelulusan_wawancara()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/kelulusan-wawancara')
                ->waitForLocation('/kelulusan-wawancara', 10)
                ->assertPathIs('/kelulusan-wawancara');
        });
    }

    /**
     * Guest diarahkan ke halaman login
     */
    public function test_guest_diarahkan_ke_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/kelulusan-wawancara')
                ->waitForLocation('/login', 10)
                ->assertPathIs('/login');
        });
    }

    /**
     * User dapat melihat hasil kelulusan wawancara miliknya
     */
    public function test_user_dapat_melihat_hasil_kelulusan_wawancara()
{
    $user = User::factory()->create();

    $infoOr = \App\Models\InfoOr::factory()->create();

    $dinas = \App\Models\Dinas::factory()->create([
        'nama_dinas' => 'Dinas Kominfo',
    ]);

    $pendaftaran = Pendaftaran::factory()->create([
    'user_id' => $user->id,
    'info_or_id' => $infoOr->id,
    'pilihan_dinas_1' => $dinas->id,
    'dinas_diterima_id' => $dinas->id, // 🔥 INI KUNCI NYA
]);


    $penilaian = \App\Models\PenilaianWawancara::factory()->lulus()->create([
        'pendaftaran_id' => $pendaftaran->id,
    ]);

    $this->browse(function (Browser $browser) use ($user, $dinas) {
    $browser->loginAs($user)
        ->visit('/kelulusan-wawancara')
        ->waitForText('Kelulusan Wawancara')
        ->assertSee($dinas->nama_dinas)
        ->assertSee('Lolos');
});

}


    /**
     * User tidak melihat kelulusan milik user lain
     */
    public function test_user_tidak_melihat_kelulusan_user_lain()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $infoOr = \App\Models\InfoOr::factory()->create();
        $dinas = \App\Models\Dinas::factory()->create();

        $p1 = \App\Models\Pendaftaran::factory()->create([
            'user_id' => $user1->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);

        $p2 = \App\Models\Pendaftaran::factory()->create([
            'user_id' => $user2->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);

        \App\Models\PenilaianWawancara::factory()->lulus()->create([
            'pendaftaran_id' => $p1->id,
        ]);

        \App\Models\PenilaianWawancara::factory()->tidakLulus()->create([
            'pendaftaran_id' => $p2->id,
        ]);

        $this->browse(function (Browser $browser) use ($user1) {
            $browser->loginAs($user1)
                ->visit('/kelulusan-wawancara')
                ->assertSee('lulus');
        });
    }

    /**
     * User dapat melihat detail kelulusan wawancara per kandidat
     */
   public function test_user_dapat_melihat_detail_kelulusan()
{
    $user = User::factory()->create();

    $infoOr = \App\Models\InfoOr::factory()->create();
    $dinas = \App\Models\Dinas::factory()->create([
        'nama_dinas' => 'Dinas Kominfo',
    ]);

    $pendaftaran = \App\Models\Pendaftaran::factory()->create([
        'user_id' => $user->id,
        'info_or_id' => $infoOr->id,
        'pilihan_dinas_1' => $dinas->id,
    ]);

    \App\Models\PenilaianWawancara::factory()->lulus()->create([
        'pendaftaran_id' => $pendaftaran->id,
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/kelulusan-wawancara')
            ->waitForText('Kelulusan Wawancara')
            ->click('@lihat-detail')
            ->waitForText('Detail Nilai Wawancara')
            ->assertSee('Nilai Rata-rata');
    });
}

    /**
     * Menampilkan status tidak lulus dengan benar
     */
    public function test_menampilkan_status_tidak_lulus()
    {
        $user = User::factory()->create();

        $infoOr = \App\Models\InfoOr::factory()->create();
        $dinas = \App\Models\Dinas::factory()->create();

        $pendaftaran = \App\Models\Pendaftaran::factory()->create([
            'user_id' => $user->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);

        \App\Models\PenilaianWawancara::factory()->tidakLulus()->create([
            'pendaftaran_id' => $pendaftaran->id,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/kelulusan-wawancara')
                ->assertSee('Tidak Lolos');
        });
    }
}