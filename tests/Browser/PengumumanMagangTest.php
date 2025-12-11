<?php

namespace Tests\Browser;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PengumumanMagangTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testMahasiswaLulusCanSeeAnnouncement()
    {
        // Create a user with 'mahasiswa' role
        $user = User::factory()->create([
            'role' => 'mahasiswa',
        ]);

        // Create a Pendaftaran record for the user with 'lulus_magang' status
        Pendaftaran::factory()->create([
            'user_id' => $user->id,
            'status_pendaftaran' => 'lulus_magang',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit(route('pengumuman.kelulusan'))
                    ->assertSee('Selamat Anda Lulus')
                    ->assertSee($user->nama_lengkap);
        });
    }

    /**
     * A Dusk test example for a student who did not pass.
     *
     * @return void
     */
    public function testMahasiswaTidakLulusCanSeeAnnouncement()
    {
        // Create a user with 'mahasiswa' role
        $user = User::factory()->create([
            'role' => 'mahasiswa',
        ]);

        // Create a Pendaftaran record for the user with 'tidak_lulus' status
        Pendaftaran::factory()->create([
            'user_id' => $user->id,
            'status_pendaftaran' => 'tidak_lulus',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit(route('pengumuman.kelulusan'))
                    ->assertSee('Mohon Maaf Anda Belum Lulus')
                    ->assertSee($user->nama_lengkap);
        });
    }
}
