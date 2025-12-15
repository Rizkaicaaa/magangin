<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\PenilaianWawancara;
use App\Models\JadwalSeleksi;
use App\Models\Pendaftaran;
use App\Models\InfoOr;
use App\Models\Dinas;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Carbon\Carbon;

class PenilaianWawancaraTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;
    protected $infoOr;
    protected $dinas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin  = User::factory()->superadmin()->create();
        $this->infoOr = InfoOr::factory()->create([
            'judul' => 'Seleksi Magang 2024'
        ]);
        $this->dinas  = Dinas::factory()->create();
    }

    private function buatPenilaianLengkap($index = 1)
    {
        $user = User::factory()->create([
            'nama_lengkap' => "Peserta {$index}"
        ]);

        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $user->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'status_pendaftaran' => 'terdaftar',
        ]);

        $jadwal = JadwalSeleksi::factory()->create([
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $pendaftaran->id,
            'tanggal_seleksi' => Carbon::tomorrow()->format('Y-m-d'),
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '11:00:00',
        ]);

        return PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_seleksi_id' => $jadwal->id,
            'penilai_id' => $this->admin->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 80,
            'nilai_total' => 240,
            'nilai_rata_rata' => 80,
            'kkm' => 75,
            'status' => 'sudah_dinilai',
        ]);
    }

    /** TEST 01 */
    public function test_superadmin_dapat_melihat_halaman_penilaian_wawancara()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/penilaian-wawancara')
                ->assertPathIs('/penilaian-wawancara')
                ->assertSee('Penilaian Wawancara');
        });
    }

    /** TEST 02 */
    public function test_superadmin_dapat_membuat_penilaian_wawancara()
    {
        $penilaian = $this->buatPenilaianLengkap(1);

        $this->assertDatabaseHas('penilaian_wawancara', [
            'id' => $penilaian->id,
            'status' => 'sudah_dinilai',
        ]);
    }

    /** TEST 03 */
    public function test_superadmin_dapat_melihat_semua_penilaian_di_tabel()
    {
        $this->buatPenilaianLengkap(1);
        $this->buatPenilaianLengkap(2);
        $this->buatPenilaianLengkap(3);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/penilaian-wawancara')
                ->assertSee('Peserta 1')
                ->assertSee('Peserta 2')
                ->assertSee('Peserta 3');
        });
    }

    /** TEST 04 */
    public function test_superadmin_dapat_melihat_detail_penilaian()
    {
        $penilaian = $this->buatPenilaianLengkap(1);

        $this->browse(function (Browser $browser) use ($penilaian) {
            $browser->loginAs($this->admin)
                ->visit("/penilaian-wawancara/{$penilaian->id}")
                ->assertSee('Detail Penilaian Wawancara')
                ->assertSee('Peserta 1')
                ->assertSee('80');
        });
    }

    /** TEST 05 */
    public function test_superadmin_dapat_mengedit_penilaian_wawancara()
    {
        $penilaian = $this->buatPenilaianLengkap(1);

        $this->browse(function (Browser $browser) use ($penilaian) {
            $browser->loginAs($this->admin)
                ->visit("/penilaian-wawancara/{$penilaian->id}/edit")
                ->clear('nilai_komunikasi')
                ->type('nilai_komunikasi', '95')
                ->press('Update')
                ->pause(1000);
        });

        $this->assertDatabaseHas('penilaian_wawancara', [
            'id' => $penilaian->id,
            'nilai_komunikasi' => 95,
        ]);
    }

    /** TEST 06 */
    public function test_superadmin_dapat_update_status_kelulusan()
    {
        $this->buatPenilaianLengkap(1);
        $this->buatPenilaianLengkap(2);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/penilaian-wawancara')
                ->type('kkm', '75')
                ->press('Terapkan')
                ->pause(2000)
                ->waitFor('.swal2-popup', 5);
        });
    }

    /** TEST 07 */
    public function test_superadmin_dapat_menghapus_penilaian_wawancara()
    {
        $penilaian = $this->buatPenilaianLengkap(1);

        $this->browse(function (Browser $browser) use ($penilaian) {
            $browser->loginAs($this->admin)
                ->visit('/penilaian-wawancara')
                ->assertSee('Peserta 1');
            
            // Coba berbagai cara klik tombol delete
            try {
                // Cara 1: Pakai JavaScript langsung
                $browser->script("document.querySelector('form[action*=\"/penilaian-wawancara/{$penilaian->id}\"] .delete-button').click()");
            } catch (\Exception $e) {
                // Cara 2: Pakai Dusk click biasa
                $browser->click('form[action*="/penilaian-wawancara/' . $penilaian->id . '"] .delete-button');
            }
            
            $browser->pause(1000)
                // Konfirmasi SweetAlert2
                ->waitFor('.swal2-confirm', 5)
                ->click('.swal2-confirm')
                ->pause(3000) // Tunggu lebih lama untuk redirect/reload
                ->assertDontSee('Peserta 1');
        });

        // Verifikasi data hilang dari database
        $this->assertDatabaseMissing('penilaian_wawancara', [
            'id' => $penilaian->id,
        ]);
    }

    /** TEST 08*/
    public function test_tidak_bisa_menilai_peserta_yang_sama_dua_kali()
    {
        $penilaian = $this->buatPenilaianLengkap(1);
        
        // Verifikasi awal: hanya ada 1 penilaian
        $countBefore = PenilaianWawancara::where('pendaftaran_id', $penilaian->pendaftaran_id)->count();
        $this->assertEquals(1, $countBefore);

        $this->browse(function (Browser $browser) use ($penilaian) {
            $browser->loginAs($this->admin)
                ->visit('/penilaian-wawancara/create')
                ->waitForText('Tambah Penilaian Wawancara', 10);
            
            // Pilih jadwal/pewawancara
            $browser->waitFor('select[name="jadwal_seleksi_id"]', 5)
                ->select('jadwal_seleksi_id', $penilaian->jadwal_seleksi_id)
                ->pause(2000);
            
            // Pilih peserta yang sama
            $browser->waitFor('select[name="pendaftaran_id"]', 5)
                ->select('pendaftaran_id', $penilaian->pendaftaran_id);
            
            // Isi nilai berbeda
            $browser->type('nilai_komunikasi', '90')
                ->type('nilai_motivasi', '90')
                ->type('nilai_kemampuan', '90');
            
            // Submit
            $browser->press('Simpan')
                ->pause(3000);
            
            // Cek bahwa tetap di halaman create (tidak redirect ke index)
            $browser->assertPathIs('/penilaian-wawancara/create');
        });
        
        // VERIFIKASI UTAMA: Tidak ada duplikasi di database
        $countAfter = PenilaianWawancara::where('pendaftaran_id', $penilaian->pendaftaran_id)->count();
        $this->assertEquals(1, $countAfter, 'Peserta tidak boleh dinilai dua kali - database seharusnya tetap 1 record');
        
        // Verifikasi nilai tidak berubah (masih nilai awal)
        $penilaianCheck = PenilaianWawancara::where('pendaftaran_id', $penilaian->pendaftaran_id)->first();
        $this->assertEquals(80, $penilaianCheck->nilai_komunikasi, 'Nilai tidak boleh berubah karena duplikasi dicegah');
    }
}