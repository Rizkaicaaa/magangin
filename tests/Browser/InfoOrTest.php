<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\InfoOr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Test;

class InfoOrTest extends DuskTestCase
{
    #[Test]
    public function superadmin_melihat_tombol_create_disabled_saat_or_aktif()
    {
        $admin = User::factory()->create();

        InfoOr::factory()->create([
            'status' => 'buka',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/info-or')
                ->assertPresent('#create-button')
                ->screenshot('tombol buat nonaktif berhasil')
                ->assertAttribute('#create-button', 'disabled', 'true');
        });
    }

    #[Test]
    public function superadmin_dapat_membuat_info_or_baru()
    {
        Storage::fake('gambar_public');

        $admin = User::factory()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/info-or')
                ->click('#empty-create-button')
                ->waitFor('#form-modal')

                ->type('judul', 'Info OR Dusk')
                ->type('deskripsi', 'Deskripsi OR')
                ->type('persyaratan_umum', 'Syarat')
                ->type('tanggal_buka', now()->format('Y-m-d'))
                ->type('tanggal_tutup', now()->addDays(5)->format('Y-m-d'))
                ->type('periode', '2025')

                // ⬇️ FIX: TIDAK pakai image() (biar Windows aman)
                ->attach('gambar', base_path('tests/Browser/files/dummy.jpg'))
                
                ->press('Simpan')
                ->waitForLocation('/info-or')
                ->screenshot('nambah info or berhasil')
                ->assertSee('Info OR berhasil ditambahkan!');
                

        });
    }

    #[Test]
    public function superadmin_dapat_menutup_info_or()
    {
        $admin = User::factory()->create();

        InfoOr::factory()->create([
            'status' => 'buka',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/info-or')
                ->click('.close-button')
                ->waitForText('Tutup Pendaftaran?')
                ->press('Ya, Tutup!')
                ->waitForLocation('/info-or')
                ->screenshot('nutup info or berhasil')
                ->assertSee('Ditutup');
        });
    }

    #[Test]
    public function validasi_gagal_jika_form_info_or_kosong()
    {
        $admin = User::factory()->create(['role' => 'superadmin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/info-or')
                ->click('#empty-create-button')
                ->waitFor('#form-modal')
                ->press('Simpan')
                ->screenshot('validasi gagal input berhasil')
                ->assertAttribute('input[name=judul]', 'required', 'true');
        });
    }
}
