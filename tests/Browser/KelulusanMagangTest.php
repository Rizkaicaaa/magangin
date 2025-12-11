<?php

namespace Tests\Browser;

use App\Models\EvaluasiMagangModel;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class KelulusanMagangTest extends DuskTestCase
{
    use RefreshDatabase;

    #[Test]
    #[Group('kelulusan-magang-dusk')]
    public function testMahasiswaSeesPassingResultAndCertificateLink(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $pendaftaran = Pendaftaran::factory()->create(['user_id' => $mahasiswa->id]);
        EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $pendaftaran->id,
            'hasil_evaluasi' => 'Lulus',
            'nilai_total' => 92.5,
            'file_sertifikat' => 'sertifikat/contoh-sertifikat.pdf', // Contoh path
        ]);

        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->loginAs($mahasiswa)
                ->visit(route('kelulusan-magang.index'))
                ->assertSee('Hasil Kelulusan Magang')
                ->assertSee('Selamat! Anda dinyatakan Lulus Magang')
                ->assertSee('92.5') // Cek nilai akhir langsung
                ->assertVisible('a[href*="contoh-sertifikat.pdf"]') // Cek link download terlihat
                ->assertSee('Download Sertifikat');
        });
    }

    #[Test]
    #[Group('kelulusan-magang-dusk')]
    public function testMahasiswaSeesFailingResult(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $pendaftaran = Pendaftaran::factory()->create(['user_id' => $mahasiswa->id]);
        EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $pendaftaran->id,
            'hasil_evaluasi' => 'Tidak Lulus',
            'nilai_total' => 65,
        ]);

        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->loginAs($mahasiswa)
                ->visit(route('kelulusan-magang.index'))
                ->assertSee('Hasil Kelulusan Magang')
                ->assertSee('Mohon Maaf, Anda Dinyatakan Tidak Lulus')
                ->assertSee('65') // Cek nilai akhir
                ->assertDontSee('Download Sertifikat'); // Pastikan link download tidak ada
        });
    }

    #[Test]
    #[Group('kelulusan-magang-dusk')]
    public function testMahasiswaSeesWaitingMessageWhenNotEvaluated(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        Pendaftaran::factory()->create(['user_id' => $mahasiswa->id]);

        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->loginAs($mahasiswa)
                ->visit(route('kelulusan-magang.index'))
                ->assertSee('Hasil Kelulusan Magang')
                ->assertSee('Hasil evaluasi akhir Anda sedang diproses.')
                ->assertDontSee('Download Sertifikat');
        });
    }

    #[Test]
    #[Group('kelulusan-magang-dusk')]
    public function testGuestIsRedirectedToLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit(route('kelulusan-magang.index'))
                ->assertPathIs('/login');
        });
    }
}
