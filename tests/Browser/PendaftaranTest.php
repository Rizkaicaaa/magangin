<?php

namespace Tests\Browser;

use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class PendaftaranTest extends DuskTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup data master yang diperlukan
        Dinas::factory()->count(3)->create();
        InfoOr::factory()->aktif()->create();
    }

    #[Test]
    #[Group('pendaftaran-dusk')]
    public function testGuestCanRegisterAsStudentSuccessfully(): void
    {
        Storage::fake('public');

        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Register');

            $dinasList = Dinas::all();
            $dinas1 = $dinasList->first();
            $dinas2 = $dinasList->get(1);

            $browser->type('nama_lengkap', 'Budi Dharmawan')
                ->type('nim', '2025001')
                ->type('email', 'budi.dharmawan@test.com')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->type('no_telp', '081234567890')
                ->select('pilihan_dinas_1', $dinas1->id)
                ->select('pilihan_dinas_2', $dinas2->id)
                ->type('motivasi', 'Motivasi saya adalah untuk belajar hal baru.')
                ->type('pengalaman', 'Belum ada pengalaman relevan.')
                ->attach('file_cv', UploadedFile::fake()->create('cv_budi.pdf', 100))
                ->attach('file_transkrip', UploadedFile::fake()->create('transkrip_budi.pdf', 100))
                ->press('Register')
                ->assertPathIs('/dashboard')
                // Memastikan diarahkan ke dashboard mahasiswa yang benar
                ->assertSee('Selamat Datang, Budi Dharmawan!');
        });
    }

    #[Test]
    #[Group('pendaftaran-dusk')]
    public function testRegistrationFormValidation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->press('Register')
                ->assertPathIs('/register') // Stay on the page
                ->assertSee('The nama lengkap field is required.')
                ->assertSee('The nim field is required.')
                ->assertSee('The email field is required.')
                ->assertSee('The password field is required.')
                ->assertSee('The pilihan dinas 1 field is required.')
                ->assertSee('The file cv field is required.');
        });
    }

    #[Test]
    #[Group('pendaftaran-dusk')]
    public function testSuperadminCanManagePendaftar(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $dinas = Dinas::first();
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $mahasiswa->id,
            'status_pendaftaran' => 'terdaftar',
            'dinas_diterima_id' => null,
            'pilihan_dinas_1' => $dinas->id,
        ]);

        $this->browse(function (Browser $browser) use ($superadmin, $mahasiswa, $dinas, $pendaftaran) {
            $browser->loginAs($superadmin)
                ->visit(route('pendaftar.index'))
                ->assertSee('Daftar Pendaftar')
                ->assertSee($mahasiswa->nama_lengkap);

            // Navigate to detail page by clicking the student's name
            $browser->clickLink($mahasiswa->nama_lengkap)
                ->assertPathIs(route('pendaftar.show', $pendaftaran->id))
                ->assertSee($mahasiswa->nama_lengkap)
                ->assertSee('Status Pendaftaran: terdaftar');

            // Update status to 'lulus_wawancara'
            $browser->select('status', 'lulus_wawancara')
                ->press('Update Status')
                ->assertSee('Status pendaftaran berhasil diperbarui.')
                ->assertSee('Status Pendaftaran: lulus_wawancara');

            // Assign a 'dinas'
            $browser->select('dinas_diterima_id', $dinas->id)
                ->press('Tetapkan Dinas')
                ->assertSee('Dinas penerima berhasil ditetapkan.')
                ->assertSee($dinas->nama_dinas); // Cek apakah nama dinas muncul di halaman
        });
    }
}
