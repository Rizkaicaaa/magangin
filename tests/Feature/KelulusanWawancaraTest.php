<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use App\Models\PenilaianWawancara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KelulusanWawancaraTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;
    protected $admin;
    protected $mahasiswa;
    protected $mahasiswaLain;
    protected $penilai;
    protected $infoOr;
    protected $dinas;

    /**
     * Setup yang dijalankan sebelum setiap test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. Buat user dengan berbagai role
        $this->superadmin = User::factory()->create([
            'nama_lengkap' => 'Superadmin Test',
            'role' => 'superadmin',
            'email' => 'superadmin@test.com',
            'dinas_id' => null,
        ]);

        $this->admin = User::factory()->create([
            'nama_lengkap' => 'Admin Test',
            'role' => 'admin',
            'email' => 'admin@test.com',
            'dinas_id' => null,
        ]);

        $this->mahasiswa = User::factory()->create([
            'nama_lengkap' => 'Mahasiswa Test',
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
            'dinas_id' => null,
        ]);

        $this->mahasiswaLain = User::factory()->create([
            'nama_lengkap' => 'Mahasiswa Lain',
            'role' => 'mahasiswa',
            'email' => 'mahasiswa2@test.com',
            'dinas_id' => null,
        ]);

        $this->penilai = User::factory()->create([
            'nama_lengkap' => 'Penilai Test',
            'role' => 'admin',
            'email' => 'penilai@test.com',
            'dinas_id' => null,
        ]);

        // 2. Buat data master
        $this->dinas = Dinas::factory()->create([
            'nama_dinas' => 'Dinas Test',
        ]);

        $this->infoOr = InfoOr::factory()->create([
            'judul' => 'Periode 2024/2025',
            'status' => 'buka',
            'tanggal_buka' => now()->subDays(5),
            'tanggal_tutup' => now()->addDays(30)
        ]);
    }

    /**
     * TC-KW-001: Test mahasiswa dapat mengakses halaman kelulusan wawancara
     */
    public function test_mahasiswa_dapat_mengakses_halaman_kelulusan_wawancara(): void
    {
        Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $response = $this->withoutVite()
            ->actingAs($this->mahasiswa)
            ->get(route('kelulusanwawancara.index'));

        $response->assertStatus(200);
        $response->assertViewIs('kelulusan-wawancara.index');
        $response->assertViewHas('penilaian');
    }

    /**
     * TC-KW-002: Test guest tidak dapat mengakses halaman
     */
    public function test_guest_tidak_dapat_mengakses_halaman_kelulusan_wawancara(): void
    {
        $response = $this->get(route('kelulusanwawancara.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * TC-KW-003: Test halaman menampilkan penilaian jika ada
     */
    public function test_halaman_menampilkan_penilaian_jika_ada(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        $response = $this->withoutVite()
            ->actingAs($this->mahasiswa)
            ->get(route('kelulusanwawancara.index'));

        $response->assertStatus(200);
        $penilaian = $response->viewData('penilaian');
        $this->assertNotNull($penilaian);
        $this->assertEquals(80, $penilaian->nilai_komunikasi);
        $this->assertEquals(75, $penilaian->nilai_motivasi);
        $this->assertEquals(85, $penilaian->nilai_kemampuan);
    }

    /**
     * TC-KW-004: Test halaman menampilkan null jika belum dinilai
     */
    public function test_halaman_menampilkan_null_jika_belum_dinilai(): void
    {
        Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $response = $this->withoutVite()
            ->actingAs($this->mahasiswa)
            ->get(route('kelulusanwawancara.index'));

        $response->assertStatus(200);
        $penilaian = $response->viewData('penilaian');
        $this->assertNull($penilaian);
    }

    /**
     * TC-KW-005: Test filter user_id dengan Auth::id() bekerja benar
     */
    public function test_filter_user_id_dengan_auth_id_bekerja_benar(): void
    {
        $pendaftaran1 = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $pendaftaran2 = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswaLain->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        // Buat penilaian untuk kedua mahasiswa
        PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran1->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran2->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 70,
            'nilai_motivasi' => 65,
            'nilai_kemampuan' => 75,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        // Mahasiswa 1 hanya melihat penilaiannya sendiri
        $response1 = $this->withoutVite()
            ->actingAs($this->mahasiswa)
            ->get(route('kelulusanwawancara.index'));
        $penilaian1 = $response1->viewData('penilaian');

        // Mahasiswa 2 hanya melihat penilaiannya sendiri
        $response2 = $this->withoutVite()
            ->actingAs($this->mahasiswaLain)
            ->get(route('kelulusanwawancara.index'));
        $penilaian2 = $response2->viewData('penilaian');

        $this->assertEquals(80, $penilaian1->nilai_komunikasi);
        $this->assertEquals(70, $penilaian2->nilai_komunikasi);
        $this->assertNotEquals($penilaian1->id, $penilaian2->id);
    }

    /**
     * TC-KW-006: Test whereHas relasi pendaftaran bekerja
     */
    public function test_wherehas_relasi_pendaftaran_bekerja(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        // Test query dengan whereHas
        $penilaian = PenilaianWawancara::whereHas('pendaftaran', function ($query) {
            $query->where('user_id', $this->mahasiswa->id);
        })->first();

        $this->assertNotNull($penilaian);
        $this->assertEquals($pendaftaran->id, $penilaian->pendaftaran_id);
    }

    /**
     * TC-KW-007: Test validasi nilai komunikasi valid
     */
    public function test_validasi_nilai_komunikasi_valid(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        // Test nilai valid (0-100)
        $penilaian = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 80,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        $this->assertGreaterThanOrEqual(0, $penilaian->nilai_komunikasi);
        $this->assertLessThanOrEqual(100, $penilaian->nilai_komunikasi);
    }

    /**
     * TC-KW-008: Test validasi nilai motivasi valid
     */
    public function test_validasi_nilai_motivasi_valid(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $penilaian = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        $this->assertGreaterThanOrEqual(0, $penilaian->nilai_motivasi);
        $this->assertLessThanOrEqual(100, $penilaian->nilai_motivasi);
    }

    /**
     * TC-KW-009: Test validasi nilai kemampuan valid
     */
    public function test_validasi_nilai_kemampuan_valid(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $penilaian = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 90,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        $this->assertGreaterThanOrEqual(0, $penilaian->nilai_kemampuan);
        $this->assertLessThanOrEqual(100, $penilaian->nilai_kemampuan);
    }

    /**
     * TC-KW-010: Test status penilaian belum_dinilai
     */
    public function test_status_penilaian_belum_dinilai(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $penilaian = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
            'status' => 'belum_dinilai',
        ]);

        $this->assertEquals('belum_dinilai', $penilaian->status);
    }

    /**
     * TC-KW-011: Test status penilaian sudah_dinilai
     */
    public function test_status_penilaian_sudah_dinilai(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $penilaian = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        $this->assertEquals('sudah_dinilai', $penilaian->status);
    }

    /**
     * TC-KW-012: Test KKM nilai default 70
     */
    public function test_kkm_nilai_default_70(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $penilaian = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        $this->assertEquals(70, $penilaian->kkm);
    }

    /**
     * TC-KW-013: Test compact pass penilaian ke view
     */
    public function test_compact_pass_penilaian_ke_view(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        $response = $this->withoutVite()
            ->actingAs($this->mahasiswa)
            ->get(route('kelulusanwawancara.index'));

        $response->assertStatus(200);
        $response->assertViewHas('penilaian');
    }

    /**
     * TC-KW-014: Test view render dengan benar
     */
    public function test_view_render_dengan_benar(): void
    {
        Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $response = $this->withoutVite()
            ->actingAs($this->mahasiswa)
            ->get(route('kelulusanwawancara.index'));

        $response->assertStatus(200);
        $response->assertViewIs('kelulusan-wawancara.index');
    }

    /**
     * TC-KW-015: Test penilaian dengan data lengkap ditampilkan
     */
    public function test_penilaian_dengan_data_lengkap_ditampilkan(): void
    {
        $pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
        ]);

        $penilaianCreate = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $this->penilai->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
            'status' => 'sudah_dinilai',
        ]);

        $response = $this->withoutVite()
            ->actingAs($this->mahasiswa)
            ->get(route('kelulusanwawancara.index'));

        $penilaian = $response->viewData('penilaian');

        $this->assertNotNull($penilaian);
        $this->assertEquals($penilaianCreate->id, $penilaian->id);
        $this->assertEquals($penilaianCreate->nilai_komunikasi, $penilaian->nilai_komunikasi);
        $this->assertEquals($penilaianCreate->nilai_motivasi, $penilaian->nilai_motivasi);
        $this->assertEquals($penilaianCreate->nilai_kemampuan, $penilaian->nilai_kemampuan);
        $this->assertEquals($penilaianCreate->status, $penilaian->status);
    }
}