<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Dinas;
use App\Models\InfoOr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class pendaftaran_backup extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $dinas1;
    protected $dinas2;
    protected $infoOr;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        // Buat data dinas
        $this->dinas1 = Dinas::create([
            'nama_dinas' => 'Dinas Pendidikan',
            'deskripsi' => 'Dinas Pendidikan Kota',
            'kontak_person' => '081234567890'
        ]);
        
        $this->dinas2 = Dinas::create([
            'nama_dinas' => 'Dinas Kesehatan',
            'deskripsi' => 'Dinas Kesehatan Kota',
            'kontak_person' => '081234567891'
        ]);
        
        // Buat Info OR
        $this->infoOr = InfoOr::create([
            'judul' => 'Pendaftaran Magang 2024',
            'deskripsi' => 'Pendaftaran magang periode 2024',
            'persyaratan_umum' => 'Mahasiswa aktif',
            'tanggal_buka' => now()->subDays(5),
            'tanggal_tutup' => now()->addDays(30),
            'periode' => '2024-1',
            'status' => 'buka'
        ]);
    }

    /**
     * TEST CASE 1: Pendaftaran dengan data valid dan lengkap
     */
    public function test_pendaftaran_dengan_data_valid_berhasil()
    {
        // PERBAIKAN: Menggunakan endpoint '/register' sesuai route auth.php
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Budi Santoso',
            'nim' => '2021001',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'pilihan_dinas_2' => $this->dinas2->id,
            'motivasi' => 'Saya ingin belajar',
            'pengalaman' => 'Pernah magang',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        // Verifikasi User
        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'nim' => '2021001',
        ]);

        // Verifikasi Pendaftaran
        $this->assertDatabaseHas('pendaftaran', [
            'pilihan_dinas_1' => $this->dinas1->id,
            'status_pendaftaran' => 'terdaftar' // Pastikan controller mengisi status ini
        ]);
    }

    /**
     * TEST CASE 2: Pendaftaran tanpa nama lengkap
     */
    public function test_pendaftaran_tanpa_nama_lengkap_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => '', // Kosong
            'nim' => '2021002',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000)
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nama_lengkap']);
    }

    /**
     * TEST CASE 3: Pendaftaran dengan NIM duplikat
     */
    public function test_pendaftaran_dengan_nim_duplikat_gagal()
    {
        // User existing
        User::create([
            'nama_lengkap' => 'User Awal',
            'nim' => '2021003',
            'email' => 'awal@example.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa'
        ]);

        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User Kedua',
            'nim' => '2021003', // Duplikat
            'email' => 'kedua@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567899',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000)
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nim']);
    }

    /**
     * TEST CASE 4: Pendaftaran dengan email duplikat
     */
    public function test_pendaftaran_dengan_email_duplikat_gagal()
    {
        User::create([
            'nama_lengkap' => 'User Awal',
            'nim' => '2021004',
            'email' => 'sama@example.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa'
        ]);

        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User Kedua',
            'nim' => '2021005',
            'email' => 'sama@example.com', // Duplikat
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567899',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000)
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * TEST CASE 5: Password tidak match
     */
    public function test_pendaftaran_dengan_password_tidak_cocok_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User',
            'nim' => '2021006',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'beda123', // Beda
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /**
     * TEST CASE 6: Password kependekan
     */
    public function test_pendaftaran_dengan_password_pendek_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User',
            'nim' => '2021007',
            'email' => 'user@example.com',
            'password' => '123', // Pendek
            'password_confirmation' => '123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /**
     * TEST CASE 7: Pilihan dinas 1 kosong
     */
    public function test_pendaftaran_tanpa_pilihan_dinas_1_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User',
            'nim' => '2021008',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => '', // Kosong
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['pilihan_dinas_1']);
    }

    /**
     * TEST CASE 8: Pilihan dinas sama
     */
    public function test_pendaftaran_dengan_dinas_sama_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User',
            'nim' => '2021009',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'pilihan_dinas_2' => $this->dinas1->id, // Sama
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['pilihan_dinas_2']);
    }

    /**
     * TEST CASE 9: File CV kosong
     */
    public function test_pendaftaran_tanpa_file_cv_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User',
            'nim' => '2021010',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf')
            // CV missing
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file_cv']);
    }

    /**
     * TEST CASE 10: File Transkrip kosong
     */
    public function test_pendaftaran_tanpa_file_transkrip_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User',
            'nim' => '2021011',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf')
            // Transkrip missing
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file_transkrip']);
    }

    /**
     * TEST CASE 11: Format CV salah
     */
    public function test_pendaftaran_dengan_format_cv_tidak_valid_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User',
            'nim' => '2021012',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.txt', 1000, 'text/plain'), // TXT salah
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file_cv']);
    }

    /**
     * TEST CASE 12: Ukuran CV > 5MB
     */
    public function test_pendaftaran_dengan_ukuran_cv_melebihi_batas_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'User',
            'nim' => '2021013',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 6000), // 6000KB > 5MB
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file_cv']);
    }

    /**
     * TEST CASE 13: Pendaftaran dengan format email tidak valid
     */
    public function test_pendaftaran_dengan_format_email_tidak_valid_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021014',
            'email' => 'email-tidak-valid', // Format salah
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * TEST CASE 14: Pendaftaran tanpa motivasi
     */
    public function test_pendaftaran_tanpa_motivasi_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021015',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => '', // Kosong
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['motivasi']);
    }

    /**
     * TEST CASE 15: Pendaftaran saat Info OR tidak aktif
     */
    public function test_pendaftaran_saat_info_or_tidak_aktif_gagal()
    {
        // Update Info OR menjadi tutup
        $this->infoOr->update(['status' => 'tutup']);

        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021016',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        // Asumsi Controller mengembalikan 400 jika periode tutup
        $response->assertStatus(400);
    }

    /**
     * TEST CASE 16: Pendaftaran tanpa pengalaman berhasil (opsional)
     */
    public function test_pendaftaran_tanpa_pengalaman_berhasil()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021017',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'pengalaman' => '', // Kosong tapi opsional
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'nim' => '2021017'
        ]);
    }

    /**
     * TEST CASE 17: Pendaftaran tanpa pilihan dinas 2 berhasil
     */
    public function test_pendaftaran_tanpa_pilihan_dinas_2_berhasil()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021018',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            // pilihan_dinas_2 tidak disertakan
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('pendaftaran', [
            'pilihan_dinas_1' => $this->dinas1->id,
            'pilihan_dinas_2' => null
        ]);
    }

    /**
     * TEST CASE 18: Verifikasi password ter-hash
     */
    public function test_password_terhash_dengan_benar()
    {
        $password = 'password123';
        
        $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021019',
            'email' => 'test@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf')
        ]);

        $user = User::where('nim', '2021019')->first();
        
        // PERBAIKAN: Cek apakah user ada sebelum cek password untuk menghindari error null
        $this->assertNotNull($user, 'User tidak ditemukan di database. Pastikan route /register berfungsi.');
        
        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /**
     * TEST CASE 19: Verifikasi status pendaftaran default
     */
    public function test_status_pendaftaran_default_adalah_terdaftar()
    {
        $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021020',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $this->assertDatabaseHas('pendaftaran', [
            'status_pendaftaran' => 'terdaftar'
        ]);
    }

    /**
     * TEST CASE 20: Verifikasi role user adalah mahasiswa
     */
    public function test_role_user_adalah_mahasiswa()
    {
        $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021021',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $this->assertDatabaseHas('users', [
            'nim' => '2021021',
            'role' => 'mahasiswa'
        ]);
    }

    /**
     * TEST CASE 21: Verifikasi relasi antara User dan Pendaftaran
     */
    public function test_relasi_user_dan_pendaftaran_benar()
    {
        $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021022',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'pilihan_dinas_2' => $this->dinas2->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $user = User::where('nim', '2021022')->first();
        
        $this->assertNotNull($user);
        $pendaftaran = $user->pendaftaran->first();

        $this->assertNotNull($pendaftaran);
        $this->assertEquals($user->id, $pendaftaran->user_id);
        $this->assertEquals($this->dinas1->id, $pendaftaran->pilihan_dinas_1);
        $this->assertEquals($this->dinas2->id, $pendaftaran->pilihan_dinas_2);
    }

    /**
     * TEST CASE 22: Pendaftaran dengan motivasi maksimal 500 karakter
     */
    public function test_pendaftaran_dengan_motivasi_500_karakter_berhasil()
    {
        $motivasi = str_repeat('a', 500); // Tepat 500 karakter

        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021023',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => $motivasi,
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
    }

    /**
     * TEST CASE 23: Pendaftaran dengan motivasi melebihi 500 karakter
     */
    public function test_pendaftaran_dengan_motivasi_lebih_500_karakter_gagal()
    {
        $motivasi = str_repeat('a', 501); // 501 karakter

        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021024',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => $motivasi,
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['motivasi']);
    }

    /**
     * TEST CASE 24: Pendaftaran dengan NIM maksimal 10 karakter
     */
    public function test_pendaftaran_dengan_nim_10_karakter_berhasil()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '1234567890', // 10 karakter
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
    }

    /**
     * TEST CASE 25: Pendaftaran dengan NIM melebihi 10 karakter
     */
    public function test_pendaftaran_dengan_nim_lebih_10_karakter_gagal()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '12345678901', // 11 karakter
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nim']);
    }

    /**
     * TEST CASE 26: Verifikasi transaksi database rollback saat error
     */
    public function test_rollback_database_saat_error()
    {
        // Paksa error dengan ID dinas yang tidak valid
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021026',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => 99999, // ID tidak ada
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        // Pastikan user tidak tersimpan
        $this->assertDatabaseMissing('users', [
            'nim' => '2021026'
        ]);

        // Pastikan pendaftaran juga tidak tersimpan
        $this->assertDatabaseMissing('pendaftaran', [
            'pilihan_dinas_1' => 99999
        ]);
    }

    /**
     * TEST CASE 27: Verifikasi tanggal_daftar ter-set otomatis
     */
    public function test_tanggal_daftar_terisi_otomatis()
    {
        $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021027',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf')
        ]);

        $user = User::where('nim', '2021027')->first();
        
        $this->assertNotNull($user);
        $this->assertNotNull($user->tanggal_daftar);
        $this->assertEquals(now()->format('Y-m-d'), $user->tanggal_daftar->format('Y-m-d'));
    }

    /**
     * TEST CASE 28: Pendaftaran dengan no_telp maksimal 13 karakter
     */
    public function test_pendaftaran_dengan_no_telp_13_karakter_berhasil()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021028',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '0812345678901', // 13 karakter
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.pdf', 1000, 'application/pdf'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
    }

    /**
     * TEST CASE 29: Pendaftaran dengan format transkrip JPG
     */
    public function test_pendaftaran_dengan_transkrip_jpg_berhasil()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021029',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi',
            'file_cv' => UploadedFile::fake()->create('cv.pdf'),
            // PERBAIKAN: Gunakan create() dengan MIME type agar tidak perlu GD library
            'file_transkrip' => UploadedFile::fake()->create('transkrip.jpg', 1000, 'image/jpeg') 
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
    }

    /**
     * TEST CASE 30: Pendaftaran dengan format CV DOCX
     */
    public function test_pendaftaran_dengan_cv_docx_berhasil()
    {
        $response = $this->postJson('/register', [
            'nama_lengkap' => 'Test User',
            'nim' => '2021030',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'pilihan_dinas_1' => $this->dinas1->id,
            'motivasi' => 'Motivasi test',
            'file_cv' => UploadedFile::fake()->create('cv.docx', 1000, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'file_transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000, 'application/pdf')
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
    }
}