<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\EvaluasiMagangModel;
use App\Models\Dinas;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EvaluasiMagangTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $dinas;
    protected $pendaftaran;
    protected $templateSertifikat;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Buat Dinas dulu
        $this->dinas = Dinas::factory()->create([
            'id' => 1,
            'nama_dinas' => 'Dinas PSDM'
        ]);

        // User admin sebagai penilai
        $this->user = User::factory()->create([
            'dinas_id' => $this->dinas->id,
            'email' => 'penilai@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        // User mahasiswa sebagai pendaftar
        $userPendaftar = User::factory()->create([
            'nama_lengkap' => 'John Doe',
            'dinas_id' => null,
            'role' => 'mahasiswa'
        ]);

        // Buat InfoOr untuk pendaftaran
        $infoOr = \App\Models\InfoOr::factory()->create();

        // Pendaftaran dengan status lulus_wawancara menggunakan state factory
        $this->pendaftaran = Pendaftaran::factory()->lulusWawancara()->create([
            'dinas_diterima_id' => $this->dinas->id,
            'user_id' => $userPendaftar->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'info_or_id' => $infoOr->id
        ]);

        // Buat template sertifikat default untuk testing
        $this->templateSertifikat = \App\Models\TemplateSertifikatModel::factory()->create();
    }

    /**
     * Test halaman index dapat diakses
     */
    public function test_dapat_mengakses_halaman_penilaian()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/penilaian')
                    ->waitForText('Kelola Penilaian Mahasiswa Magang', 10)
                    ->assertSee('Kelola Penilaian Mahasiswa Magang')
                    ->assertSee('Buat Penilaian');
        });
    }

    /**
     * Test menampilkan empty state ketika belum ada penilaian
     */
    public function test_menampilkan_empty_state()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/penilaian')
                    ->waitFor('#empty-state', 10)
                    ->assertSee('Belum ada Data yang dimasukkan');
        });
    }

    /**
 * Test membuat penilaian baru dengan status lulus
 */
public function test_dapat_membuat_penilaian_lulus()
{
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->user)
                ->visit('/penilaian')
                ->waitFor('#create-button', 10)
                ->click('#create-button')
                ->waitFor('#create-form', 5)
                ->assertVisible('#create-form')
                ->select('select[name="pendaftaran_id"]', $this->pendaftaran->id)
                ->type('input[name="nilai_kedisiplinan"]', '85')
                ->type('input[name="nilai_kerjasama"]', '90')
                ->type('input[name="nilai_inisiatif"]', '80')
                ->type('input[name="nilai_hasil_kerja"]', '88')
                ->pause(500)
                ->press('Simpan')
                ->pause(3000);
    });

    // Verifikasi data tersimpan
    $this->assertDatabaseHas('evaluasi_magang', [
        'pendaftaran_id' => $this->pendaftaran->id,
        'penilai_id' => $this->user->id,
        'nilai_kedisiplinan' => 85,
        'nilai_kerjasama' => 90,
        'nilai_inisiatif' => 80,
        'nilai_hasil_kerja' => 88,
        'hasil_evaluasi' => 'lulus'
    ]);

    // Verifikasi status pendaftaran berubah
    $this->assertDatabaseHas('pendaftaran', [
        'id' => $this->pendaftaran->id,
        'status_pendaftaran' => 'lulus_magang'
    ]);
}
    /**
     * Test update penilaian yang sudah ada
     */
    public function test_dapat_mengupdate_penilaian()
{
    $evaluasi = EvaluasiMagangModel::create([
        'pendaftaran_id' => $this->pendaftaran->id,
        'penilai_id' => $this->user->id,
        'template_sertifikat_id' => $this->templateSertifikat->id,
        'nilai_kedisiplinan' => 70,
        'nilai_kerjasama' => 75,
        'nilai_inisiatif' => 72,
        'nilai_hasil_kerja' => 78,
        'nilai_total' => 73.75,
        'hasil_evaluasi' => 'lulus'
    ]);

    $this->browse(function (Browser $browser) use ($evaluasi) {
        $browser->loginAs($this->user)
                ->visit('/penilaian')
                ->waitFor('table tbody tr', 10)
                ->pause(500);

        // ✅ KLIK EDIT BUTTON YANG BENAR (PAKAI data-id)
        $browser->script("
            const btn = document.querySelector('.edit-button[data-id=\"{$evaluasi->id}\"]');
            if (btn) {
                btn.scrollIntoView({ block: 'center' });
                btn.click();
            }
        ");

        // ✅ TUNGGU FORM EDIT BENAR-BENAR MUNCUL
        $browser->waitFor('#edit-form:not(.hidden)', 10)
                ->assertVisible('#edit-form')
                ->pause(500);

        // ✅ CLEAR + TYPE (BIAR GA ELEMENT NOT INTERACTABLE)
        $browser->clear('#nilai_kedisiplinan_edit')
                ->type('#nilai_kedisiplinan_edit', '95')
                ->clear('#nilai_kerjasama_edit')
                ->type('#nilai_kerjasama_edit', '92')
                ->clear('#nilai_inisiatif_edit')
                ->type('#nilai_inisiatif_edit', '90')
                ->clear('#nilai_hasil_kerja_edit')
                ->type('#nilai_hasil_kerja_edit', '93')
                ->pause(500)
                ->press('Simpan')
                ->pause(3000);
    });

    // ✅ VERIFIKASI DATABASE
    $this->assertDatabaseHas('evaluasi_magang', [
        'id' => $evaluasi->id,
        'nilai_kedisiplinan' => 95,
        'nilai_kerjasama' => 92,
        'nilai_inisiatif' => 90,
        'nilai_hasil_kerja' => 93,
        'hasil_evaluasi' => 'lulus',
    ]);
}

    /**
     * Test validasi nilai tidak boleh kosong
     */
    public function test_validasi_nilai_tidak_boleh_kosong()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/penilaian')
                    ->click('#create-button')
                    ->waitFor('#create-form', 5)
                    ->select('select[name="pendaftaran_id"]', $this->pendaftaran->id)
                    // Tidak mengisi nilai
                    ->press('Simpan')
                    ->pause(1000);
            
            // Browser akan mencegah submit karena input required
            // Verifikasi form masih tampil
            $browser->assertVisible('#create-form');
        });
    }

    /**
     * Test tombol batal pada form create
     */
    public function test_dapat_membatalkan_form_create()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/penilaian')
                    ->click('#create-button')
                    ->waitFor('#create-form', 5)
                    ->assertVisible('#create-form')
                    ->click('#cancel-create')
                    ->pause(500)
                    ->assertMissing('#create-form:not(.hidden)');
        });
    }

    /**
     * Test tombol batal pada form edit
     */
    public function test_dapat_membatalkan_form_edit()
    {
        $evaluasi = EvaluasiMagangModel::create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'penilai_id' => $this->user->id,
            'template_sertifikat_id' => $this->templateSertifikat->id,
            'nilai_kedisiplinan' => 80,
            'nilai_kerjasama' => 85,
            'nilai_inisiatif' => 82,
            'nilai_hasil_kerja' => 88,
            'nilai_total' => 83.75,
            'hasil_evaluasi' => 'lulus'
        ]);

        $this->browse(function (Browser $browser) use ($evaluasi) {
            $browser->loginAs($this->user)
                    ->visit('/penilaian')
                    ->waitFor('table', 10)
                    ->pause(1000);

            // Scroll ke element dan klik
            $browser->script("
                const button = document.querySelector('.edit-button[data-id=\"{$evaluasi->id}\"]');
                if (button) {
                    button.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => button.click(), 500);
                }
            ");
            
            $browser->waitFor('#edit-form', 5)
                    ->click('#cancel-edit')
                    ->pause(500)
                    ->assertVisible('#table-state');
        });
    }

    /**
     * Test menghapus penilaian
     */
    public function test_dapat_menghapus_penilaian()
    {
        $evaluasi = EvaluasiMagangModel::create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'penilai_id' => $this->user->id,
            'template_sertifikat_id' => $this->templateSertifikat->id,
            'nilai_kedisiplinan' => 80,
            'nilai_kerjasama' => 85,
            'nilai_inisiatif' => 82,
            'nilai_hasil_kerja' => 88,
            'nilai_total' => 83.75,
            'hasil_evaluasi' => 'lulus'
        ]);

        $this->browse(function (Browser $browser) use ($evaluasi) {
            $browser->loginAs($this->user)
                    ->visit('/penilaian')
                    ->waitFor('table tbody tr', 10)
                    ->pause(1000)
                    ->screenshot('before-delete');
            
            // Klik tombol delete
            $browser->script("
                const deleteBtn = document.querySelector('.delete-button');
                if (deleteBtn) {
                    deleteBtn.scrollIntoView({ behavior: 'instant', block: 'center' });
                    deleteBtn.click();
                }
            ");
            
            $browser->pause(1500)
                    ->screenshot('after-delete-click');
            
            // Wait for SweetAlert
            $browser->waitFor('.swal2-confirm', 5)
                    ->pause(500)
                    ->screenshot('swal-visible');
            
            // Click konfirmasi dengan beberapa cara
            try {
                $browser->click('.swal2-confirm');
            } catch (\Exception $e) {
                // Fallback: gunakan JavaScript
                $browser->script("
                    const confirmBtn = document.querySelector('.swal2-confirm');
                    if (confirmBtn) confirmBtn.click();
                ");
            }
            
            $browser->pause(3000)
                    ->screenshot('after-confirm');
        });

        // Verifikasi data terhapus
        $this->assertDatabaseMissing('evaluasi_magang', [
            'id' => $evaluasi->id
        ]);
    }

    /**
     * Test perhitungan nilai total benar
     */
    public function test_perhitungan_nilai_total_benar()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/penilaian')
                    ->click('#create-button')
                    ->waitFor('#create-form', 5)
                    ->select('select[name="pendaftaran_id"]', $this->pendaftaran->id)
                    ->type('input[name="nilai_kedisiplinan"]', '80')
                    ->type('input[name="nilai_kerjasama"]', '90')
                    ->type('input[name="nilai_inisiatif"]', '70')
                    ->type('input[name="nilai_hasil_kerja"]', '100')
                    ->pause(500)
                    // Verifikasi total dihitung otomatis di form
                    ->assertInputValue('#total_nilai_create', '85.00')
                    ->press('Simpan')
                    ->pause(3000);
        });

        // Nilai total = (80 + 90 + 70 + 100) / 4 = 85
        $this->assertDatabaseHas('evaluasi_magang', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_total' => 85
        ]);
    }

    /**
     * Test menampilkan data penilaian di table
     */
    public function test_menampilkan_data_penilaian_di_table()
    {
        $evaluasi = EvaluasiMagangModel::create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'penilai_id' => $this->user->id,
            'template_sertifikat_id' => $this->templateSertifikat->id,
            'nilai_kedisiplinan' => 85,
            'nilai_kerjasama' => 90,
            'nilai_inisiatif' => 80,
            'nilai_hasil_kerja' => 88,
            'nilai_total' => 85.75,
            'hasil_evaluasi' => 'lulus'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/penilaian')
                    ->waitFor('table', 10)
                    ->assertVisible('#table-state')
                    ->assertDontSee('Belum ada Data yang dimasukkan')
                    ->assertSee($this->pendaftaran->user->nama_lengkap)
                    ->assertSee('85')
                    ->assertSee('90')
                    ->assertSee('80')
                    ->assertSee('88')
                    ->assertSee('85.75')
                    ->assertSee('Lulus');
        });
    }
}