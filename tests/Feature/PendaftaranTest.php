<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
// GANTI RefreshDatabase dengan DatabaseMigrations untuk mengatasi error SQLite
use Illuminate\Foundation\Testing\DatabaseMigrations; 
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PendaftaranTest extends TestCase
{
    // PENTING: Gunakan DatabaseMigrations agar tabel selalu dibuat ulang per test
    // Ini memperbaiki error "no such table" jika ada DB::commit() di controller
    use DatabaseMigrations, WithFaker;

    protected $dinasPsdm;
    protected $dinas1;
    protected $dinas2;
    protected $infoOr;
    
    protected $superadmin; 
    protected $adminDinas; 
    protected $mahasiswa;
    
    protected $pendaftaran;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        Storage::disk('public')->put('cv/dummy.pdf', 'Isi dummy CV');
        Storage::disk('public')->put('transkrip/dummy.pdf', 'Isi dummy Transkrip');
        
        // 1. SETUP DINAS
        $this->dinasPsdm = Dinas::factory()->create([
            'nama_dinas' => 'Dinas PSDM', 
            'kontak_person' => 'psdm@test.com'
        ]);

        $this->dinas1 = Dinas::factory()->create(['nama_dinas' => 'Dinas Pendidikan']);
        $this->dinas2 = Dinas::factory()->create(['nama_dinas' => 'Dinas Kesehatan']);

        // 2. SETUP INFO OR
        $this->infoOr = InfoOr::factory()->aktif()->create();

        // 3. SETUP USER: SUPERADMIN
        $this->superadmin = User::factory()->create([
            'nama_lengkap' => 'Super Admin PSDM',
            'email' => 'psdm@magangin.test',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
            'dinas_id' => $this->dinasPsdm->id,
            'status' => 'aktif'
        ]);

        // 4. SETUP USER: ADMIN DINAS
        $this->adminDinas = User::factory()->create([
            'nama_lengkap' => 'Admin Dinas Pendidikan',
            'email' => 'admin.pendidikan@magangin.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'dinas_id' => $this->dinas1->id,
            'status' => 'aktif'
        ]);

        // 5. SETUP USER: MAHASISWA
        $this->mahasiswa = User::factory()->create([
            'nama_lengkap' => 'Mahasiswa Peserta',
            'nim' => '2021001',
            'email' => 'mahasiswa@student.test',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
            'status' => 'aktif'
        ]);

        // 6. SETUP PENDAFTARAN
        $this->pendaftaran = Pendaftaran::create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas1->id,
            'pilihan_dinas_2' => $this->dinas2->id,
            'motivasi' => 'Saya sangat ingin belajar',
            'pengalaman' => 'Pernah ikut bootcamp',
            'status_pendaftaran' => 'terdaftar',
            'file_cv' => 'cv/dummy.pdf',
            'file_transkrip' => 'transkrip/dummy.pdf'
        ]);
    }

    protected function getValidRegistrationData(array $overrides = []): array
    {
        return array_merge([
            'nama_lengkap' => $this->faker->name,
            'nim' => $this->faker->unique()->numerify('2021###'),
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'pilihan_dinas_2' => $this->dinas2->id,
            'motivasi' => 'Saya ingin belajar dan berkembang',
            'pengalaman' => 'Pernah magang di perusahaan startup',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ], $overrides);
    }

    // ==========================================
    // BAGIAN 1: TEST REGISTRASI (MAHASISWA)
    // ==========================================

    /** @test 1 */
    public function test_pendaftaran_dengan_data_valid_berhasil(): void
    {
        $response = $this->postJson('/register', $this->getValidRegistrationData());

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'role' => 'mahasiswa',
            'status' => 'aktif'
        ]);

        $this->assertDatabaseHas('pendaftaran', [
            'pilihan_dinas_1' => $this->dinas1->id,
            'status_pendaftaran' => 'terdaftar'
        ]);
    }

    /** @test 2 */
    public function test_pendaftaran_tanpa_nama_lengkap_gagal(): void
    {
        $data = $this->getValidRegistrationData(['nama_lengkap' => '']);
        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['nama_lengkap']);
    }

    /** @test 3 */
    public function test_pendaftaran_dengan_nim_duplikat_gagal(): void
    {
        User::factory()->mahasiswa()->create(['nim' => '2021003']);
        $data = $this->getValidRegistrationData(['nim' => '2021003']);

        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['nim']);
    }

    /** @test 4 */
    public function test_pendaftaran_dengan_email_duplikat_gagal(): void
    {
        User::factory()->mahasiswa()->create(['email' => 'sama@example.com']);
        $data = $this->getValidRegistrationData(['email' => 'sama@example.com']);

        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
    }

    /** @test 5 */
    public function test_pendaftaran_dengan_password_tidak_cocok_gagal(): void
    {
        $data = $this->getValidRegistrationData([
            'password' => 'password123',
            'password_confirmation' => 'beda123'
        ]);

        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['password']);
    }

    /** @test 6 */
    public function test_pendaftaran_dengan_password_pendek_gagal(): void
    {
        $data = $this->getValidRegistrationData([
            'password' => '123',
            'password_confirmation' => '123'
        ]);

        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['password']);
    }

    /** @test 7 */
    public function test_pendaftaran_tanpa_pilihan_dinas_1_gagal(): void
    {
        $data = $this->getValidRegistrationData(['pilihan_dinas_1' => '']);
        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['pilihan_dinas_1']);
    }

    /** @test 8 */
    public function test_pendaftaran_dengan_dinas_sama_gagal(): void
    {
        $data = $this->getValidRegistrationData([
            'pilihan_dinas_1' => $this->dinas1->id,
            'pilihan_dinas_2' => $this->dinas1->id
        ]);

        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['pilihan_dinas_2']);
    }

    /** @test 9 */
    public function test_pendaftaran_tanpa_file_cv_gagal(): void
    {
        $data = $this->getValidRegistrationData();
        unset($data['file_cv']);

        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['file_cv']);
    }

    /** @test 10 */
    public function test_pendaftaran_tanpa_file_transkrip_gagal(): void
    {
        $data = $this->getValidRegistrationData();
        unset($data['file_transkrip']);

        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['file_transkrip']);
    }

    /** @test 11 */
    public function test_pendaftaran_dengan_format_cv_tidak_valid_gagal(): void
    {
        $data = $this->getValidRegistrationData([
            'file_cv' => UploadedFile::fake()->create('cv.txt', 1000, 'text/plain')
        ]);
        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['file_cv']);
    }

    /** @test 12 */
    public function test_pendaftaran_dengan_ukuran_cv_melebihi_batas_gagal(): void
    {
        $data = $this->getValidRegistrationData([
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 6000)
        ]);
        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['file_cv']);
    }

    /** @test 13 */
    public function test_pendaftaran_dengan_format_email_tidak_valid_gagal(): void
    {
        $data = $this->getValidRegistrationData(['email' => 'email-tidak-valid']);
        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
    }

    /** @test 14 */
    public function test_pendaftaran_tanpa_motivasi_gagal(): void
    {
        $data = $this->getValidRegistrationData(['motivasi' => '']);
        $this->postJson('/register', $data)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['motivasi']);
    }

    /** @test 15 */
    public function test_pendaftaran_saat_info_or_tidak_aktif_gagal(): void
    {
        $this->infoOr->update(['status' => 'tutup']);
        $data = $this->getValidRegistrationData();

        $this->postJson('/register', $data)
             ->assertStatus(400)
             ->assertJson(['success' => false]);
    }

    // ==========================================
    // BAGIAN 2: TEST SUPERADMIN / ADMIN
    // ==========================================

    /** @test 16 */
    public function test_superadmin_dapat_melihat_halaman_index_pendaftar(): void
    {
        $response = $this->actingAs($this->superadmin)
                         ->get('/pendaftar');

        $response->assertStatus(200)
                 ->assertViewIs('pendaftar.index');
    }

    /** @test 17 */
    public function test_superadmin_dapat_melihat_detail_pendaftar(): void
    {
        // Perbaikan: Gunakan $this->superadmin, bukan $this->admin
        $response = $this->actingAs($this->superadmin)
                         ->get("/pendaftar/{$this->mahasiswa->id}");

        $response->assertStatus(200)
                 ->assertViewIs('pendaftar.show')
                 ->assertViewHas('pendaftar')
                 ->assertSee($this->mahasiswa->nama_lengkap);
    }


    /** @test 18 */
    public function test_superadmin_update_status_pendaftaran(): void
    {
        $statusList = [
            'terdaftar',
            'lulus_wawancara',
            'tidak_lulus_wawancara',
            'lulus_magang',
            'tidak_lulus_magang'
        ];

        foreach ($statusList as $status) {
            // Perbaikan: Gunakan $this->superadmin
            $response = $this->actingAs($this->superadmin)
                             ->put("/pendaftar/{$this->mahasiswa->id}/status", [
                                 'status' => $status
                             ]);

            $response->assertStatus(302)
                     ->assertSessionHas('success');

            $this->assertDatabaseHas('pendaftaran', [
                'user_id' => $this->mahasiswa->id,
                'status_pendaftaran' => $status
            ]);
        }
    }

    /** @test 19 */
    public function test_superadmin_set_dinas_diterima_untuk_pendaftar_lulus_wawancara(): void
    {
        // Set status ke lulus wawancara terlebih dahulu
        $this->pendaftaran->update(['status_pendaftaran' => 'lulus_wawancara']);

        // Perbaikan: Gunakan $this->superadmin
        $response = $this->actingAs($this->superadmin)
                         ->post("/pendaftar/{$this->mahasiswa->id}/dinas", [
                             'dinas_diterima_id' => $this->dinas1->id
                         ]);

        $response->assertStatus(302)
                 ->assertSessionHas('success', 'Dinas penerima berhasil ditetapkan.');

        $this->assertDatabaseHas('pendaftaran', [
            'user_id' => $this->mahasiswa->id,
            'dinas_diterima_id' => $this->dinas1->id
        ]);
    }
}