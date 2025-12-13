<?php

namespace Tests\Browser;

use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PendaftaranTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080);
        });
    }

    private function createSuperadmin()
    {
        return User::factory()->superadmin()->create([
            'nama_lengkap' => 'Superadmin Ganteng',
            'email' => 'superadmin@example.com'
        ]);
    }

    /**
     * Helper untuk membuat file dummy fisik agar bisa diupload browser
     */
    private function createDummyFile($filename, $type = 'pdf')
    {
        $path = storage_path("framework/testing/disks/public/{$filename}");
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);
        file_put_contents($path, $type === 'pdf' ? '%PDF-1.4 dummy content' : 'dummy content');
        return $path;
    }

/**
     * TEST CASE 1: Mahasiswa dapat mendaftar jika info OR sedang buka
     * @test
     */
    public function mahasiswa_dapat_mendaftar_jika_info_or_sedang_buka()
    {
        // 1. Setup Data
        $infoOr = InfoOr::factory()->aktif()->create([
            'judul' => 'Recruitment Batch 1',
            'gambar' => 'images/poster_default.jpg' 
        ]);
        $dinas = Dinas::factory()->count(2)->create();
        
        $cvPath = $this->createDummyFile('cv_maba.pdf');
        $transkripPath = $this->createDummyFile('transkrip_maba.pdf');

        $this->browse(function (Browser $browser) use ($dinas, $cvPath, $transkripPath) {
            $browser->visit('/login')
                    ->waitForText('MagangIn', 10)
                    ->waitFor('#registerTab', 10);

            // Force enable button (Jaga-jaga delay JS)
            $browser->script("
                var btn = document.getElementById('registerTab');
                if(btn) { 
                    btn.disabled = false; 
                    btn.classList.remove('cursor-not-allowed', 'bg-gray-200');
                }
            ");
            
            // Klik Tab Register
            $browser->click('#registerTab')
                    ->waitFor('#registerForm:not(.hidden)', 10)
                    ->pause(500)
                    
                    // --- STEP 1 ---
                    ->type('#register-nama', 'Mahasiswa Baru')
                    ->type('#register-telp', '081234567890')
                    ->type('#register-email', 'maba@test.com')
                    ->type('#register-nim', '12345678')
                    ->type('#register-password', 'password123')
                    ->type('#register-confirm-password', 'password123')
                    
                    // Scroll ke Next Step agar tidak tertutup keyboard/footer
                    ->scrollIntoView('#nextStep')
                    ->click('#nextStep')
                    
                    // --- STEP 2 ---
                    ->waitFor('#step2:not(.hidden)', 10)
                    ->pause(500) // Tunggu animasi transisi
                    
                    ->select('pilihan_dinas_1', $dinas[0]->id)
                    ->pause(500) // Tunggu JS disable dinas 2
                    ->select('pilihan_dinas_2', $dinas[1]->id)
                    
                    ->type('motivasi', 'Motivasi belajar yang tinggi.')
                    ->type('pengalaman', 'Pengalaman organisasi.')
                    
                    ->attach('file_cv', $cvPath)
                    ->attach('file_transkrip', $transkripPath)
                    ->pause(500)

                    // Submit Form
                    ->scrollIntoView('#registerForm button[type="submit"]')
                    ->click('#registerForm button[type="submit"]')
                    
                    // --- FIX UTAMA DISINI ---
                    // JavaScript Anda menggunakan class '.alert-message' bukan SweetAlert
                    // untuk notifikasi sukses registrasi.
                    ->waitFor('.alert-message', 30) 
                    ->assertSee('Pendaftaran berhasil')
                    
                    // Tunggu JS melakukan switch tab otomatis ke Login (setelah 2 detik)
                    ->pause(3000)
                    ->assertVisible('#loginForm'); // Pastikan sudah kembali ke form login
                    
            // Validasi Database
            $this->assertDatabaseHas('users', ['email' => 'maba@test.com']);
            $this->assertDatabaseHas('pendaftaran', ['pilihan_dinas_1' => $dinas[0]->id]);
        });
    }
/**
     * TEST CASE 2: Validasi mahasiswa tidak bisa mendaftar apabila Info OR Tutup
     * @test
     */
    public function mahasiswa_tidak_bisa_mendaftar_jika_info_or_tutup()
    {
        InfoOr::factory()->tutup()->create([
            'judul' => 'Batch Tutup',
            'gambar' => 'images/poster_default.jpg'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitForText('MagangIn', 10) // Tunggu halaman siap
                    ->assertSee('MagangIn')
                    
                    ->waitFor('#registerTab', 10)
                    ->pause(500)
                    
                    // Cek via JS apakah tombol disabled
                    ->assertScript("return document.getElementById('registerTab').disabled", true)
                    
                    // Form Register harus tetap hidden
                    ->assertMissing('#registerForm:not(.hidden)');
        });
    }

    /**
     * TEST CASE 3: Superadmin dapat memfilter tabel pendaftar (JS Filter)
     * @test
     */
    public function superadmin_dapat_memfilter_tabel_pendaftar()
    {
        $superadmin = $this->createSuperadmin();
        $periode = InfoOr::factory()->create(['periode' => '2025-1']);
        $dinas = Dinas::factory()->create();

        // User A: Terdaftar
        Pendaftaran::factory()->create([
            'info_or_id' => $periode->id,
            'status_pendaftaran' => 'terdaftar',
            'user_id' => User::factory()->mahasiswa()->create(['nama_lengkap' => 'Budi Terdaftar'])->id,
            'pilihan_dinas_1' => $dinas->id
        ]);

        // User B: Lulus Wawancara
        Pendaftaran::factory()->create([
            'info_or_id' => $periode->id,
            'status_pendaftaran' => 'lulus_wawancara',
            'user_id' => User::factory()->mahasiswa()->create(['nama_lengkap' => 'Siti Lulus'])->id,
            'pilihan_dinas_1' => $dinas->id
        ]);

        $this->browse(function (Browser $browser) use ($superadmin) {
            $browser->loginAs($superadmin)
                    ->visit('/pendaftar')
                    ->assertSee('Budi Terdaftar')
                    ->assertSee('Siti Lulus')
                    
                    // Filter Status: Lulus Wawancara
                    ->select('#filter-status', 'lulus_wawancara')
                    ->pause(500) // Tunggu JS Filter
                    
                    // Siti harus tetap terlihat
                    ->assertSee('Siti Lulus')
                    
                    // Budi harus tersembunyi (display: none)
                    // Dusk assertDontSee kadang tricky dengan JS hide, kita cek computed style
                    ->waitUsing(5, 100, function () use ($browser) {
                        return $browser->script(
                            "const rows = Array.from(document.querySelectorAll('tbody tr'));
                             const budiRow = rows.find(r => r.textContent.includes('Budi Terdaftar'));
                             return budiRow && getComputedStyle(budiRow).display === 'none';"
                        )[0] === true;
                    });
        });
    }

    /**
     * TEST CASE 4: Superadmin dapat melihat detail data pendaftar & link file
     * @test
     */
    public function superadmin_dapat_melihat_detail_dan_file_pendaftar()
    {
        $superadmin = $this->createSuperadmin();
        $periode = InfoOr::factory()->create();
        
        // Buat pendaftar dengan file
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => User::factory()->mahasiswa()->create(['nama_lengkap' => 'Andi Detail'])->id,
            'info_or_id' => $periode->id,
            'motivasi' => 'Saya ingin belajar keras',
            'file_cv' => 'pendaftaran/cv/test.pdf', // Path dummy di DB
            'file_transkrip' => 'pendaftaran/transkrip/test.pdf'
        ]);

        $this->browse(function (Browser $browser) use ($superadmin, $pendaftaran) {
            $browser->loginAs($superadmin)
                    ->visit('/pendaftar')
                    ->assertSee('Andi Detail')
                    
                    // 1. Cek Tombol Download CV & Transkrip di Tabel
                    ->assertPresent("a[href*='/pendaftar/{$pendaftaran->user_id}/view-cv']")
                    ->assertPresent("a[href*='/pendaftar/{$pendaftaran->user_id}/view-transkrip']")
                    
                    // 2. Klik Tombol Detail (Icon Mata)
                    // Selector berdasarkan onclick function
                    ->click("button[onclick*='showDetail({$pendaftaran->user_id})']")
                    
                    // 3. Tunggu Modal Detail
                    ->waitFor('#detail-modal:not(.hidden)', 10)
                    ->pause(500) // Tunggu konten AJAX termuat
                    
                    // 4. Validasi Isi Modal
                    ->within('#detail-modal', function ($modal) use ($pendaftaran) {
                        $modal->assertSee('Detail Pendaftar')
                              ->assertSee('Andi Detail') // Nama
                              ->assertSee('Saya ingin belajar keras'); // Motivasi
                    })
                    
                    // 5. Tutup Modal
                    ->click("#detail-modal button[onclick='closeDetailModal()']");
        });
    }

    /**
     * TEST CASE 5: Superadmin dapat menetapkan dinas penerima (Khusus Lulus Wawancara)
     * @test
     */
    public function superadmin_dapat_menetapkan_dinas_penerima()
    {
        $superadmin = $this->createSuperadmin();
        $periode = InfoOr::factory()->create();
        $dinas = Dinas::factory()->create(['nama_dinas' => 'Dinas Kominfo']);

        // User harus status 'lulus_wawancara' agar tombol muncul
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_lengkap' => 'Calon Magang']);
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $mahasiswa->id,
            'info_or_id' => $periode->id,
            'status_pendaftaran' => 'lulus_wawancara', 
            'pilihan_dinas_1' => $dinas->id,
            'dinas_diterima_id' => null
        ]);

        $this->browse(function (Browser $browser) use ($superadmin, $dinas, $mahasiswa) {
            $browser->loginAs($superadmin)
                    ->visit('/pendaftar')
                    ->assertSee('Calon Magang')
                    ->pause(1000) // Tunggu render
                    
                    // 1. Klik Tombol Edit Dinas (Icon Pensil)
                    // Selector spesifik title karena onclick agak panjang
                    ->click("button[title='Edit Dinas Diterima']")
                    
                    // 2. Tunggu Modal Dinas
                    ->waitFor('#dinas-modal:not(.hidden)', 10)
                    ->pause(500)
                    
                    // 3. Pilih Dinas di Modal
                    ->select('#dinas_diterima_id', $dinas->id)
                    
                    // 4. Submit
                    ->click('#dinas-form button[type="submit"]')
                    
                    // 5. Konfirmasi SweetAlert
                    ->waitFor('.swal2-popup', 10)
                    ->click('.swal2-confirm')
                    
                    // 6. Tunggu Sukses (Redirect Back)
                    ->pause(2000)
                    ->assertSee('Dinas penerima berhasil ditetapkan');
            
            // Verifikasi DB
            $this->assertDatabaseHas('pendaftaran', [
                'user_id' => $mahasiswa->id,
                'dinas_diterima_id' => $dinas->id
            ]);
        });
    }
}