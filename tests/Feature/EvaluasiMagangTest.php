<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use App\Models\EvaluasiMagangModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class EvaluasiMagangTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $mahasiswa;
    protected $dinas;
    protected $infoOr;
    protected $pendaftaran;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->dinas = Dinas::create([
            'nama_dinas' => 'Dinas Test',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
            'dinas_id' => $this->dinas->id,
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
            'dinas_id' => null,
        ]);

        $this->infoOr = InfoOr::factory()->create([
            'periode' => '2024/2025',
            'status' => 'buka',
        ]);

        $this->pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'dinas_diterima_id' => $this->dinas->id,
            'status_pendaftaran' => 'lulus_wawancara',
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
            'file_cv' => 'dummy_cv.pdf',
            'file_transkrip' => 'dummy_transkrip.pdf',
        ]);
    }

    public function test_admin_dapat_mengakses_halaman_evaluasi_magang()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('penilaian.index'));

        $response->assertStatus(200);
        $response->assertViewIs('penilaian.index');
        $response->assertViewHas(['pendaftar', 'penilaian']);
    }

    public function test_nilai_total_dihitung_dengan_benar_untuk_berbagai_kombinasi()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 100,
            'nilai_kerjasama' => 90,
            'nilai_inisiatif' => 80,
            'nilai_hasil_kerja' => 70,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertStatus(302);

        // Nilai total = (100 + 90 + 80 + 70) / 4 = 85
        $this->assertDatabaseHas('evaluasi_magang', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_total' => 85,
            'hasil_evaluasi' => 'lulus',
        ]);
    }

    public function test_guest_tidak_dapat_mengakses_halaman_evaluasi()
    {
        $response = $this->get(route('penilaian.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_dapat_membuat_evaluasi_baru_dengan_data_valid()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 85,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertStatus(302);
        $response->assertRedirect(route('penilaian.index'));
        $response->assertSessionHas('success', 'Penilaian berhasil disimpan!');

        // Nilai total = (85 + 80 + 90 + 85) / 4 = 85
        $this->assertDatabaseHas('evaluasi_magang', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 85,
            'nilai_total' => 85,
            'hasil_evaluasi' => 'lulus',
        ]);
    }

    public function test_evaluasi_dengan_nilai_70_keatas_menghasilkan_status_lulus()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 70,
            'nilai_kerjasama' => 70,
            'nilai_inisiatif' => 70,
            'nilai_hasil_kerja' => 70,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertStatus(302);

        $this->assertDatabaseHas('evaluasi_magang', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_total' => 70,
            'hasil_evaluasi' => 'lulus',
        ]);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'status_pendaftaran' => 'lulus_magang',
        ]);
    }

    public function test_evaluasi_dengan_nilai_dibawah_70_menghasilkan_status_tidak_lulus()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 60,
            'nilai_kerjasama' => 65,
            'nilai_inisiatif' => 68,
            'nilai_hasil_kerja' => 69,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertStatus(302);

        // Nilai total = (60 + 65 + 68 + 69) / 4 = 65.5
        $this->assertDatabaseHas('evaluasi_magang', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_total' => 65.5,
            'hasil_evaluasi' => 'tidak_lulus',
        ]);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'status_pendaftaran' => 'tidak_lulus_magang',
        ]);
    }

    public function test_admin_dapat_update_evaluasi_yang_sudah_ada()
    {
        $evaluasi = EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'penilai_id' => $this->admin->id,
            'nilai_kedisiplinan' => 70,
            'nilai_kerjasama' => 70,
            'nilai_inisiatif' => 70,
            'nilai_hasil_kerja' => 70,
            'nilai_total' => 70,
            'hasil_evaluasi' => 'lulus',
        ]);

        $dataUpdate = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 90,
            'nilai_kerjasama' => 85,
            'nilai_inisiatif' => 88,
            'nilai_hasil_kerja' => 92,
        ];

        // Gunakan PUT request dengan id di URL
        $response = $this->actingAs($this->admin)
            ->put(route('penilaian.update', $evaluasi->id), $dataUpdate);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Nilai total = (90 + 85 + 88 + 92) / 4 = 88.75
        $this->assertDatabaseHas('evaluasi_magang', [
            'id' => $evaluasi->id,
            'nilai_kedisiplinan' => 90,
            'nilai_total' => 88.75,
        ]);
    }

    public function test_validasi_pendaftaran_id_wajib_diisi()
    {
        $dataEvaluasi = [
            'nilai_kedisiplinan' => 85,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors(['pendaftaran_id']);
    }

    public function test_validasi_pendaftaran_id_harus_exist()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => 99999,
            'nilai_kedisiplinan' => 85,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors(['pendaftaran_id']);
    }

    public function test_validasi_nilai_kedisiplinan_wajib_diisi()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors(['nilai_kedisiplinan']);
    }

    public function test_validasi_nilai_kedisiplinan_harus_numeric()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 'abc',
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors(['nilai_kedisiplinan']);
    }

    public function test_validasi_nilai_kedisiplinan_min_0()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => -1,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors(['nilai_kedisiplinan']);
    }

    public function test_validasi_nilai_kedisiplinan_max_100()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 101,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors(['nilai_kedisiplinan']);
    }

    public function test_validasi_nilai_kerjasama_wajib_diisi()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 85,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors(['nilai_kerjasama']);
    }

    public function test_validasi_nilai_inisiatif_wajib_diisi()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 85,
            'nilai_kerjasama' => 80,
            'nilai_hasil_kerja' => 85,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors(['nilai_inisiatif']);
    }

    public function test_validasi_nilai_hasil_kerja_wajib_diisi()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 85,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors(['nilai_hasil_kerja']);
    }

    public function test_validasi_semua_nilai_harus_valid()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 'abc',
            'nilai_kerjasama' => -5,
            'nilai_inisiatif' => 150,
            'nilai_hasil_kerja' => 'xyz',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertSessionHasErrors([
            'nilai_kedisiplinan',
            'nilai_kerjasama',
            'nilai_inisiatif',
            'nilai_hasil_kerja'
        ]);
    }

    public function test_admin_dapat_menghapus_evaluasi()
    {
        $evaluasi = EvaluasiMagangModel::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'penilai_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('penilaian.destroy', $evaluasi->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('penilaian.index'));
        $response->assertSessionHas('success', 'Penilaian berhasil dihapus!');

        $this->assertDatabaseMissing('evaluasi_magang', [
            'id' => $evaluasi->id
        ]);
    }

    public function test_evaluasi_tidak_bisa_dibuat_untuk_pendaftaran_yang_sama_dua_kali()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 85,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        // Create pertama kali
        $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        // Coba create lagi dengan data yang sama
        $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        // Cek hanya ada 1 evaluasi (controller harusnya handle duplicate)
        $count = EvaluasiMagangModel::where('pendaftaran_id', $this->pendaftaran->id)->count();
        $this->assertEquals(1, $count);
    }

    public function test_penilai_id_tersimpan_dengan_benar()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 85,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 85,
        ];

        $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $this->assertDatabaseHas('evaluasi_magang', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'penilai_id' => $this->admin->id,
        ]);
    }

    public function test_perhitungan_nilai_total_benar()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 80,
            'nilai_kerjasama' => 85,
            'nilai_inisiatif' => 90,
            'nilai_hasil_kerja' => 75,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        $response->assertStatus(302);

        // Nilai total = (80 + 85 + 90 + 75) / 4 = 82.5
        $this->assertDatabaseHas('evaluasi_magang', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_total' => 82.5,
        ]);
    }

    public function test_status_pendaftaran_berubah_sesuai_hasil_evaluasi()
    {
        $dataLulus = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 80,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 80,
            'nilai_hasil_kerja' => 80,
        ];

        $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataLulus);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'status_pendaftaran' => 'lulus_magang',
        ]);

        $evaluasi = EvaluasiMagangModel::where('pendaftaran_id', $this->pendaftaran->id)->first();
        
        $dataTidakLulus = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 60,
            'nilai_kerjasama' => 60,
            'nilai_inisiatif' => 60,
            'nilai_hasil_kerja' => 60,
        ];

        // Gunakan PUT untuk update
        $this->actingAs($this->admin)
            ->put(route('penilaian.update', $evaluasi->id), $dataTidakLulus);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'status_pendaftaran' => 'tidak_lulus_magang',
        ]);
    }

    public function test_hanya_pendaftar_lulus_wawancara_yang_ditampilkan()
    {
        $pendaftarLain = Pendaftaran::factory()->create([
            'user_id' => User::factory()->create(['role' => 'mahasiswa'])->id,
            'info_or_id' => $this->infoOr->id,
            'dinas_diterima_id' => $this->dinas->id,
            'status_pendaftaran' => 'lulus_magang',
            'pilihan_dinas_1' => $this->dinas->id,
            'pilihan_dinas_2' => $this->dinas->id,
            'file_cv' => 'dummy_cv.pdf',
            'file_transkrip' => 'dummy_transkrip.pdf',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('penilaian.index'));

        $response->assertStatus(200);
        
        $pendaftarView = $response->viewData('pendaftar');
        
        $this->assertTrue($pendaftarView->contains('id', $this->pendaftaran->id));
        $this->assertFalse($pendaftarView->contains('id', $pendaftarLain->id));
    }

    public function test_admin_hanya_melihat_pendaftar_dari_dinasnya()
    {
        $dinasLain = Dinas::create([
            'nama_dinas' => 'Dinas Lain',
        ]);
        
        $pendaftarDinasLain = Pendaftaran::factory()->create([
            'user_id' => User::factory()->create(['role' => 'mahasiswa'])->id,
            'info_or_id' => $this->infoOr->id,
            'dinas_diterima_id' => $dinasLain->id,
            'status_pendaftaran' => 'lulus_wawancara',
            'pilihan_dinas_1' => $dinasLain->id,
            'pilihan_dinas_2' => $dinasLain->id,
            'file_cv' => 'dummy_cv.pdf',
            'file_transkrip' => 'dummy_transkrip.pdf',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('penilaian.index'));

        $response->assertStatus(200);
        
        $pendaftarView = $response->viewData('pendaftar');
        
        $this->assertTrue($pendaftarView->contains('id', $this->pendaftaran->id));
        $this->assertFalse($pendaftarView->contains('id', $pendaftarDinasLain->id));
    }

    public function test_nilai_boundary_6999_menghasilkan_tidak_lulus()
    {
        $dataEvaluasi = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_kedisiplinan' => 69,
            'nilai_kerjasama' => 70,
            'nilai_inisiatif' => 70,
            'nilai_hasil_kerja' => 70,
        ];

        $this->actingAs($this->admin)
            ->post(route('penilaian.store'), $dataEvaluasi);

        // Nilai total = (69 + 70 + 70 + 70) / 4 = 69.75
        $this->assertDatabaseHas('evaluasi_magang', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'hasil_evaluasi' => 'tidak_lulus',
        ]);
    }
}