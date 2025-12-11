<?php

namespace Tests\Browser;

use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\JadwalKegiatan;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class DashboardTest extends DuskTestCase
{
    use RefreshDatabase;

    /**
     * TEST CASE 1: Superadmin dapat mengakses dashboard dan melihat semua data.
     */
    #[Test]
    #[Group('dashboard-dusk')]
    public function testSuperadminCanSeeAllDataOnDashboard(): void
    {
        // 1. Setup Data
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $dinasList = Dinas::factory()->count(3)->create();
        $infoOr = InfoOr::factory()->create();
        JadwalKegiatan::factory()->count(1)->create(['info_or_id' => $infoOr->id]);
        Pendaftaran::factory()->count(5)->create([
            'pilihan_dinas_1' => $dinasList->first()->id,
        ]);

        // 2. Akses & Assertions
        $this->browse(function (Browser $browser) use ($superadmin) {
            $browser->loginAs($superadmin)
                ->visit('/dashboard')
                ->assertSee('Dashboard Superadmin')
                // Mengganti assertSeeIn dengan assertSee karena view tidak memiliki data-dusk
                ->assertSee('Total Pendaftar')
                ->assertSee('5')
                ->assertSee('Total Dinas')
                ->assertSee('3')
                ->assertSee('Total Kegiatan')
                ->assertSee('1');
        });
    }

    /**
     * TEST CASE 2: Admin Dinas hanya melihat data pendaftar untuk dinasnya.
     */
    #[Test]
    #[Group('dashboard-dusk')]
    public function testAdminDinasSeesOnlyTheirDataOnDashboard(): void
    {
        // 1. Setup
        $dinasKita = Dinas::factory()->create(['nama_dinas' => 'Dinas Admin A']);
        $dinasLain = Dinas::factory()->create(['nama_dinas' => 'Dinas Lain B']);
        $admin = User::factory()->create(['role' => 'admin', 'dinas_id' => $dinasKita->id]);

        // Pendaftar ke dinas kita (Harus terhitung)
        Pendaftaran::factory()->create(['pilihan_dinas_1' => $dinasKita->id, 'pilihan_dinas_2' => null]);
        // Pendaftar ke dinas lain (Tidak boleh terhitung)
        Pendaftaran::factory()->count(2)->create(['pilihan_dinas_1' => $dinasLain->id, 'pilihan_dinas_2' => null]);

        // 2. Akses & Assertions
        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/dashboard')
                ->assertSee('Dashboard Admin')
                ->assertSee('Total Pendaftar')
                // Hanya 1 pendaftar untuk dinasnya
                ->assertSee('1')
                // Kartu Total Dinas tidak ditampilkan untuk admin, jadi hapus assertion
                ->assertDontSee('Total Dinas');
        });
    }

    /**
     * TEST CASE 3: Mahasiswa melihat riwayat pendaftarannya di dashboard.
     */
    #[Test]
    #[Group('dashboard-dusk')]
    public function testMahasiswaSeesTheirRegistrationHistory(): void
    {
        // 1. Setup
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $dinas = Dinas::factory()->create(['nama_dinas' => 'Dinas Tujuan Mahasiswa']);
        Pendaftaran::factory()->create([
            'user_id' => $mahasiswa->id,
            'pilihan_dinas_1' => $dinas->id,
            'status_pendaftaran' => 'terdaftar', // Status 'terdaftar' akan menampilkan 'Menunggu Seleksi'
        ]);

        // 2. Akses & Assertions
        $this->browse(function (Browser $browser) use ($mahasiswa, $dinas) {
            $browser->loginAs($mahasiswa)
                ->visit('/dashboard')
                // Judul yang benar di view adalah "Status Pendaftaran Magang"
                ->assertSee('Status Pendaftaran Magang')
                ->assertSee($dinas->nama_dinas)
                // View menampilkan "Menunggu Seleksi" untuk status "terdaftar"
                ->assertSee('Menunggu Seleksi')
                // Pastikan tidak ada statistik admin
                ->assertDontSee('Total Pendaftar');
        });
    }

    /**
     * TEST CASE 4: Superadmin dapat memfilter data berdasarkan periode.
     */
    #[Test]
    #[Group('dashboard-dusk')]
    public function testSuperadminCanFilterDashboardByPeriode(): void
    {
        // 1. Setup
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $periode1 = InfoOr::factory()->create(['judul' => 'Periode Gasal 2024']);
        $periode2 = InfoOr::factory()->create(['judul' => 'Periode Genap 2025']);
        $dinas = Dinas::factory()->create();

        Pendaftaran::factory()->count(2)->create(['info_or_id' => $periode1->id, 'pilihan_dinas_1' => $dinas->id]);
        Pendaftaran::factory()->count(3)->create(['info_or_id' => $periode2->id, 'pilihan_dinas_1' => $dinas->id]);

        // 2. Akses & Assertions
        $this->browse(function (Browser $browser) use ($superadmin, $periode1, $periode2) {
            $browser->loginAs($superadmin)
                ->visit('/dashboard')
                // Awalnya melihat semua pendaftar
                ->assertSee('Total Pendaftar')
                ->assertSee('5')

                // Filter ke periode 1. Form submit onchange, jadi tidak perlu ->press()
                ->select('info_or_id', $periode1->id)
                ->assertPathIs('/dashboard')
                ->assertQueryStringHas('info_or_id', $periode1->id)
                ->assertSee('Pendaftar Periode Ini')
                ->assertSee('2') // Data terfilter

                // Coba filter periode lain
                ->select('info_or_id', $periode2->id)
                ->assertQueryStringHas('info_or_id', $periode2->id)
                ->assertSee('Pendaftar Periode Ini')
                ->assertSee('3'); // Data terfilter
        });
    }
}
