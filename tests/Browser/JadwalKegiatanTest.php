<?php

namespace Tests\Browser;

use App\Models\InfoOr;
use App\Models\JadwalKegiatan;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class JadwalKegiatanTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function createSuperadmin()
    {
        return User::factory()->superadmin()->create([
            'nama_lengkap' => 'Superadmin PSDM',
            'email' => 'superadmin@example.com'
        ]);
    }

    /**
     * TEST CASE 1: Superadmin dapat menambah kegiatan baru (CREATE)
     * @test
     */
    public function superadmin_dapat_menambah_kegiatan_baru()
    {
        $superadmin = $this->createSuperadmin();
        $periode = InfoOr::factory()->aktif()->create(['periode' => '2025-1']);

        $this->browse(function (Browser $browser) use ($superadmin, $periode) {
            $browser->resize(1920, 1080)
                ->loginAs($superadmin)
                ->visit('/jadwal-kegiatan')
                ->assertSee('Kelola Jadwal Kegiatan')
                
                // 1. Pilih Periode
                ->waitFor('#periode-select')
                ->select('#periode-select', $periode->id)
                ->pause(1000)

                // 2. Buka Modal
                ->waitFor('#open-create-modal')
                ->click('#open-create-modal')
                ->waitFor('#form-modal', 10)
                ->pause(1000) // Tunggu animasi modal selesai
                
                // 3. Isi Form
                ->type('nama_kegiatan', 'Workshop Laravel Dusk')
                ->type('deskripsi_kegiatan', 'Testing otomatisasi browser')

                // Isi Date/Time via Script
                ->script([
                    "document.getElementById('tanggal_kegiatan').value = '" . now()->addDays(5)->format('Y-m-d') . "';",
                    "document.getElementById('waktu_mulai').value = '09:00';",
                    "document.getElementById('waktu_selesai').value = '12:00';",
                    "document.getElementById('tanggal_kegiatan').dispatchEvent(new Event('input'));",
                    "document.getElementById('waktu_mulai').dispatchEvent(new Event('input'));"
                ]);

            $browser->type('tempat', 'Gedung Serba Guna')
                ->pause(500);
                
            // FIX: Jangan di-chaining setelah script
            $browser->script("document.querySelector('#kegiatan-form button[type=submit]').click();");

            // 4. Validasi SweetAlert
            $browser->waitFor('.swal2-success', 10)
                ->assertSee('Berhasil')
                ->pause(500);
                
            // Klik confirm
            $browser->script("document.querySelector('.swal2-confirm').click();");
                
            // 5. Validasi Data di Tabel
            $browser->pause(1500)
                ->assertSee('Workshop Laravel Dusk')
                ->assertSee('Gedung Serba Guna');
        });
    }

    /**
     * TEST CASE 2: Superadmin dapat mengedit kegiatan (UPDATE)
     * @test
     */
    public function superadmin_dapat_mengedit_kegiatan()
    {
        $superadmin = $this->createSuperadmin();
        $periode = InfoOr::factory()->aktif()->create();

        $kegiatan = JadwalKegiatan::factory()->create([
            'info_or_id' => $periode->id,
            'nama_kegiatan' => 'Kegiatan Lama',
            'tempat' => 'Tempat Lama'
        ]);

        $this->browse(function (Browser $browser) use ($superadmin, $periode, $kegiatan) {
            $browser->resize(1920, 1080)
                ->loginAs($superadmin)
                ->visit('/jadwal-kegiatan')
                ->select('#periode-select', $periode->id)
                ->waitForText('Kegiatan Lama')
                ->pause(1000);

            // Klik tombol edit
            $browser->script("document.querySelector(\"button[onclick*='editKegiatan({$kegiatan->id})']\").click();");
                
            $browser->waitFor('#form-modal', 10)
                ->pause(1000)
                ->assertSeeIn('#modal-title', 'Edit Kegiatan')
                ->assertInputValue('nama_kegiatan', 'Kegiatan Lama')

                // Update data
                ->type('nama_kegiatan', 'Kegiatan Baru Diedit')
                ->type('tempat', 'Tempat Baru')
                ->pause(500);

            // Submit form
            $browser->script("document.querySelector('#kegiatan-form button[type=submit]').click();");

            // Validasi Success
            $browser->waitFor('.swal2-success', 10);
            
            // Tutup Alert
            $browser->script("document.querySelector('.swal2-confirm').click();");
                
            $browser->pause(1500)
                ->assertSee('Kegiatan Baru Diedit')
                ->assertSee('Tempat Baru');
        });
    }

    /**
     * TEST CASE 3: Superadmin dapat menghapus kegiatan (DELETE)
     * @test
     */
    public function superadmin_dapat_menghapus_kegiatan()
    {
        $superadmin = $this->createSuperadmin();
        $periode = InfoOr::factory()->aktif()->create();

        $kegiatan = JadwalKegiatan::factory()->create([
            'info_or_id' => $periode->id,
            'nama_kegiatan' => 'Kegiatan Dihapus'
        ]);

        $this->browse(function (Browser $browser) use ($superadmin, $periode, $kegiatan) {
            $browser->resize(1920, 1080)
                ->loginAs($superadmin)
                ->visit('/jadwal-kegiatan')
                ->select('#periode-select', $periode->id)
                ->waitForText('Kegiatan Dihapus')
                ->pause(1000);

            // Klik tombol hapus
            $browser->script("document.querySelector(\"button[onclick*='deleteKegiatan({$kegiatan->id})']\").click();");

            // Tunggu SweetAlert Konfirmasi
            $browser->waitFor('.swal2-popup', 10)
                ->pause(1000); // Tunggu animasi popup

            // Klik Ya
            $browser->script("document.querySelector('.swal2-confirm').click();");

            // Tunggu Sukses
            $browser->waitFor('.swal2-success', 10)
                ->pause(1000);
                
            // Klik OK pada sukses
            $browser->script("document.querySelector('.swal2-confirm').click();");
                
            $browser->pause(1500)
                ->assertDontSee('Kegiatan Dihapus');
        });
    }
    
    /**
     * TEST CASE 4: Validasi gagal jika jadwal bentrok (NEGATIVE TEST)
     * @test
     */
    public function validasi_gagal_jika_jadwal_bentrok_waktu()
    {
        $superadmin = User::factory()->superadmin()->create();
        $periode = InfoOr::factory()->aktif()->create();
        
        $besok = now()->addDay()->format('Y-m-d');
        JadwalKegiatan::factory()->create([
            'info_or_id' => $periode->id,
            'nama_kegiatan' => 'Kegiatan A',
            'tanggal_kegiatan' => $besok,
            'waktu_mulai' => '10:00:00',
            'waktu_selesai' => '12:00:00'
        ]);

        $this->browse(function (Browser $browser) use ($superadmin, $periode, $besok) {
            $browser->resize(1920, 1080)
                    ->loginAs($superadmin)
                    ->visit('/jadwal-kegiatan')
                    ->select('#periode-select', $periode->id)
                    ->click('#open-create-modal')
                    ->waitFor('#form-modal')
                    ->pause(1000)
                    
                    ->type('nama_kegiatan', 'Kegiatan Bentrok')
                    ->script([
                        "document.getElementById('tanggal_kegiatan').value = '$besok';",
                        "document.getElementById('waktu_mulai').value = '11:00';",
                        "document.getElementById('waktu_selesai').value = '13:00';"
                    ]);
                    
            $browser->pause(500);

            // Submit
            $browser->script("document.querySelector('#kegiatan-form button[type=submit]').click();");

            // Harapkan Error SweetAlert
            $browser->waitFor('.swal2-error', 10)
                    ->assertSee('Sudah ada kegiatan lain');
        });
    }

    /**
     * TEST CASE 5: Superadmin dapat melihat data kegiatan per periode (Filter)
     * @test
     */
    public function superadmin_dapat_melihat_data_kegiatan_per_periode()
    {
        $superadmin = User::factory()->superadmin()->create();
        
        $periode1 = InfoOr::factory()->create(['periode' => '2024-1']);
        JadwalKegiatan::factory()->count(2)->create([
            'info_or_id' => $periode1->id,
            'nama_kegiatan' => 'Kegiatan Periode Satu'
        ]);

        $periode2 = InfoOr::factory()->create(['periode' => '2025-2']);
        JadwalKegiatan::factory()->count(1)->create([
            'info_or_id' => $periode2->id,
            'nama_kegiatan' => 'Kegiatan Periode Dua'
        ]);

        $this->browse(function (Browser $browser) use ($superadmin, $periode1, $periode2) {
            $browser->resize(1920, 1080)
                    ->loginAs($superadmin)
                    ->visit('/jadwal-kegiatan')
                    
                    // Filter Periode 1
                    ->select('#periode-select', $periode1->id)
                    ->waitForText('Kegiatan Periode Satu')
                    ->assertDontSee('Kegiatan Periode Dua')
                    
                    // Filter Periode 2
                    ->select('#periode-select', $periode2->id)
                    ->pause(1000)
                    ->waitForText('Kegiatan Periode Dua')
                    ->assertDontSee('Kegiatan Periode Satu');
        });
    }

    /**
     * TEST CASE 6: Superadmin dapat melihat detail kegiatan
     * @test
     */
    public function superadmin_dapat_melihat_detail_kegiatan()
    {
        $superadmin = User::factory()->superadmin()->create();
        $periode = InfoOr::factory()->aktif()->create();
        
        $kegiatan = JadwalKegiatan::factory()->create([
            'info_or_id' => $periode->id,
            'nama_kegiatan' => 'Rapat Akbar',
            'deskripsi_kegiatan' => 'Deskripsi sangat lengkap dan detail.',
            'tempat' => 'Aula Utama'
        ]);

        $this->browse(function (Browser $browser) use ($superadmin, $periode, $kegiatan) {
            $browser->resize(1920, 1080)
                    ->loginAs($superadmin)
                    ->visit('/jadwal-kegiatan')
                    ->select('#periode-select', $periode->id)
                    ->waitForText('Rapat Akbar')
                    
                    // Klik tombol Detail (Icon Mata)
                    ->click("button[onclick='showDetail({$kegiatan->id})']")
                    
                    // Tunggu SweetAlert Detail Muncul
                    ->waitFor('.swal2-popup')
                    ->assertSee($kegiatan->nama_kegiatan)
                    ->assertSee($kegiatan->tempat)
                    ->assertSee($kegiatan->deskripsi_kegiatan)
                    
                    // Tutup detail
                    ->click('.swal2-confirm');
        });
    }

    /**
     * TEST CASE 7: Admin melihat kegiatan (Read Only)
     * @test
     */
    public function admin_dapat_melihat_kegiatan_namun_tidak_bisa_mengedit()
    {
        $admin = User::factory()->admin()->create();
        $periode = InfoOr::factory()->create();
        JadwalKegiatan::factory()->create([
            'info_or_id' => $periode->id,
            'nama_kegiatan' => 'Rapat Koordinasi'
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->resize(1920, 1080)
                    ->loginAs($admin)
                    ->visit('/jadwal-kegiatan')
                    ->assertSee('Jadwal Kegiatan Magang')
                    
                    ->waitForText('Rapat Koordinasi')
                    ->assertMissing('#open-create-modal')
                    ->assertMissing("button[title='Edit Kegiatan']")
                    ->assertMissing("button[title='Hapus Kegiatan']");
        });
    }
    
    /**
     * TEST CASE 8: Admin dapat melihat detail kegiatan
     * @test
     */
    public function admin_dapat_melihat_detail_kegiatan()
    {
        $admin = User::factory()->admin()->create();
        $periode = InfoOr::factory()->create();
        
        $kegiatan = JadwalKegiatan::factory()->create([
            'info_or_id' => $periode->id,
            'nama_kegiatan' => 'Koordinasi Admin',
            'deskripsi_kegiatan' => 'Detail khusus admin'
        ]);

        $this->browse(function (Browser $browser) use ($admin, $kegiatan) {
            $browser->resize(1920, 1080)
                    ->loginAs($admin)
                    ->visit('/jadwal-kegiatan')
                    ->waitForText('Koordinasi Admin')
                    
                    ->click("button[onclick='showDetail({$kegiatan->id})']")
                    ->waitFor('.swal2-popup')
                    ->assertSee('Detail khusus admin')
                    ->click('.swal2-confirm');
        });
    }

    /**
     * TEST CASE 9: Mahasiswa melihat jadwal sesuai periode pendaftarannya
     * @test
     */
    public function mahasiswa_melihat_jadwal_sesuai_periode_pendaftaran()
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        
        $periode1 = InfoOr::factory()->create(['judul' => 'Batch 1']);
        JadwalKegiatan::factory()->create([
            'info_or_id' => $periode1->id, 
            'nama_kegiatan' => 'Kegiatan Batch 1'
        ]);
        
        $periode2 = InfoOr::factory()->create(['judul' => 'Batch 2']);
        JadwalKegiatan::factory()->create([
            'info_or_id' => $periode2->id, 
            'nama_kegiatan' => 'Kegiatan Batch 2'
        ]);

        Pendaftaran::factory()->create([
            'user_id' => $mahasiswa->id,
            'info_or_id' => $periode1->id
        ]);

        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->resize(1920, 1080)
                    ->loginAs($mahasiswa)
                    ->visit('/jadwal-kegiatan')
                    ->assertSee('🗓️ Jadwal Kegiatan Magang')
                    ->assertMissing('#periode-select')
                    ->assertMissing('#open-create-modal')
                    ->assertMissing("button[title='Hapus Kegiatan']")
                    
                    ->waitForText('Kegiatan Batch 1')
                    ->assertDontSee('Kegiatan Batch 2');
        });
    }

    /**
     * TEST CASE 10: Mahasiswa dapat melihat detail kegiatan
     * @test
     */
    public function mahasiswa_dapat_melihat_detail_kegiatan()
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $periode = InfoOr::factory()->create();
        
        $kegiatan = JadwalKegiatan::factory()->create([
            'info_or_id' => $periode->id, 
            'nama_kegiatan' => 'Workshop Mahasiswa',
            'deskripsi_kegiatan' => 'Detail khusus mahasiswa'
        ]);

        Pendaftaran::factory()->create([
            'user_id' => $mahasiswa->id,
            'info_or_id' => $periode->id
        ]);

        $this->browse(function (Browser $browser) use ($mahasiswa, $kegiatan) {
            $browser->resize(1920, 1080)
                    ->loginAs($mahasiswa)
                    ->visit('/jadwal-kegiatan')
                    ->waitForText('Workshop Mahasiswa')
                    
                    ->assertPresent("button[onclick='showDetail({$kegiatan->id})']")
                    ->assertMissing("button[onclick='editKegiatan({$kegiatan->id})']")
                    
                    ->click("button[onclick='showDetail({$kegiatan->id})']")
                    ->waitFor('.swal2-popup')
                    ->assertSee('Detail khusus mahasiswa')
                    ->click('.swal2-confirm');
        });
    }
}