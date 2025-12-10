<?php

namespace Tests\Feature;

use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\JadwalKegiatan;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini


class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup data dasar yang dibutuhkan (opsional, bisa juga per method)
    }

    /**
     * TEST CASE 1: Superadmin dapat mengakses dashboard
     */
    public function test_superadmin_mengakses_dashboard(): void
    {
        // 1. Setup Data
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        
        // Buat Dinas dengan nama unik menggunakan sequence agar tidak bentrok (Unique Constraint)
        $dinasList = Dinas::factory()->count(3)
            ->sequence(fn ($sequence) => ['nama_dinas' => 'Dinas Test ' . $sequence->index])
            ->create();
            
        InfoOr::factory()->count(2)->create();
        
        // Buat Pendaftaran menggunakan Dinas yang SUDAH ADA.
        Pendaftaran::factory()->count(5)->create([
            'pilihan_dinas_1' => $dinasList->first()->id,
            // Jika factory pendaftaran mengisi pilihan_dinas_2, kita override juga biar aman
            'pilihan_dinas_2' => $dinasList->last()->id, 
        ]);

        // 2. Akses Dashboard
        $response = $this->actingAs($superadmin)
                         ->get('/dashboard');

        // 3. Assertions
        $response->assertStatus(200)
                 ->assertViewIs('dashboard')
                 ->assertViewHasAll([
                     'totalPendaftar',
                     'totalDinas',
                     'totalInfo',
                     'showFilter' => true 
                 ]);
        
        // Pastikan angkanya sesuai (5 pendaftar, 3 dinas, 2 info)
        $this->assertEquals(5, $response->viewData('totalPendaftar'));
        $this->assertEquals(3, $response->viewData('totalDinas'));
    }

    /**
     * TEST CASE 2: Admin Dinas mengakses dashboard dan melihat data yang relevan dengan dinasnya.
     */
   public function test_admin_dinas_mengakses_dashboard_dengan_data_pendaftar_dinasnya_saja(): void
    {
        // 1. Setup Dinas dengan nama unik
        // Pastikan nama dinas benar-benar unik dan tidak bentrok (ini sudah OK)
        $dinasKita = Dinas::factory()->create(['nama_dinas' => 'Dinas A Unique']);
        $dinasLain = Dinas::factory()->create(['nama_dinas' => 'Dinas B Unique']);

        // 2. Setup Admin untuk Dinas A
        $admin = User::factory()->create([
            'role' => 'admin',
            'dinas_id' => $dinasKita->id
        ]);

        // 3. Setup Pendaftaran
        $infoOr = InfoOr::factory()->create();

        // Pendaftar ke Dinas A (HARUS terhitung = 1)
        Pendaftaran::factory()->create([
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinasKita->id,
            // Tambahkan pilihan_dinas_2 ke dinas KITA juga (atau null)
            // agar factory tidak membuat dinas baru/bentrok.
            'pilihan_dinas_2' => $dinasKita->id, 
            'status_pendaftaran' => 'terdaftar'
        ]);

        // Pendaftar ke Dinas B (TIDAK terhitung oleh Admin Dinas A)
        Pendaftaran::factory()->create([
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinasLain->id,
            // Tambahkan pilihan_dinas_2 ke dinas LAIN juga (atau null)
            'pilihan_dinas_2' => $dinasLain->id, 
            'status_pendaftaran' => 'terdaftar'
        ]);
        
        // **OPSIONAL**: Tambahkan pendaftar yang TIDAK memilih dinas KITA sama sekali.
        // Pendaftar ke Dinas B (Pilihan 1) dan Pilihan C (Pilihan 2)
        // Ini lebih memperkuat bahwa filter berjalan
        $dinasC = Dinas::factory()->create(['nama_dinas' => 'Dinas C Unique']);
        Pendaftaran::factory()->create([
             'info_or_id' => $infoOr->id,
             'pilihan_dinas_1' => $dinasLain->id,
             'pilihan_dinas_2' => $dinasC->id,
             'status_pendaftaran' => 'terdaftar'
        ]);


        // 4. Akses Dashboard
        $response = $this->actingAs($admin)
                         ->get('/dashboard');

        // 5. Assertions
        $response->assertStatus(200);

        // Admin harusnya melihat totalPendaftar = 1 
        // (yaitu pendaftar yang memilih dinas KITA pada pilihan 1 atau 2, 
        // tapi berdasarkan setup di atas, HANYA 1 yang memilih dinasKita di pilihan 1)
        $this->assertEquals(1, $response->viewData('totalPendaftar'));
        
        // Admin melihat totalDinas = 1 (Karena hanya melihat datanya sendiri)
        $this->assertEquals(1, $response->viewData('totalDinas'));
    }

    /**
     * TEST CASE 3: Mahasiswa mengakses dashboard dengan riwayat pendaftarannya sendiri.
     */
    public function test_mahasiswa_mengakses_dashboard_dengan_riwayat_pendaftaran_sendiri(): void
    {
        // 1. Setup User Mahasiswa
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $mahasiswaLain = User::factory()->create(['role' => 'mahasiswa']);

        // 2. Setup Data Dasar (Dinas & Info)
        $infoOr = InfoOr::factory()->create();
        // Beri nama unik agar tidak bentrok dengan test lain atau factory default
        $dinas = Dinas::factory()->create(['nama_dinas' => 'Dinas Mahasiswa Test']);

        // Pendaftaran milik mahasiswa ini
        $pendaftaranMilikSendiri = Pendaftaran::factory()->create([
            'user_id' => $mahasiswa->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            // Override field lain yang mungkin memicu factory Dinas otomatis
            'pilihan_dinas_2' => $dinas->id, 
        ]);

        // Pendaftaran milik orang lain
        Pendaftaran::factory()->create([
            'user_id' => $mahasiswaLain->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id,
        ]);

        // 3. Akses Dashboard
        $response = $this->actingAs($mahasiswa)
                         ->get('/dashboard');

        // 4. Assertions
        $response->assertStatus(200)
                 ->assertViewHas('pendaftaranUser');

        $pendaftaranDiView = $response->viewData('pendaftaranUser');
        $this->assertCount(1, $pendaftaranDiView);
        $this->assertEquals($pendaftaranMilikSendiri->id, $pendaftaranDiView->first()->id);
        
        $response->assertViewMissing('totalPendaftar');
    }

    /**
     * TEST CASE 4: Superadmin Filter Data Berdasarkan Info OR (Periode).
     */
    public function test_superadmin_filter_data_berdasarkan_periode(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $periode1 = InfoOr::factory()->create(['judul' => 'Periode 1']);
        $periode2 = InfoOr::factory()->create(['judul' => 'Periode 2']);
        
        // Buat satu Dinas safe untuk dipakai semua pendaftaran di test ini
        $dinasSafe = Dinas::factory()->create(['nama_dinas' => 'Dinas Filter Test']);

        // 2 Pendaftar di Periode 1
        Pendaftaran::factory()->count(2)->create([
            'info_or_id' => $periode1->id,
            'pilihan_dinas_1' => $dinasSafe->id,
            'pilihan_dinas_2' => $dinasSafe->id
        ]);
        
        // 3 Pendaftar di Periode 2
        Pendaftaran::factory()->count(3)->create([
            'info_or_id' => $periode2->id,
            'pilihan_dinas_1' => $dinasSafe->id,
            'pilihan_dinas_2' => $dinasSafe->id
        ]);

        // Akses dashboard dengan filter periode 1
        $response = $this->actingAs($superadmin)
                         ->get('/dashboard?info_or_id=' . $periode1->id);

        $response->assertStatus(200);

        // Harusnya totalPendaftar = 2 (sesuai filter), bukan 5 (total semua)
        $this->assertEquals(2, $response->viewData('totalPendaftar'));
        
        $this->assertArrayHasKey('terdaftar', $response->viewData('additionalStats'));
    }
}