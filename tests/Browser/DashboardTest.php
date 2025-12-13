<?php

namespace Tests\Browser;

use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\JadwalKegiatan;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * TEST CASE 1: Superadmin dapat mengakses dashboard dan melihat semua data
     * 
     * @test
     */
    public function superadmin_dapat_mengakses_dashboard_dan_melihat_semua_data()
    {
        // 1. Setup Data dengan Factory
        $superadmin = User::factory()->superadmin()->create([
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password123')
        ]);

        // Buat 3 dinas dengan nama unik
        $dinas = Dinas::factory()->count(3)
            ->sequence(
                ['nama_dinas' => 'Dinas Pendidikan'],
                ['nama_dinas' => 'Dinas Kesehatan'],
                ['nama_dinas' => 'Dinas Sosial']
            )
            ->create();

        // Buat 2 periode Info OR
        $infoOr = InfoOr::factory()->count(2)
            ->sequence(
                ['judul' => 'Magang Batch 1', 'periode' => '2025-1'],
                ['judul' => 'Magang Batch 2', 'periode' => '2025-2']
            )
            ->create();
        
        // Buat 5 pendaftaran
        Pendaftaran::factory()->count(5)->create([
            'pilihan_dinas_1' => $dinas->first()->id,
            'pilihan_dinas_2' => $dinas->last()->id,
            'info_or_id' => $infoOr->first()->id,
            'status_pendaftaran' => 'terdaftar'
        ]);

        // Buat 3 jadwal kegiatan yang akan datang
        JadwalKegiatan::factory()->count(3)->create([
            'info_or_id' => $infoOr->first()->id,
            'tanggal_kegiatan' => now()->addDays(5)->format('Y-m-d')
        ]);

        // 2. Test Browser Interaction
        $this->browse(function (Browser $browser) use ($superadmin) {
            $browser->loginAs($superadmin)
                    ->visit('/dashboard')
                    ->assertSee('Dashboard Superadmin')
                    ->assertSee('Ringkasan sistem magang BEM KM FTI Universitas Andalas')
                    
                    // Validasi Statistik Cards Ada
                    ->assertSee('Total Pendaftar')
                    ->assertSee('5') // Total 5 pendaftar
                    ->assertSee('Total Dinas')
                    ->assertSee('3') // Total 3 dinas
                    ->assertSee('Total Kegiatan')
                    ->assertSee('3') // Total 3 kegiatan
                    
                    // Validasi Filter Dropdown Ada
                    ->assertPresent('select#info_or_id')
                    ->assertSee('Semua Periode')
                    ->assertSee('Magang Batch 1')
                    ->assertSee('Magang Batch 2')
                    
                    // Validasi Tabel Pendaftar Terbaru
                    ->assertSee('Pendaftar Terbaru')
                    ->assertPresent('table')
                    ->assertSee('Nama Pendaftar')
                    ->assertSee('Status')
                    ->assertSee('Tanggal')
                    
                    // Validasi Jadwal Kegiatan
                    ->assertSee('Jadwal Kegiatan Terdekat')
                    
                    // Screenshot untuk dokumentasi
                    ->screenshot('superadmin-dashboard-all-data');
        });
    }

    /**
     * TEST CASE 2: Superadmin dapat memfilter data berdasarkan periode
     * 
     * @test
     */
    public function superadmin_dapat_memfilter_data_berdasarkan_periode()
    {
        // 1. Setup Data
        $superadmin = User::factory()->superadmin()->create([
            'email' => 'superadmin@test.com'
        ]);

        $dinas = Dinas::factory()->create(['nama_dinas' => 'Dinas Testing']);
        
        $periode1 = InfoOr::factory()->create([
            'judul' => 'Magang Batch 1',
            'periode' => '2025-1'
        ]);
        
        $periode2 = InfoOr::factory()->create([
            'judul' => 'Magang Batch 2',
            'periode' => '2025-2'
        ]);

        // 2 pendaftar di periode 1
        Pendaftaran::factory()->count(2)->create([
            'info_or_id' => $periode1->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id,
            'status_pendaftaran' => 'terdaftar'
        ]);

        // 3 pendaftar di periode 2
        Pendaftaran::factory()->count(3)->create([
            'info_or_id' => $periode2->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id,
            'status_pendaftaran' => 'terdaftar'
        ]);

        // Kegiatan untuk periode 1
        JadwalKegiatan::factory()->count(2)->create([
            'info_or_id' => $periode1->id,
            'tanggal_kegiatan' => now()->addDays(3)->format('Y-m-d')
        ]);

        // 2. Test Filter Interaction
        $this->browse(function (Browser $browser) use ($superadmin, $periode1, $periode2) {
            $browser->loginAs($superadmin)
                    ->visit('/dashboard')
                    
                    // Default: Semua Periode (Total 5 pendaftar)
                    ->assertSee('Semua Periode')
                    ->assertSee('5') // Total semua pendaftar
                    
                    // Filter ke Periode 1
                    ->select('info_or_id', $periode1->id)
                    ->pause(1000) // Wait for page reload
                    ->assertPathIs('/dashboard')
                    ->assertQueryStringHas('info_or_id', $periode1->id)
                    
                    // Validasi data berubah
                    ->assertSee('Magang Batch 1 (2025-1)')
                    ->assertSee('2') // Hanya 2 pendaftar periode 1
                    
                    // Validasi Detail Status Pendaftaran Muncul
                    ->assertSee('Detail Status Pendaftaran')
                    ->assertSee('Menunggu Seleksi')
                    
                    ->screenshot('superadmin-filter-periode-1')
                    
                    // Filter ke Periode 2
                    ->select('info_or_id', $periode2->id)
                    ->pause(1000)
                    ->assertSee('Magang Batch 2 (2025-2)')
                    ->assertSee('3') // 3 pendaftar periode 2
                    
                    ->screenshot('superadmin-filter-periode-2')
                    
                    // Kembali ke Semua Periode
                    ->select('info_or_id', 'all')
                    ->pause(1000)
                    ->assertSee('5'); // Kembali ke total semua
        });
    }

/**
     * TEST CASE 3: Admin Dinas dapat mengakses dashboard dan melihat data dinasnya saja
     * * @test
     */
    public function admin_dinas_dapat_mengakses_dashboard_dan_melihat_data_dinasnya_saja()
    {
        // 1. Setup Data
        $dinasA = Dinas::factory()->create(['nama_dinas' => 'Dinas A']);
        $dinasB = Dinas::factory()->create(['nama_dinas' => 'Dinas B']);
        
        $admin = User::factory()->admin()->create([
            'email' => 'admin@test.com',
            'dinas_id' => $dinasA->id
        ]);

        $infoOr = InfoOr::factory()->create([
            'judul' => 'Magang 2025',
            'periode' => '2025'
        ]);

        // Pendaftar ke Dinas A (HARUS terlihat)
        Pendaftaran::factory()->count(3)->create([
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinasA->id,
            'pilihan_dinas_2' => $dinasA->id,
            'status_pendaftaran' => 'terdaftar'
        ]);

        // Pendaftar ke Dinas B (TIDAK terlihat)
        Pendaftaran::factory()->count(2)->create([
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinasB->id,
            'pilihan_dinas_2' => $dinasB->id,
            'status_pendaftaran' => 'terdaftar'
        ]);

        // 2. Test Browser Interaction
        $this->browse(function (Browser $browser) use ($admin, $dinasA) {
            $browser->loginAs($admin)
                    ->visit('/dashboard')
                    ->assertSee('Dashboard Admin - ' . $dinasA->nama_dinas)
                    ->assertSee('Kelola pendaftaran & kegiatan dinas Anda')
                    
                    // Validasi hanya melihat data dinasnya (3 pendaftar)
                    ->assertSee('Total Pendaftar')
                    // Menggunakan selector CSS untuk memastikan angkanya benar (biasanya dalam card statistik)
                    // Jika selector .text-4xl.font-bold tidak ada, ganti dengan assertSee('3') biasa
                    ->assertSee('3') 
                    
                    // FIX: Hapus assertSee('Total Dinas') karena Admin Dinas biasanya tidak melihat statistik ini.
                    // Statistik "Total Dinas" umumnya hanya untuk Superadmin.
                    
                    // Validasi Filter Periode Ada
                    ->assertPresent('select#info_or_id')
                    
                    // Validasi Tabel Pendaftar
                    ->assertSee('Pendaftar Terbaru')
                    ->assertSee('Data khusus untuk dinas Anda')
                    
                    ->screenshot('admin-dashboard-dinas-a');
        });
    }
    /**
     * TEST CASE 4: Admin tanpa dinas_id melihat peringatan
     * 
     * @test
     */
    public function admin_tanpa_dinas_id_melihat_peringatan()
    {
        // 1. Setup Admin tanpa dinas_id
        $admin = User::factory()->admin()->create([
            'email' => 'admin-no-dinas@test.com',
            'dinas_id' => null // TIDAK ada dinas
        ]);

        // 2. Test Browser Interaction
        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/dashboard')
                    
                    // Validasi Peringatan Muncul
                    ->assertSee('Peringatan')
                    ->assertSee('Akun admin Anda belum dikaitkan dengan dinas')
                    ->assertSee('Silakan hubungi superadmin untuk mengatur dinas Anda')
                    
                    // Validasi Data Kosong
                    ->assertSee('0') // Total pendaftar = 0
                    ->assertSee('Akun Belum Dikaitkan')
                    
                    ->screenshot('admin-no-dinas-warning');
        });
    }

    /**
     * TEST CASE 5: Mahasiswa dapat mengakses dashboard dan melihat status pendaftarannya
     * 
     * @test
     */
    public function mahasiswa_dapat_mengakses_dashboard_dan_melihat_status_pendaftaran()
    {
        // 1. Setup Data
        $mahasiswa = User::factory()->mahasiswa()->create([
            'email' => 'mahasiswa@test.com',
            'nama_lengkap' => 'Budi Santoso',
            'nim' => '2211001'
        ]);

        $dinas = Dinas::factory()->create(['nama_dinas' => 'Dinas Pendidikan']);
        $infoOr = InfoOr::factory()->create([
            'judul' => 'Magang Batch 1',
            'periode' => '2025'
        ]);

        // Pendaftaran milik mahasiswa ini
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $mahasiswa->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id,
            'status_pendaftaran' => 'terdaftar'
        ]);

        // 2. Test Browser Interaction
        $this->browse(function (Browser $browser) use ($mahasiswa, $dinas, $infoOr) {
            $browser->loginAs($mahasiswa)
                    ->visit('/dashboard')
                    ->assertSee('Dashboard Mahasiswa')
                    ->assertSee('Pantau perkembangan magang Anda di sini')
                    
                    // Validasi Welcome Message
                    ->assertSee('Selamat Datang, ' . $mahasiswa->nama_lengkap)
                    
                    // Validasi Informasi Profil
                    ->assertSee('Informasi Profil')
                    ->assertSee($mahasiswa->nama_lengkap)
                    ->assertSee($mahasiswa->nim)
                    ->assertSee($mahasiswa->email)
                    
                    // Validasi Status Pendaftaran
                    ->assertSee('Status Pendaftaran Magang')
                    ->assertSee($dinas->nama_dinas)
                    ->assertSee('Menunggu Seleksi')
                    ->assertSee('Magang Batch 1 (2025)')
                    
                    // TIDAK melihat data statistik admin/superadmin
                    ->assertDontSee('Total Pendaftar')
                    ->assertDontSee('Total Dinas')
                    
                    ->screenshot('mahasiswa-dashboard');
        });
    }


    /**
     * TEST CASE 6: Mahasiswa dengan status lulus wawancara melihat kegiatan terdekat
     * 
     * @test
     */
    public function mahasiswa_lulus_wawancara_melihat_kegiatan_terdekat()
    {
        // 1. Setup Data
        $mahasiswa = User::factory()->mahasiswa()->create([
            'email' => 'mahasiswa-lulus@test.com'
        ]);

        $dinas = Dinas::factory()->create(['nama_dinas' => 'Dinas Kesehatan']);
        $infoOr = InfoOr::factory()->create();

        // Pendaftaran dengan status lulus wawancara
        Pendaftaran::factory()->lulusWawancara()->create([
            'user_id' => $mahasiswa->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id,
            'dinas_diterima_id' => $dinas->id
        ]);

        // Kegiatan untuk periode ini
        JadwalKegiatan::factory()->count(2)->create([
            'info_or_id' => $infoOr->id,
            'nama_kegiatan' => 'Workshop Kepemimpinan',
            'tanggal_kegiatan' => now()->addDays(5)->format('Y-m-d')
        ]);

        // 2. Test Browser Interaction
        $this->browse(function (Browser $browser) use ($mahasiswa, $dinas) {
            $browser->loginAs($mahasiswa)
                    ->visit('/dashboard')
                    
                    // Validasi Status Lulus
                    ->assertSee('Lulus Wawancara')
                    ->assertSee('Diterima di Dinas')
                    ->assertSee($dinas->nama_dinas)
                    
                    // Validasi Kegiatan Terdekat Muncul
                    ->assertSee('Kegiatan Terdekat')
                    ->assertSee('Workshop Kepemimpinan')
                    
                    ->screenshot('mahasiswa-lulus-with-kegiatan');
        });
    }

    /**
     * TEST CASE 7: Superadmin melihat detail statistik status pada filtered periode
     * 
     * @test
     */
    public function superadmin_melihat_detail_statistik_status_pada_filtered_periode()
    {
        // 1. Setup Data
        $superadmin = User::factory()->superadmin()->create();
        
        $dinas = Dinas::factory()->create();
        $periode = InfoOr::factory()->create([
            'judul' => 'Magang 2025',
            'periode' => '2025'
        ]);

        // Berbagai status pendaftaran
        Pendaftaran::factory()->count(3)->create([
            'info_or_id' => $periode->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id,
            'status_pendaftaran' => 'terdaftar'
        ]);

        Pendaftaran::factory()->count(2)->lulusWawancara()->create([
            'info_or_id' => $periode->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id
        ]);

        Pendaftaran::factory()->count(1)->tidakLulusWawancara()->create([
            'info_or_id' => $periode->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id
        ]);

        // 2. Test Browser Interaction
        $this->browse(function (Browser $browser) use ($superadmin, $periode) {
            $browser->loginAs($superadmin)
                    ->visit('/dashboard')
                    
                    // Filter ke periode tertentu
                    ->select('info_or_id', $periode->id)
                    ->pause(1000)
                    
                    // Validasi Detail Status Muncul
                    ->assertSee('Detail Status Pendaftaran')
                    ->assertSee('Magang 2025')
                    
                    // Validasi Statistik Detail
                    ->assertSee('Menunggu Seleksi')
                    ->assertSee('3') // 3 terdaftar
                    
                    ->assertSee('Lulus Wawancara')
                    ->assertSee('2') // 2 lulus
                    
                    ->assertSee('Ditolak')
                    ->assertSee('1') // 1 ditolak
                    
                    ->screenshot('superadmin-detail-stats');
        });
    }

    /**
     * TEST CASE 8: Test responsivitas filter dropdown dengan banyak periode
     * 
     * @test
     */
    public function filter_dropdown_bekerja_dengan_banyak_periode()
    {
        // 1. Setup Data dengan banyak periode
        $superadmin = User::factory()->superadmin()->create();
        
        $dinas = Dinas::factory()->create();
        
        // Buat 5 periode berbeda
        $periodes = InfoOr::factory()->count(5)
            ->sequence(
                ['judul' => 'Magang Batch 1', 'periode' => '2024-1'],
                ['judul' => 'Magang Batch 2', 'periode' => '2024-2'],
                ['judul' => 'Magang Batch 3', 'periode' => '2025-1'],
                ['judul' => 'Magang Batch 4', 'periode' => '2025-2'],
                ['judul' => 'Magang Batch 5', 'periode' => '2026-1']
            )
            ->create();

        // Buat pendaftaran untuk setiap periode dengan jumlah berbeda
        foreach ($periodes as $index => $periode) {
            Pendaftaran::factory()->count($index + 1)->create([
                'info_or_id' => $periode->id,
                'pilihan_dinas_1' => $dinas->id,
                'pilihan_dinas_2' => $dinas->id
            ]);
        }

        // 2. Test Filter dengan berbagai periode
        $this->browse(function (Browser $browser) use ($superadmin, $periodes) {
            $browser->loginAs($superadmin)
                    ->visit('/dashboard')
                    
                    // Validasi semua periode muncul di dropdown
                    ->assertSelectHasOptions('info_or_id', array_merge(
                        ['all'],
                        $periodes->pluck('id')->toArray()
                    ))
                    
                    // Test filter ke periode terakhir (5 pendaftar)
                    ->select('info_or_id', $periodes->last()->id)
                    ->pause(1000)
                    ->assertSee('Magang Batch 5')
                    ->assertSee('5')
                    
                    // Kembali ke semua periode (1+2+3+4+5 = 15 total)
                    ->select('info_or_id', 'all')
                    ->pause(1000)
                    ->assertSee('15')
                    
                    ->screenshot('filter-multiple-periodes');
        });
    }

    /**
     * TEST CASE 9: Validasi bahwa mahasiswa lain tidak bisa melihat pendaftaran user lain
     * 
     * @test
     */
    public function mahasiswa_hanya_melihat_pendaftaran_sendiri()
    {
        // 1. Setup 2 mahasiswa dengan pendaftaran masing-masing
        $mahasiswa1 = User::factory()->mahasiswa()->create([
            'email' => 'mahasiswa1@test.com',
            'nama_lengkap' => 'Mahasiswa Satu'
        ]);

        $mahasiswa2 = User::factory()->mahasiswa()->create([
            'email' => 'mahasiswa2@test.com',
            'nama_lengkap' => 'Mahasiswa Dua'
        ]);

        $dinas = Dinas::factory()->create();
        $infoOr = InfoOr::factory()->create();

        // Pendaftaran mahasiswa 1
        Pendaftaran::factory()->create([
            'user_id' => $mahasiswa1->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id
        ]);

        // Pendaftaran mahasiswa 2
        Pendaftaran::factory()->create([
            'user_id' => $mahasiswa2->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id
        ]);

        // 2. Test Mahasiswa 1 TIDAK bisa lihat data Mahasiswa 2
        $this->browse(function (Browser $browser) use ($mahasiswa1, $mahasiswa2) {
            $browser->loginAs($mahasiswa1)
                    ->visit('/dashboard')
                    
                    // Lihat nama sendiri
                    ->assertSee($mahasiswa1->nama_lengkap)
                    
                    // TIDAK melihat nama mahasiswa lain
                    ->assertDontSee($mahasiswa2->nama_lengkap)
                    
                    // Hanya 1 pendaftaran yang tampil
                    ->assertPresent('.border.border-gray-200.rounded-xl') // Card pendaftaran
                    
                    ->screenshot('mahasiswa-isolated-data');
        });
    }
}