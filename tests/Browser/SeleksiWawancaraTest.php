<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SeleksiWawancaraTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * User login dapat mengakses halaman seleksi wawancara
     */
    public function test_user_dapat_mengakses_halaman_seleksi_wawancara()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/seleksi-wawancara')
                ->waitForLocation('/seleksi-wawancara', 10)
                ->assertPathIs('/seleksi-wawancara');
        });
    }

    /**
     * Halaman seleksi wawancara dapat dibuka tanpa redirect atau crash
     */
    public function test_halaman_seleksi_wawancara_tidak_crash()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/seleksi-wawancara')
                ->waitForLocation('/seleksi-wawancara', 10)
                ->assertPathIs('/seleksi-wawancara')
                ->assertAuthenticated();
        });
    }

    /**
     * User tidak mendapat forbidden saat mengakses halaman
     */
    public function test_user_tidak_mengalami_forbidden()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/seleksi-wawancara')
                ->assertDontSee('403')
                ->assertDontSee('Forbidden');
        });
    }

    /**
     * Guest diarahkan ke halaman login
     */
    public function test_guest_diarahkan_ke_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/seleksi-wawancara')
                ->waitForLocation('/login', 10)
                ->assertPathIs('/login');
        });
    }

    /**
     * Halaman seleksi wawancara bisa direfresh
     */
    public function test_halaman_seleksi_wawancara_bisa_direfresh()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/seleksi-wawancara')
                ->waitForLocation('/seleksi-wawancara', 10)
                ->refresh()
                ->assertPathIs('/seleksi-wawancara');
        });
    }

    /**
 * User dapat melihat jadwal seleksi wawancara miliknya
 */
public function test_user_dapat_melihat_jadwal_seleksi_wawancara()
{
    $user = User::factory()->create();

    $infoOr = \App\Models\InfoOr::factory()->create([
        'judul' => 'Magang Backend',
    ]);

    $dinas = \App\Models\Dinas::factory()->create([
        'nama_dinas' => 'Diskominfo',
    ]);

    $pendaftaran = \App\Models\Pendaftaran::factory()->create([
        'user_id' => $user->id,
        'info_or_id' => $infoOr->id,
        'pilihan_dinas_1' => $dinas->id,
        'status_pendaftaran' => 'terdaftar',
    ]);

    \App\Models\JadwalSeleksi::factory()->create([
        'pendaftaran_id' => $pendaftaran->id,
        'info_or_id' => $infoOr->id,
        'tanggal_seleksi' => now()->addDay(),
        'tempat' => 'Ruang Wawancara 1',
        'pewawancara' => 'John Doe',
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/seleksi-wawancara')
            ->waitForText('Magang Backend', 10)
            ->assertSee('Magang Backend')
            ->assertSee('Ruang Wawancara 1')
            ->assertSee('John Doe');
    });
}

public function test_user_tidak_melihat_jadwal_milik_user_lain()
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

    \App\Models\JadwalSeleksi::factory()->create([
        'pendaftaran_id' => $p1->id,
        'tempat' => 'Ruang User 1',
    ]);

    \App\Models\JadwalSeleksi::factory()->create([
        'pendaftaran_id' => $p2->id,
        'tempat' => 'Ruang User 2',
    ]);

    $this->browse(function (Browser $browser) use ($user1) {
        $browser->loginAs($user1)
            ->visit('/seleksi-wawancara')
            ->assertSee('Ruang User 1')
            ->assertDontSee('Ruang User 2');
    });
}

public function test_jadwal_yang_sudah_berlalu_tidak_ditampilkan()
{
    $user = User::factory()->create();
    $infoOr = \App\Models\InfoOr::factory()->create();
    $dinas = \App\Models\Dinas::factory()->create();

    $pendaftaran = \App\Models\Pendaftaran::factory()->create([
        'user_id' => $user->id,
        'info_or_id' => $infoOr->id,
        'pilihan_dinas_1' => $dinas->id,
    ]);

    \App\Models\JadwalSeleksi::factory()->create([
        'pendaftaran_id' => $pendaftaran->id,
        'tanggal_seleksi' => now()->subDay(),
        'tempat' => 'Ruang Lama',
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/seleksi-wawancara')
            ->assertDontSee('Ruang Lama');
    });
}


}
