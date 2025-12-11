<?php

namespace Tests\Browser;

use App\Models\Dinas;
use App\Models\EvaluasiMagangModel;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class EvaluasiMagangTest extends DuskTestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $mahasiswa;
    protected $pendaftaran;

    protected function setUp(): void
    {
        parent::setUp();

        $dinas = Dinas::factory()->create();
        $this->admin = User::factory()->create(['role' => 'admin', 'dinas_id' => $dinas->id]);
        $this->mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $this->pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'dinas_diterima_id' => $dinas->id,
            'status_pendaftaran' => 'lulus_wawancara',
        ]);
    }

    #[Test]
    #[Group('evaluasi-dusk')]
    public function testAdminCanAccessEvaluasiPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit(route('penilaian.index'))
                ->assertSee('Penilaian Magang') // Judul halaman yang lebih mungkin
                ->assertSee($this->mahasiswa->nama_lengkap); // Menggunakan atribut yang benar
        });
    }

    #[Test]
    #[Group('evaluasi-dusk')]
    public function testGuestCannotAccessEvaluasiPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit(route('penilaian.index'))
                ->assertPathIs('/login');
        });
    }

    #[Test]
    #[Group('evaluasi-dusk')]
    public function testAdminCanCreateNewEvaluationSuccessfully(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit(route('penilaian.index'))
                // Mengunjungi URL create secara langsung karena route mungkin tidak terdefinisi dengan benar
                ->visit('/penilaian/create?pendaftaran_id=' . $this->pendaftaran->id)
                ->assertSee('Form Penilaian Magang')

                // Fill the form
                ->type('nilai_kedisiplinan', '85')
                ->type('nilai_kerjasama', '90')
                ->type('nilai_inisiatif', '80')
                ->type('nilai_hasil_kerja', '95')
                ->press('Simpan')

                // Assertions after submission
                ->assertPathIs(route('penilaian.index'))
                ->assertSee('Penilaian berhasil disimpan')
                ->assertSee('87.5') // Rata-rata dari nilai di atas
                ->assertSee('Lulus');
        });
    }

    #[Test]
    #[Group('evaluasi-dusk')]
    public function testEvaluationFormShowsValidationErrors(): void
    {
        $this->browse(function (Browser $browser) {
            // Mengunjungi URL create secara langsung
            $browser->loginAs($this->admin)
                ->visit('/penilaian/create?pendaftaran_id=' . $this->pendaftaran->id)
                // Submit with empty and invalid data
                ->type('nilai_kedisiplinan', '') // Required
                ->type('nilai_kerjasama', 'bukan_angka') // Numeric
                ->type('nilai_inisiatif', '101') // Max 100
                ->type('nilai_hasil_kerja', '-10') // Min 0
                ->press('Simpan')

                // Assert we are still on the same page and see errors
                ->assertPathIs('/penilaian/create?pendaftaran_id=' . $this->pendaftaran->id)
                ->assertSee('The nilai kedisiplinan field is required.')
                ->assertSee('The nilai kerjasama field must be a number.')
                ->assertSee('The nilai inisiatif field must not be greater than 100.')
                ->assertSee('The nilai hasil kerja field must be at least 0.');
        });
    }

    #[Test]
    #[Group('evaluasi-dusk')]
    public function testAdminCanUpdateEvaluation(): void
    {
        $evaluasi = EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'penilai_id' => $this->admin->id,
            'nilai_kedisiplinan' => 70,
            'nilai_kerjasama' => 70,
            'nilai_inisiatif' => 70,
            'nilai_hasil_kerja' => 70,
            'nilai_total' => 70,
            'hasil_evaluasi' => 'Lulus',
        ]);

        $this->browse(function (Browser $browser) use ($evaluasi) {
            $browser->loginAs($this->admin)
                ->visit(route('penilaian.index'))
                ->assertSee('70')
                // Mengunjungi URL edit secara langsung karena route 'penilaian.edit' tidak ada
                ->visit('/penilaian/' . $evaluasi->id . '/edit')
                ->assertSee('Form Penilaian Magang')

                // Update one value
                ->type('nilai_kedisiplinan', '60')
                ->press('Update') // Asumsi tombol update

                ->assertPathIs(route('penilaian.index'))
                ->assertSee('Penilaian berhasil diperbarui')
                ->assertSee('67.5') // (60+70+70+70)/4
                ->assertSee('Tidak Lulus');
        });
    }

    #[Test]
    #[Group('evaluasi-dusk')]
    public function testAdminCanDeleteEvaluation(): void
    {
        $evaluasi = EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'penilai_id' => $this->admin->id,
        ]);

        $this->browse(function (Browser $browser) use ($evaluasi) {
            $browser->loginAs($this->admin)
                ->visit(route('penilaian.index'))
                ->assertSee($this->mahasiswa->nama_lengkap)

                // Menggunakan press dengan value 'Hapus' atau selector yang lebih umum
                // karena @hapus-nilai- selector tidak ada.
                // Ini mengasumsikan ada form di sekitar tombol hapus.
                ->press('Hapus')
                ->acceptDialog()

                ->assertPathIs(route('penilaian.index'))
                ->assertSee('Penilaian berhasil dihapus')
                // Memastikan nama mahasiswa tidak lagi memiliki nilai terkait (atau logikanya terhapus)
                ->assertDontSee($evaluasi->hasil_evaluasi);
        });
    }
}
