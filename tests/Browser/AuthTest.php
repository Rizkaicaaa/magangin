<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\InfoOr;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080);
        });
    }

    #[Test]
    public function halaman_login_berhasil_dimuat_saat_or_buka()
    {
        InfoOr::factory()->create([
            'status' => 'buka',
            'gambar' => 'images/poster.jpg',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('#loginForm', 10)
                    ->screenshot('01-login-page-or-buka')
                    ->assertPathIs('/login')
                    ->assertPresent('#loginForm');
        });
    }

    #[Test]
    public function user_berhasil_login_dan_redirect_ke_dashboard()
    {
        InfoOr::factory()->create([
            'status' => 'buka',
            'gambar' => 'images/poster.jpg',
        ]);

        User::factory()->create([
            'email' => 'mahasiswa@test.com',
            'password' => Hash::make('password123'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('#loginForm', 10)
                    ->type('#email', 'mahasiswa@test.com')
                    ->type('#password', 'password123')
                    ->click('form#loginForm button[type="submit"]')
                    ->waitForLocation('/dashboard', 10)
                    ->screenshot('02-login-berhasil-ke-dashboard')
                    ->assertPathIs('/dashboard')

                    // 🔴 PENTING: LOGOUT supaya test berikutnya tidak kena session
                    ->logout();
        });
    }

    #[Test]
    public function user_gagal_login_karena_password_salah()
    {
        InfoOr::factory()->create([
            'status' => 'buka',
            'gambar' => 'images/poster.jpg',
        ]);

        User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('passwordBenar'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('#loginForm', 10)
                    ->type('#email', 'user@test.com')
                    ->type('#password', 'passwordSalah')
                    ->click('form#loginForm button[type="submit"]')
                    ->pause(500)
                    ->screenshot('03-login-gagal-password-salah')
                    ->assertPathIs('/login')
                    ->assertPresent('#loginForm');
        });
    }

    #[Test]
    public function user_gagal_login_karena_email_tidak_terdaftar()
    {
        InfoOr::factory()->create([
            'status' => 'buka',
            'gambar' => 'images/poster.jpg',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('#loginForm', 10)
                    ->type('#email', 'tidakada@test.com')
                    ->type('#password', 'password123')
                    ->click('form#loginForm button[type="submit"]')
                    ->pause(500)
                    ->screenshot('04-login-gagal-email-tidak-terdaftar')
                    ->assertPathIs('/login')
                    ->assertPresent('#loginForm');
        });
    }

    #[Test]
    public function tombol_register_dinonaktifkan_jika_or_tutup()
    {
        InfoOr::factory()->create([
            'status' => 'tutup',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('#registerTab', 10)
                    ->screenshot('05-register-disabled-or-tutup')
                    ->assertAttribute('#registerTab', 'disabled', 'true')
                    ->assertPresent('#loginForm')
                    ->assertMissing('#registerForm');
        });
    }
}
