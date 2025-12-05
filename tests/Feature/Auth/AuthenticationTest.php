<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Dinas;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $testUser;
    protected $dinas;
    
    /**
     * Setup yang dijalankan sebelum setiap test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Buat data dummy untuk tabel 'dinas' agar halaman login tidak error
        $this->dinas = Dinas::factory()->create([
            'id' => 1,
            'nama_dinas' => 'Dinas Testing',
        ]);

        // Buat user yang akan digunakan untuk testing login
        $this->testUser = User::factory()->create([
            'email' => 'login@test.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);
        
        // Buat satu dinas lagi untuk memastikan koleksi dinas lebih dari satu
        Dinas::factory()->create([
            'id' => 2,
            'nama_dinas' => 'Dinas Dua',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AUTH-001: Pengujian Akses Halaman Login (create)
    |--------------------------------------------------------------------------
    */

    /**
     * TC-AUTH-001: Test halaman login dapat diakses dan membawa data dinas
     */
    public function test_halaman_login_menampilkan_view_dan_data_dinas()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        
        // Pastikan view membawa data dinas dan jumlahnya sesuai
        $response->assertViewHas('allDinas');
        $this->assertCount(2, $response->viewData('allDinas'));
    }

    /*
    |--------------------------------------------------------------------------
    | Pengujian Login (store)
    |--------------------------------------------------------------------------
    */

    /**
     * TC-AUTH-002: Test user dapat login dengan kredensial yang valid
     */
    public function test_user_dapat_login_dengan_kredensial_valid()
    {
        $response = $this->post('/login', [
            'email' => $this->testUser->email,
            'password' => 'password',
        ]);

        // Pastikan user terautentikasi
        $this->assertAuthenticatedAs($this->testUser);

        // Pastikan redirect ke halaman intended (dashboard)
        $response->assertRedirect(route('dashboard')); 
    }

    /**
     * TC-AUTH-003: Test user GAGAL login dengan password yang salah
     */
    public function test_user_gagal_login_dengan_password_salah()
    {
        $response = $this->post('/login', [
            'email' => $this->testUser->email,
            'password' => 'salahpassword',
        ]);

        // Pastikan user TIDAK terautentikasi
        $this->assertGuest();
        
        // Pastikan terjadi error di sesi (biasanya dikembalikan ke form login)
        $response->assertSessionHasErrors('email'); 
    }

    /**
     * TC-AUTH-004: Test user GAGAL login dengan email yang tidak terdaftar
     */
    public function test_user_gagal_login_dengan_email_tidak_terdaftar()
    {
        $response = $this->post('/login', [
            'email' => 'unknown@test.com',
            'password' => 'password',
        ]);

        // Pastikan user TIDAK terautentikasi
        $this->assertGuest();
        
        $response->assertSessionHasErrors('email'); 
    }
    
    /*
    |--------------------------------------------------------------------------
    | Pengujian Logout (destroy)
    |--------------------------------------------------------------------------
    */

    /**
     * TC-AUTH-005: Test user dapat logout dengan sukses
     */
    public function test_user_dapat_logout_dengan_sukses()
    {
        // Lakukan login terlebih dahulu
        $this->actingAs($this->testUser);

        // Lakukan POST ke rute logout (asumsi rute logout adalah '/logout')
        $response = $this->post('/logout');

        // Pastikan user TIDAK terautentikasi lagi
        $this->assertGuest();

        // Pastikan redirect ke halaman login ('/login')
        $response->assertRedirect('/login');
    }
}