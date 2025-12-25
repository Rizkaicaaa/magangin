<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfileTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Setup browser size
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080);
        });
    }

    /**
     * TEST CASE 1: User dapat memperbarui informasi profil (Nama & Email)
     * @test
     */
    public function user_dapat_memperbarui_informasi_profil()
    {
        $user = User::factory()->create([
            'nama_lengkap' => 'Nama Lama',
            'email' => 'lama@example.com'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertSee('Edit Profil')
                    ->screenshot('01_profile_page_loaded')
                    
                    // Ubah Data
                    ->type('nama_lengkap', 'Budi Update')
                    ->type('email', 'budi.update@example.com')
                    ->screenshot('02_form_filled')
                    
                    ->click('section form button[type="submit"]')

                    // Validasi Pesan Sukses
                    ->waitForText('Berhasil disimpan.')
                    ->screenshot('03_profile_updated_success')
                    
                    // Validasi Data UI
                    ->assertInputValue('nama_lengkap', 'Budi Update')
                    ->assertInputValue('email', 'budi.update@example.com');
            
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'nama_lengkap' => 'Budi Update',
                'email' => 'budi.update@example.com'
            ]);
        });
    }

    /**
     * TEST CASE 2: Validasi gagal saat update profil (Input Kosong/Email Duplikat)
     * @test
     */
    public function validasi_gagal_saat_update_profil()
    {
        $user = User::factory()->create(['email' => 'user1@test.com']);
        $otherUser = User::factory()->create(['email' => 'user2@test.com']);

        $this->browse(function (Browser $browser) use ($user, $otherUser) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->screenshot('04_validation_test_page_loaded');

            // --- SKENARIO 1: Input Kosong ---
            
            // 1. Kosongkan Input
            $browser->click('input[name="nama_lengkap"]')
                    ->keys('input[name="nama_lengkap"]', ['{control}', 'a'], '{backspace}')
                    ->click('input[name="email"]')
                    ->keys('input[name="email"]', ['{control}', 'a'], '{backspace}')
                    ->screenshot('05_empty_fields');

            // 2. PENTING: Hapus atribut 'required' HTML5 via JS 
            // agar validasi browser tidak memblokir submit ke server
            $browser->script("
                document.querySelector('input[name=\"nama_lengkap\"]').removeAttribute('required');
                document.querySelector('input[name=\"email\"]').removeAttribute('required');
            ");

            // 3. Submit
            $browser->click('section form button[type="submit"]')
                    ->pause(1000) // Tunggu server merespon
                    ->assertSee('field is required') // Pesan error Laravel
                    ->screenshot('06_empty_fields_validation_error');

            // --- SKENARIO 2: Email Duplikat ---
            
            $browser->type('nama_lengkap', 'Budi Santoso')
                    ->type('email', $otherUser->email) // Pakai email orang lain
                    ->screenshot('07_duplicate_email_filled')
                    ->click('section form button[type="submit"]')
                    ->pause(1000)
                    ->assertSee('already been taken')
                    ->screenshot('08_duplicate_email_validation_error');
        });
    }

    /**
     * TEST CASE 3: User dapat mengubah password (Happy Path)
     * @test
     */
    public function user_dapat_mengubah_password()
    {
        // Default password dari factory: password123 (sesuai factory Anda)
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile/password') 
                    ->assertSee('Ubah Password')
                    
                    // Tunggu elemen form siap
                    ->waitFor('#update_password_current_password', 10)
                    ->screenshot('09_change_password_page_loaded')

                    // Isi Form
                    ->type('#update_password_current_password', 'password123') 
                    ->type('#update_password_password', 'passwordBaru123')
                    ->type('#update_password_password_confirmation', 'passwordBaru123')
                    ->screenshot('10_password_form_filled')
                    
                    ->click('section form button[type="submit"]')

                    // Validasi Pesan Sukses Muncul
                    ->waitForText('Kata sandi berhasil diperbarui.')
                    ->screenshot('11_password_changed_success');
        });
    }

    /**
     * TEST CASE 4: Ubah password gagal saat ubah password (Password Lama Salah)
     * @test
     */
    public function ubah_password_gagal_jika_password_lama_salah()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile/password')
                    // Tunggu elemen form siap
                    ->waitFor('#update_password_current_password', 10)
                    ->screenshot('12_wrong_password_test_page_loaded')

                    ->type('#update_password_current_password', 'passwordSalah') 
                    ->type('#update_password_password', 'passwordBaru123')
                    ->type('#update_password_password_confirmation', 'passwordBaru123')
                    ->screenshot('13_wrong_password_filled')
                    
                    ->click('section form button[type="submit"]')

                    ->pause(1000)
                    ->assertSee('incorrect') // password is incorrect
                    ->screenshot('14_wrong_password_validation_error');
        });
    }

    /**
     * TEST CASE 5: Ubah password gagal saat ubah password (Konfirmasi Tidak Cocok)
     * @test
     */
    public function ubah_password_gagal_jika_konfirmasi_password_tidak_cocok()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile/password')
                    // Tunggu elemen form siap
                    ->waitFor('#update_password_current_password', 10)
                    ->screenshot('15_mismatch_password_test_page_loaded')

                    ->type('#update_password_current_password', 'password123') 
                    ->type('#update_password_password', 'passwordBaru123')
                    ->type('#update_password_password_confirmation', 'BedaPassword123')
                    ->screenshot('16_mismatch_password_filled')
                    
                    ->click('section form button[type="submit"]')

                    ->pause(1000)
                    ->assertSee('match') // confirmation does not match
                    ->screenshot('17_mismatch_password_validation_error');
        });
    }
}