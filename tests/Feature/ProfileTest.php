<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * TEST CASE 1: Pastikan halaman edit profil bisa diakses oleh user yang login.
     */
    public function test_halaman_edit_profil_bisa_diakses(): void
    {
        // 1. Buat user dummy
        $user = User::factory()->create();

        // 2. Login sebagai user tersebut dan akses route profile.edit
        $response = $this->actingAs($user)
                         ->get(route('profile.edit'));

        // 3. Pastikan statusnya 200 OK dan view-nya benar
        $response->assertStatus(200)
                 ->assertViewIs('profile.edit');
    }

    /**
     * TEST CASE 2: Pastikan tamu (guest) tidak bisa akses halaman profil.
     */
    public function test_tamu_tidak_bisa_akses_halaman_profil(): void
    {
        // Akses tanpa login
        $response = $this->get(route('profile.edit'));

        // Harusnya di-redirect ke halaman login (status 302)
        $response->assertStatus(302);
    }

    /**
     * TEST CASE 3: Update profil (Nama dan Email) berhasil.
     */
    public function test_update_data_profil_berhasil(): void
    {
        $user = User::factory()->create([
            'nama_lengkap' => 'Nama Lama',
            'email' => 'lama@example.com',
        ]);

        $response = $this->actingAs($user)
                         ->patch(route('profile.update'), [
                             'nama_lengkap' => 'Nama Baru',
                             'email' => 'baru@example.com',
                         ]);

        // Pastikan tidak ada error session dan redirect kembali ke halaman edit
        $response->assertSessionHasNoErrors()
                 ->assertRedirect(route('profile.edit'));
        
        // Cek database apakah data user benar-benar berubah
        $user->refresh(); // Refresh data user dari DB
        $this->assertSame('Nama Baru', $user->nama_lengkap);
        $this->assertSame('baru@example.com', $user->email);
    }

    /**
     * TEST CASE 4: Update profil gagal jika email sudah dipakai user lain.
     */
    public function test_update_profil_gagal_jika_email_sudah_terdaftar(): void
    {
        // Buat user A
        $user = User::factory()->create(['email' => 'user1@example.com']);
        // Buat user B (pemilik email target)
        User::factory()->create(['email' => 'user2@example.com']);

        // User A mencoba mengganti emailnya menjadi email User B
        $response = $this->actingAs($user)
                         ->patch(route('profile.update'), [
                             'nama_lengkap' => 'User Satu',
                             'email' => 'user2@example.com', // Email milik user B
                         ]);

        // Harusnya gagal validasi
        $response->assertSessionHasErrors('email');
    }

    /**
     * TEST CASE 5: Update profil berhasil jika email tidak diubah (mengabaikan ID sendiri).
     */
    public function test_update_profil_berhasil_jika_email_tidak_berubah(): void
    {
        $user = User::factory()->create(['email' => 'tetap@example.com']);

        $response = $this->actingAs($user)
                         ->patch(route('profile.update'), [
                             'nama_lengkap' => 'Ganti Nama Saja',
                             'email' => 'tetap@example.com', // Email sama
                         ]);

        $response->assertSessionHasNoErrors()
                 ->assertRedirect(route('profile.edit'));
    }

    /**
     * TEST CASE 6: Halaman ubah password bisa diakses.
     */
    public function test_halaman_ubah_password_bisa_diakses(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('profile.password.edit')); // Sesuaikan route di web.php

        $response->assertStatus(200)
                 ->assertViewIs('profile.edit-password');
    }

    /**
     * TEST CASE 7: Update password berhasil dengan input valid.
     */
    public function test_update_password_berhasil(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama'),
        ]);

        $response = $this->actingAs($user)
                         ->put(route('profile.password.update'), [
                             'current_password' => 'password_lama',
                             'password' => 'password_baru',
                             'password_confirmation' => 'password_baru',
                         ]);

        $response->assertSessionHasNoErrors()
                 ->assertRedirect(route('profile.password.edit'))
                 ->assertSessionHas('status', 'password-updated');

        // Verifikasi password di database sudah berubah
        $this->assertTrue(Hash::check('password_baru', $user->fresh()->password));
    }

    /**
     * TEST CASE 8: Update password gagal jika password lama salah.
     */
    public function test_update_password_gagal_jika_password_lama_salah(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_asli'),
        ]);

        $response = $this->actingAs($user)
                         ->put(route('profile.password.update'), [
                             'current_password' => 'password_salah', // Salah
                             'password' => 'password_baru',
                             'password_confirmation' => 'password_baru',
                         ]);

        // Harus error di field current_password
        $response->assertSessionHasErrors(['current_password']);
        
        // Pastikan password TIDAK berubah
        $this->assertTrue(Hash::check('password_asli', $user->fresh()->password));
    }

    /**
     * TEST CASE 9: Update password gagal jika konfirmasi password tidak cocok.
     */
    public function test_update_password_gagal_jika_konfirmasi_tidak_cocok(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)
                         ->put(route('profile.password.update'), [
                             'current_password' => 'password123',
                             'password' => 'password_baru',
                             'password_confirmation' => 'password_beda', // Tidak sama
                         ]);

        $response->assertSessionHasErrors(['password']);
    }
}