<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function superadmin_melihat_halaman_kelola_user()
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'status' => 'aktif',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/users')
                ->assertSee('Kelola User')
                ->screenshot('melihat halaman user berhasil')
                ->assertPresent('#create-button');
        });
    }

    /** @test */
    public function superadmin_dapat_menambah_user()
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'status' => 'aktif',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/users')
                ->click('#create-button')
                ->waitFor('#form-modal')
                ->type('nama_lengkap', 'User Testing')
                ->type('email', 'user@test.com')
                ->type('password', 'password123')
                ->select('role', 'admin')
                ->select('status', 'aktif')
                ->press('Simpan')
                ->waitForLocation('/users')
                ->screenshot('nambah user berhasil')
                ->assertSee('User berhasil ditambahkan');
        });
    }

    /** @test */
    public function superadmin_dapat_edit_user()
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'status' => 'aktif',
        ]);

        $user = User::factory()->create([
            'nama_lengkap' => 'User Lama',
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->loginAs($admin)
                ->visit('/users')
                ->click(".edit-button[data-id='{$user->id}']")
                ->waitFor('#form-modal')
                ->type('nama_lengkap', 'User Baru')
                ->press('Simpan')
                ->waitForLocation('/users')
                ->assertSee('User berhasil diperbarui')
                ->screenshot('perbarui user berhasil')
                ->assertSee('User Baru');
        });
    }

    /** @test */
    public function superadmin_dapat_menghapus_user()
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'status' => 'aktif',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->loginAs($admin)
                ->visit('/users')
                ->click(".delete-button[data-id='{$user->id}']")
                ->waitFor('#delete-modal')
                ->press('Hapus')
                ->waitForLocation('/users')
                ->screenshot('hapus user berhasil')
                ->assertSee('User berhasil dihapus');
        });
    }

    /** @test */
    public function validasi_gagal_jika_form_user_kosong()
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'status' => 'aktif',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/users')
                ->click('#create-button')
                ->waitFor('#form-modal')
                ->press('Simpan')
                ->assertAttribute('input[name=nama_lengkap]', 'required', 'true')
                ->assertAttribute('input[name=email]', 'required', 'true')
                ->screenshot('validasi kegagalan berhasil berhasil')
                ->assertAttribute('input[name=password]', 'required', 'true');
        });
    }
}
