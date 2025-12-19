<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\JadwalSeleksi;
use App\Models\Pendaftaran;
use App\Models\InfoOr;
use App\Models\Dinas;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Carbon\Carbon;
use Faker\Factory as Faker;

class JadwalSeleksiTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('id_ID'); 
    }

    /**
     * Test superadmin dapat melihat halaman daftar jadwal seleksi
     */
    public function test_superadmin_dapat_melihat_halaman_jadwal_wawancara()
    {
        $admin = User::factory()->superadmin()->create();
        
        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/jadwal-seleksi')
                    ->assertPathIs('/jadwal-seleksi')
                    ->assertSee('Kelola Jadwal Wawancara');
        });
    }

    /**
     * Test superadmin dapat membuat jadwal seleksi baru
     */
    public function test_superadmin_dapat_membuat_jadwal_seleksi()
    {
        $admin = User::factory()->superadmin()->create();
        $infoOr = InfoOr::factory()->create(['judul' => 'Magang BEM FTI 2024']);
        $dinas = Dinas::factory()->create();
        
        $pendaftaran = Pendaftaran::factory()->create([
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'jadwal_seleksi_id' => null,
        ]);

        $namaPewawancara = $this->faker->name();
        $tempat = $this->faker->randomElement(['Sekretariat BEM FTI', 'PKM FTI', 'Seminar DSI']);

        $this->browse(function (Browser $browser) use ($admin, $infoOr, $pendaftaran, $namaPewawancara, $tempat) {
            $browser->loginAs($admin)
                    ->visit('/jadwal-seleksi/create')
                    ->assertSee('Tambah Jadwal Wawancara')
                    ->select('info_or_id', $infoOr->id)
                    ->pause(500)
                    ->type('tanggal_seleksi', Carbon::tomorrow()->format('Y-m-d'))
                    ->type('waktu_mulai', '09:00')
                    ->type('waktu_selesai', '11:00')
                    ->type('tempat', $tempat)
                    ->type('pewawancara', $namaPewawancara)
                    ->radio('pendaftaran_id', $pendaftaran->id)
                    ->press('Simpan')
                    ->pause(2000)
                    ->assertPathIs('/jadwal-seleksi');
        });

        $this->assertDatabaseHas('jadwal_seleksi', [
            'tempat' => $tempat,
            'pewawancara' => $namaPewawancara,
        ]);
    }

    /**
     * Test superadmin dapat mengedit jadwal seleksi
     */
    public function test_superadmin_dapat_mengedit_jadwal_seleksi()
    {
        $admin = User::factory()->superadmin()->create();
        $infoOr = InfoOr::factory()->create();
        $dinas = Dinas::factory()->create();
        
        $pendaftaran = Pendaftaran::factory()->create([
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);
        
        $tempatLama = $this->faker->randomElement(['PKM FTI', 'Lobby FTI']);
        $tempatBaru = $this->faker->randomElement(['Sekretariat BEM FTI', 'Seminar DSI']);
        
        $jadwal = JadwalSeleksi::factory()->create([
            'info_or_id' => $infoOr->id,
            'pendaftaran_id' => $pendaftaran->id,
            'tempat' => $tempatLama,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $jadwal, $tempatBaru) {
            $browser->loginAs($admin)
                    ->visit("/jadwal-seleksi/{$jadwal->id}/edit")
                    ->assertSee('Edit Jadwal Wawancara')
                    ->clear('tempat')
                    ->type('tempat', $tempatBaru)
                    ->press('Update')
                    ->pause(2000)
                    ->assertPathIs('/jadwal-seleksi');
        });

        $this->assertDatabaseHas('jadwal_seleksi', [
            'id' => $jadwal->id,
            'tempat' => $tempatBaru,
        ]);
    }

    /**
     * Test superadmin dapat melihat detail jadwal seleksi
     */
    public function test_superadmin_dapat_melihat_detail_jadwal()
    {
        $admin = User::factory()->superadmin()->create();
        $infoOr = InfoOr::factory()->create(['judul' => 'Wawancara Magang BEM FTI']);
        $dinas = Dinas::factory()->create();
        
        $namaPeserta = $this->faker->name();
        $namaPewawancara = $this->faker->name();
        $tempat = $this->faker->randomElement(['Sekretariat BEM FTI', 'PKM FTI', 'Seminar DSI']);
        
        $user = User::factory()->create(['nama_lengkap' => $namaPeserta]);
        
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $user->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);
        
        $jadwal = JadwalSeleksi::factory()->create([
            'info_or_id' => $infoOr->id,
            'pendaftaran_id' => $pendaftaran->id,
            'tempat' => $tempat,
            'pewawancara' => $namaPewawancara,
            'tanggal_seleksi' => Carbon::tomorrow(),
        ]);

        $this->browse(function (Browser $browser) use ($admin, $jadwal, $tempat, $namaPewawancara, $namaPeserta) {
            $browser->loginAs($admin)
                    ->visit("/jadwal-seleksi/{$jadwal->id}")
                    ->assertSee('Detail Jadwal Wawancara')
                    ->assertSee($tempat)
                    ->assertSee($namaPewawancara)
                    ->assertSee($namaPeserta);
        });
    }

    /**
     * Test superadmin dapat menghapus jadwal seleksi
     */
    public function test_superadmin_dapat_menghapus_jadwal_seleksi()
    {
        $admin = User::factory()->superadmin()->create();
        $infoOr = InfoOr::factory()->create();
        $dinas = Dinas::factory()->create();

        $tempat = $this->faker->randomElement(['PKM FTI', 'Seminar DSI', 'Lobby FTI']);

        $pendaftaran = Pendaftaran::factory()->create([
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);

        $jadwal = JadwalSeleksi::factory()->create([
            'info_or_id' => $infoOr->id,
            'pendaftaran_id' => $pendaftaran->id,
            'tempat' => $tempat,
        ]);

        $jadwalId = $jadwal->id;

        $this->browse(function (Browser $browser) use ($admin, $tempat) {
            $browser->loginAs($admin)
                ->visit('/jadwal-seleksi')
                ->assertSee($tempat)
                ->script("document.querySelector('.delete-button').click();");

            $browser->pause(2000)
                ->waitFor('.swal2-popup', 5)
                ->pause(1000)
                ->press('Ya, hapus!')
                ->waitUntilMissing('.swal2-popup', 5);
        });

        $this->assertDatabaseMissing('jadwal_seleksi', [
            'id' => $jadwalId,
        ]);
    }

    /**
     * Test pencarian jadwal seleksi berdasarkan pewawancara
     */
    public function test_pencarian_jadwal_berdasarkan_pewawancara()
    {
        $admin = User::factory()->superadmin()->create();
        $infoOr = InfoOr::factory()->create();
        $dinas = Dinas::factory()->create();
        
        $namaPewawancara1 = $this->faker->name();
        $namaPewawancara2 = $this->faker->name();
        
        $pendaftaran1 = Pendaftaran::factory()->create([
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);
        
        $pendaftaran2 = Pendaftaran::factory()->create([
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
        ]);
        
        JadwalSeleksi::factory()->create([
            'info_or_id' => $infoOr->id,
            'pendaftaran_id' => $pendaftaran1->id,
            'pewawancara' => $namaPewawancara1,
        ]);
        
        JadwalSeleksi::factory()->create([
            'info_or_id' => $infoOr->id,
            'pendaftaran_id' => $pendaftaran2->id,
            'pewawancara' => $namaPewawancara2,
        ]);

        // Ambil kata pertama dari nama untuk pencarian
        $keywordCari = explode(' ', $namaPewawancara1)[0];

        $this->browse(function (Browser $browser) use ($admin, $keywordCari, $namaPewawancara1, $namaPewawancara2) {
            $browser->loginAs($admin)
                    ->visit('/jadwal-seleksi')
                    ->type('search', $keywordCari)
                    ->press('Filter / Cari')
                    ->waitForText($namaPewawancara1, 5)
                    ->assertSee($namaPewawancara1)
                    ->assertDontSee($namaPewawancara2);
        });
    }

    /**
     * Test filter jadwal berdasarkan tanggal
     */
    public function test_filter_jadwal_berdasarkan_tanggal()
    {
        $admin = User::factory()->superadmin()->create();
        $infoOr = InfoOr::factory()->create(['judul' => 'Seleksi Magang FTI']);
        $dinas = Dinas::factory()->create();

        $tanggalBesok = Carbon::tomorrow()->format('Y-m-d');

        $tempat1 = $this->faker->randomElement(['PKM FTI', 'Sekretariat BEM FTI']);
        $tempat2 = $this->faker->randomElement(['Lobby FTI', 'Ruang Rapat']);
        $tempat3 = $this->faker->randomElement(['Seminar DSI', 'Aula FTI']);

        $this->buatJadwal($infoOr, $dinas, $this->faker->name(), $tempat1, $this->faker->name(), $tanggalBesok);
        $this->buatJadwal($infoOr, $dinas, $this->faker->name(), $tempat2, $this->faker->name(), $tanggalBesok);
        $this->buatJadwal($infoOr, $dinas, $this->faker->name(), $tempat3, $this->faker->name(), Carbon::today()->addDays(5)->format('Y-m-d'));

        $this->browse(function (Browser $browser) use ($admin, $tanggalBesok, $tempat1, $tempat2, $tempat3) {
            $browser->loginAs($admin)
                ->visit('/jadwal-seleksi')
                ->waitForText($tempat1)
                ->waitForText($tempat2)
                ->waitForText($tempat3);

            $browser->script("
                document.querySelector('input[name=tanggal]').value = '{$tanggalBesok}';
            ");

            $browser->press('Filter / Cari')
                ->waitForText($tempat1)
                ->assertSee($tempat1)
                ->assertSee($tempat2)
                ->assertDontSee($tempat3);
        });
    }

    /**
     * Test menampilkan empty state ketika belum ada jadwal
     */
    public function test_menampilkan_empty_state_ketika_belum_ada_jadwal()
    {
        $admin = User::factory()->superadmin()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/jadwal-seleksi')
                    ->assertSee('Belum ada Jadwal Wawancara yang dimasukkan');
        });
    }

    /**
     * Helper method untuk membuat jadwal
     */
    private function buatJadwal(
        InfoOr $infoOr,
        Dinas $dinas,
        string $namaPeserta,
        string $tempat,
        string $pewawancara,
        string $tanggal
    ) {
        $user = User::factory()->create([
            'nama_lengkap' => $namaPeserta
        ]);

        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $user->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'jadwal_seleksi_id' => null,
        ]);

        return JadwalSeleksi::factory()->create([
            'info_or_id' => $infoOr->id,
            'pendaftaran_id' => $pendaftaran->id,
            'tempat' => $tempat,
            'pewawancara' => $pewawancara,
            'tanggal_seleksi' => $tanggal,
        ]);
    }
}