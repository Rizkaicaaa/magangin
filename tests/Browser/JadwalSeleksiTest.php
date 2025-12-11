<?php

namespace Tests\Browser;

use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\JadwalSeleksi;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class JadwalSeleksiTest extends DuskTestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $mahasiswa;
    protected $pendaftaran;
    protected $infoOr;

    protected function setUp(): void
    {
        parent::setUp();

        $dinas = Dinas::factory()->create();
        $this->admin = User::factory()->create(['role' => 'admin', 'dinas_id' => $dinas->id]);
        $this->mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $this->infoOr = InfoOr::factory()->create();

        // Pendaftar yang akan dijadwalkan
        $this->pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'status_pendaftaran' => 'terdaftar',
            'jadwal_seleksi_id' => null,
        ]);
    }

    #[Test]
    #[Group('jadwal-seleksi-dusk')]
    public function testAdminCanPerformFullCrudOnJadwalSeleksi(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit(route('jadwal-seleksi.index'))
                ->assertSee('Jadwal Seleksi Wawancara');

            // CREATE
            $browser->clickLink('Buat Jadwal Wawancara')
                ->assertPathIs(route('jadwal-seleksi.create'))
                ->select('pendaftaran_id', $this->pendaftaran->id)
                ->select('info_or_id', $this->infoOr->id)
                ->type('tanggal_seleksi', now()->addDays(7)->format('Y-m-d'))
                ->type('waktu_mulai', '10:00')
                ->type('waktu_selesai', '11:00')
                ->type('tempat', 'Ruang Rapat Utama')
                ->type('pewawancara', 'Bapak Budi')
                ->press('Simpan')
                ->assertPathIs(route('jadwal-seleksi.index'))
                ->assertSee('Jadwal wawancara berhasil ditambahkan.')
                ->assertSee($this->mahasiswa->nama_lengkap) // Nama pendaftar
                ->assertSee('Bapak Budi');

            $jadwal = JadwalSeleksi::first();
            $this->assertNotNull($jadwal);

            // UPDATE
            $browser->clickLink('Edit') // Asumsi ada link 'Edit'
                ->assertPathIs(route('jadwal-seleksi.edit', $jadwal->id))
                ->assertSelected('pendaftaran_id', $this->pendaftaran->id)
                ->type('pewawancara', 'Ibu Ani')
                ->press('Update')
                ->assertPathIs(route('jadwal-seleksi.index'))
                ->assertSee('Jadwal seleksi berhasil diperbarui.')
                ->assertDontSee('Bapak Budi')
                ->assertSee('Ibu Ani');

            // DELETE
            $browser->press('Hapus') // Asumsi ada tombol 'Hapus'
                ->acceptDialog()
                ->assertPathIs(route('jadwal-seleksi.index'))
                ->assertSee('Jadwal seleksi berhasil dihapus.')
                ->assertDontSee($this->mahasiswa->nama_lengkap)
                ->assertDontSee('Ibu Ani');
        });
    }

    #[Test]
    #[Group('jadwal-seleksi-dusk')]
    public function testJadwalSeleksiCreateFormValidation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit(route('jadwal-seleksi.create'))
                ->press('Simpan')
                ->assertPathIs(route('jadwal-seleksi.create')) // Stay on the same page
                ->assertSee('The pendaftaran id field is required.')
                ->assertSee('The tanggal seleksi field is required.')
                ->assertSee('The waktu mulai field is required.')
                ->assertSee('The tempat field is required.')
                ->assertSee('The pewawancara field is required.');
        });
    }

    #[Test]
    #[Group('jadwal-seleksi-dusk')]
    public function testJadwalSeleksiIndexFilterAndSearch(): void
    {
        // Jadwal 1
        $jadwal1 = JadwalSeleksi::factory()->create([
            'pewawancara' => 'Dr. Eko',
            'tanggal_seleksi' => '2025-10-15',
        ]);
        // Jadwal 2
        $jadwal2 = JadwalSeleksi::factory()->create([
            'pewawancara' => 'Prof. Fara',
            'tanggal_seleksi' => '2025-10-20',
        ]);

        $this->browse(function (Browser $browser) use ($jadwal1, $jadwal2) {
            $browser->loginAs($this->admin)
                ->visit(route('jadwal-seleksi.index'))
                ->assertSee($jadwal1->pewawancara)
                ->assertSee($jadwal2->pewawancara);

            // Search by interviewer
            $browser->type('search', 'Eko')
                ->press('Search') // Assuming a button with name/text 'Search'
                ->assertSee($jadwal1->pewawancara)
                ->assertDontSee($jadwal2->pewawancara);

            // Filter by date
            $browser->visit(route('jadwal-seleksi.index')) // Reset search
                ->type('tanggal', '2025-10-20')
                ->press('Search')
                ->assertDontSee($jadwal1->pewawancara)
                ->assertSee($jadwal2->pewawancara);
        });
    }

    #[Test]
    #[Group('jadwal-seleksi-dusk')]
    public function testNonAdminsAreNotAuthorized(): void
    {
        $this->browse(function (Browser $browser) {
            // Mahasiswa trying to access create page
            $browser->loginAs($this->mahasiswa)
                ->visit(route('jadwal-seleksi.create'))
                ->assertSee('403') // Should see a forbidden error
                ->assertSee('FORBIDDEN');

            // Guest trying to access index page
            $browser->logout()
                ->visit(route('jadwal-seleksi.index'))
                ->assertPathIs('/login');
        });
    }
}
