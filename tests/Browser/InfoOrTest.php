<?php

namespace Tests\Browser;

use App\Models\InfoOr;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class InfoOrTest extends DuskTestCase
{
    use RefreshDatabase;

    protected $superadmin;
    protected $admin;
    protected $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    }

    #[Test]
    #[Group('infoor-dusk')]
    public function testSuperadminCanCreateInfoOrSuccessfully(): void
    {
        Storage::fake('gambar_public');
        $file = UploadedFile::fake()->create('poster.jpg', 1024);

        $this->browse(function (Browser $browser) use ($file) {
            $browser->loginAs($this->superadmin)
                ->visit(route('info-or.index'))
                ->clickLink('Tambah Info OR') // Asumsi ada link/tombol dengan teks ini
                ->assertPathIs(route('info-or.create'))
                ->type('judul', 'Penerimaan Batch Baru 2025')
                ->type('deskripsi', 'Ini adalah deskripsi untuk penerimaan batch baru.')
                ->type('persyaratan_umum', 'Mahasiswa aktif, IPK > 3.0')
                ->type('tanggal_buka', now()->addDay()->format('Y-m-d'))
                ->type('tanggal_tutup', now()->addMonth()->format('Y-m-d'))
                ->type('periode', '2025-2026')
                ->attach('gambar', $file)
                ->press('Simpan')
                ->assertPathIs(route('info-or.index'))
                ->assertSee('Info OR berhasil ditambahkan')
                ->assertSee('Penerimaan Batch Baru 2025');
        });
    }

    #[Test]
    #[Group('infoor-dusk')]
    public function testSuperadminCanCloseAnOpenRegistration(): void
    {
        InfoOr::factory()->create(['status' => 'buka']);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superadmin)
                ->visit(route('info-or.index'))
                ->assertSee('Status: Buka')
                ->press('Tutup Pendaftaran') // Asumsi ada tombol ini
                ->assertPathIs(route('info-or.index'))
                ->assertSee('Info OR berhasil ditutup')
                ->assertSee('Status: Tutup');
        });
    }

    #[Test]
    #[Group('infoor-dusk')]
    public function testInfoOrCreateFormShowsValidationErrors(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superadmin)
                ->visit(route('info-or.create'))
                ->press('Simpan')
                ->assertPathIs(route('info-or.create'))
                ->assertSee('The judul field is required.')
                ->assertSee('The deskripsi field is required.')
                ->assertSee('The gambar field is required.');
        });
    }

    #[Test]
    #[Group('infoor-dusk')]
    public function testNonSuperadminsAreNotAuthorized(): void
    {
        InfoOr::factory()->create(['status' => 'buka']);

        // Test for Admin
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit(route('info-or.index'))
                ->assertDontSee('Tambah Info OR')
                ->assertDontSee('Tutup Pendaftaran')
                ->visit(route('info-or.create'))
                ->assertSee('403') // Dusk sees the 403 error page content
                ->assertSee('FORBIDDEN');
        });

        // Test for Mahasiswa
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->mahasiswa)
                ->visit(route('info-or.index'))
                ->assertDontSee('Tambah Info OR')
                ->assertDontSee('Tutup Pendaftaran')
                ->visit(route('info-or.create'))
                ->assertSee('403')
                ->assertSee('FORBIDDEN');
        });
    }
}
