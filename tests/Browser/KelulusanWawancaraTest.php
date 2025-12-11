<?php

namespace Tests\Browser;

use App\Models\Pendaftaran;
use App\Models\PenilaianWawancara;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class KelulusanWawancaraTest extends DuskTestCase
{
    use RefreshDatabase;

    #[Test]
    #[Group('kelulusan-wawancara-dusk')]
    public function testMahasiswaSeesTheirPassingResult(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $pendaftaran = Pendaftaran::factory()->create(['user_id' => $mahasiswa->id]);
        PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $pendaftaran->id,
            'nilai_rata_rata' => 85, // Nilai lulus
            'kkm' => 70,
        ]);

        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->loginAs($mahasiswa)
                ->visit(route('kelulusanwawancara.index'))
                ->assertSee('Hasil Kelulusan Wawancara')
                ->assertSee('Selamat, Anda Lulus!')
                ->assertSee('85') // Check for the score directly
                ->assertSee('Silakan tunggu pengumuman magang selanjutnya.');
        });
    }

    #[Test]
    #[Group('kelulusan-wawancara-dusk')]
    public function testMahasiswaSeesTheirFailingResult(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $pendaftaran = Pendaftaran::factory()->create(['user_id' => $mahasiswa->id]);
        PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $pendaftaran->id,
            'nilai_rata_rata' => 60, // Nilai tidak lulus
            'kkm' => 70,
        ]);

        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->loginAs($mahasiswa)
                ->visit(route('kelulusanwawancara.index'))
                ->assertSee('Hasil Kelulusan Wawancara')
                ->assertSee('Mohon Maaf, Anda Belum Lulus')
                ->assertSee('60') // Check for the score directly
                ->assertSee('Tetap semangat dan coba lagi di kesempatan berikutnya.');
        });
    }

    #[Test]
    #[Group('kelulusan-wawancara-dusk')]
    public function testMahasiswaSeesWaitingMessageWhenNotEvaluated(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        Pendaftaran::factory()->create(['user_id' => $mahasiswa->id]);

        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->loginAs($mahasiswa)
                ->visit(route('kelulusanwawancara.index'))
                ->assertSee('Hasil Kelulusan Wawancara')
                ->assertSee('Hasil wawancara Anda sedang diproses.')
                ->assertDontSee('Nilai Rata-rata');
        });
    }

    #[Test]
    #[Group('kelulusan-wawancara-dusk')]
    public function testMahasiswaCannotSeeOthersResult(): void
    {
        // Mahasiswa 1 (Lulus)
        $mahasiswa1 = User::factory()->create(['role' => 'mahasiswa']);
        $pendaftaran1 = Pendaftaran::factory()->create(['user_id' => $mahasiswa1->id]);
        PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $pendaftaran1->id,
            'nilai_rata_rata' => 85,
        ]);

        // Mahasiswa 2 (Tidak Lulus)
        $mahasiswa2 = User::factory()->create(['role' => 'mahasiswa']);
        $pendaftaran2 = Pendaftaran::factory()->create(['user_id' => $mahasiswa2->id]);
        PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $pendaftaran2->id,
            'nilai_rata_rata' => 60,
        ]);

        $this->browse(function (Browser $browser) use ($mahasiswa1) {
            $browser->loginAs($mahasiswa1)
                ->visit(route('kelulusanwawancara.index'))
                ->assertSee('Selamat, Anda Lulus!')
                ->assertSee('85')
                ->assertDontSee('Mohon Maaf, Anda Belum Lulus')
                ->assertDontSee('60');
        });
    }

    #[Test]
    #[Group('kelulusan-wawancara-dusk')]
    public function testGuestIsRedirectedToLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit(route('kelulusanwawancara.index'))
                ->assertPathIs('/login');
        });
    }
}
