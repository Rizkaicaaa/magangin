<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use App\Models\JadwalSeleksi;
use App\Models\PenilaianWawancara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PenilaianWawancaraTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $mahasiswa;
    protected $dinas;
    protected $infoOr;
    protected $pendaftaran;
    protected $jadwalSeleksi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dinas = DB::table('dinas')->insertGetId([
            'nama_dinas' => 'Dinas Test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->dinas = (object)['id' => $this->dinas];

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
            'nama_lengkap' => 'Admin Test',
            'dinas_id' => $this->dinas->id,
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
            'nama_lengkap' => 'Mahasiswa Test', 
            'dinas_id' => null,
        ]);

        $this->infoOr = InfoOr::factory()->create([
            'periode' => '2024/2025',
            'status' => 'buka',
        ]);

        $this->pendaftaran = new Pendaftaran();
        $this->pendaftaran->user_id = $this->mahasiswa->id;
        $this->pendaftaran->info_or_id = $this->infoOr->id;
        $this->pendaftaran->dinas_diterima_id = $this->dinas->id;
        $this->pendaftaran->status_pendaftaran = 'lulus_wawancara';
        $this->pendaftaran->pilihan_dinas_1 = $this->dinas->id;
        $this->pendaftaran->pilihan_dinas_2 = $this->dinas->id;
        $this->pendaftaran->file_cv = 'dummy_cv.pdf';
        $this->pendaftaran->file_transkrip = 'dummy_transkrip.pdf';
        $this->pendaftaran->motivasi = 'Motivasi test';
        $this->pendaftaran->pengalaman = 'Pengalaman test';
        $this->pendaftaran->tanggal_daftar = now();
        $this->pendaftaran->save();

        $this->jadwalSeleksi = JadwalSeleksi::factory()->create([
            'info_or_id' => $this->infoOr->id,
            'tanggal_seleksi' => now()->addDays(7),
            'pewawancara' => 'Admin Test',
        ]);
    }

    public function test_admin_dapat_mengakses_halaman_index_penilaian_wawancara()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('penilaian-wawancara.index'));

        $response->assertStatus(200);
        $response->assertViewIs('penilaian-wawancara.index');
        $response->assertViewHas(['data', 'kkm']);
    }

    public function test_admin_dapat_mengakses_halaman_create_penilaian_wawancara()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('penilaian-wawancara.create'));

        $response->assertStatus(200);
        $response->assertViewIs('penilaian-wawancara.create');
        $response->assertViewHas(['jadwalseleksi', 'penilaianExist']);
    }

    public function test_admin_dapat_membuat_penilaian_wawancara_dengan_data_valid()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
            'kkm' => 70,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response->assertStatus(302);
        $response->assertRedirect(route('penilaian-wawancara.index'));
        $response->assertSessionHas('success', 'Penilaian berhasil ditambahkan.');

        $this->assertDatabaseHas('penilaian_wawancara', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
            'nilai_total' => 255,
            'nilai_rata_rata' => 85,
            'status' => 'sudah_dinilai',
        ]);
    }

    public function test_perhitungan_nilai_total_dan_rata_rata_benar()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 85,
            'nilai_kemampuan' => 90,
            'kkm' => 70,
        ];

        $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $this->assertDatabaseHas('penilaian_wawancara', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_total' => 255,
            'nilai_rata_rata' => 85,
        ]);
    }

    public function test_status_otomatis_sudah_dinilai_jika_ada_nilai()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 75,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 85,
            'kkm' => 70,
        ];

        $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $this->assertDatabaseHas('penilaian_wawancara', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'status' => 'sudah_dinilai',
        ]);
    }

    public function test_status_otomatis_belum_dinilai_jika_tidak_ada_nilai()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => null,
            'nilai_motivasi' => null,
            'nilai_kemampuan' => null,
            'kkm' => 70,
        ];

        $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $this->assertDatabaseHas('penilaian_wawancara', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'status' => 'belum_dinilai',
            'nilai_total' => 0,
            'nilai_rata_rata' => 0,
        ]);
    }

    public function test_tidak_bisa_membuat_penilaian_duplikat_untuk_peserta_yang_sama()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
            'kkm' => 70,
        ];

        $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Peserta ini sudah memiliki penilaian.');

        $count = PenilaianWawancara::where('pendaftaran_id', $this->pendaftaran->id)->count();
        $this->assertEquals(1, $count);
    }

    public function test_validasi_pendaftaran_id_wajib_diisi()
    {
        $dataPenilaian = [
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response->assertSessionHasErrors(['pendaftaran_id']);
    }

    public function test_validasi_pendaftaran_id_harus_exist()
    {
        $dataPenilaian = [
            'pendaftaran_id' => 99999,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response->assertSessionHasErrors(['pendaftaran_id']);
    }

    public function test_validasi_jadwal_seleksi_id_wajib_diisi()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response->assertSessionHasErrors(['jadwal_seleksi_id']);
    }

    public function test_validasi_nilai_komunikasi_harus_numeric()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 'abc',
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response->assertSessionHasErrors(['nilai_komunikasi']);
    }

    public function test_validasi_nilai_komunikasi_min_0()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => -1,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response->assertSessionHasErrors(['nilai_komunikasi']);
    }

    public function test_validasi_nilai_komunikasi_max_100()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 101,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response->assertSessionHasErrors(['nilai_komunikasi']);
    }

    public function test_validasi_semua_nilai_harus_valid()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 'abc',
            'nilai_motivasi' => -5,
            'nilai_kemampuan' => 150,
            'kkm' => 'xyz',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $response->assertSessionHasErrors([
            'nilai_komunikasi',
            'nilai_motivasi',
            'nilai_kemampuan',
            'kkm',
        ]);
    }

    public function test_admin_dapat_mengakses_halaman_edit()
    {
        $penilaian = PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'penilai_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('penilaian-wawancara.edit', $penilaian->id));

        $response->assertStatus(200);
        $response->assertViewIs('penilaian-wawancara.edit');
        $response->assertViewHas(['penilaianWawancara', 'peserta', 'jadwalseleksi']);
    }

    public function test_admin_dapat_update_penilaian_wawancara()
    {
        $penilaian = PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'penilai_id' => $this->admin->id,
            'nilai_komunikasi' => 70,
            'nilai_motivasi' => 70,
            'nilai_kemampuan' => 70,
        ]);

        $dataUpdate = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 90,
            'nilai_motivasi' => 85,
            'nilai_kemampuan' => 88,
            'kkm' => 75,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('penilaian-wawancara.update', $penilaian->id), $dataUpdate);

        $response->assertStatus(302);
        $response->assertRedirect(route('penilaian-wawancara.index'));
        $response->assertSessionHas('success', 'Penilaian berhasil diperbarui.');

        $this->assertDatabaseHas('penilaian_wawancara', [
            'id' => $penilaian->id,
            'nilai_komunikasi' => 90,
            'nilai_motivasi' => 85,
            'nilai_kemampuan' => 88,
        ]);
    }

    public function test_admin_dapat_menghapus_penilaian_wawancara()
    {
        $penilaian = PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'penilai_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('penilaian-wawancara.destroy', $penilaian->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('penilaian-wawancara.index'));
        $response->assertSessionHas('success', 'Penilaian berhasil dihapus.');

        $this->assertDatabaseMissing('penilaian_wawancara', [
            'id' => $penilaian->id,
        ]);
    }

    public function test_admin_dapat_melihat_detail_penilaian()
    {
        $penilaian = PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'penilai_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('penilaian-wawancara.show', $penilaian->id));

        $response->assertStatus(200);
        $response->assertViewIs('penilaian-wawancara.show');
        $response->assertViewHas('penilaian');
    }

    public function test_update_status_pendaftaran_berdasarkan_kkm_lulus()
    {
        $penilaian = PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'penilai_id' => $this->admin->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 85,
            'nilai_kemampuan' => 90,
            'nilai_total' => 255,
            'nilai_rata_rata' => 85,
            'kkm' => null,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('penilaian-wawancara.updateStatus'), [
                'kkm' => 70
            ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Status pendaftaran dan KKM berhasil diperbarui!']);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'status_pendaftaran' => 'lulus_wawancara',
        ]);

        $this->assertDatabaseHas('penilaian_wawancara', [
            'id' => $penilaian->id,
            'kkm' => 70,
        ]);
    }

    public function test_update_status_pendaftaran_berdasarkan_kkm_tidak_lulus()
    {
        $penilaian = PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'penilai_id' => $this->admin->id,
            'nilai_komunikasi' => 60,
            'nilai_motivasi' => 65,
            'nilai_kemampuan' => 68,
            'nilai_total' => 193,
            'nilai_rata_rata' => 64.33,
            'kkm' => null,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('penilaian-wawancara.updateStatus'), [
                'kkm' => 70
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'status_pendaftaran' => 'tidak_lulus_wawancara',
        ]);
    }

    public function test_update_status_gagal_jika_kkm_tidak_valid()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('penilaian-wawancara.updateStatus'), [
                'kkm' => 0
            ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'KKM tidak valid!']);
    }

    public function test_kkm_tersimpan_saat_membuat_penilaian()
    {
        $dataPenilaian = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 80,
            'nilai_kemampuan' => 90,
            'kkm' => 75,
        ];

        $this->actingAs($this->admin)
            ->post(route('penilaian-wawancara.store'), $dataPenilaian);

        $this->assertDatabaseHas('penilaian_wawancara', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'kkm' => 75,
        ]);
    }

    public function test_nilai_boundary_tepat_sama_dengan_kkm_menghasilkan_lulus()
    {
        $penilaian = PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'penilai_id' => $this->admin->id,
            'nilai_komunikasi' => 70,
            'nilai_motivasi' => 70,
            'nilai_kemampuan' => 70,
            'nilai_total' => 210,
            'nilai_rata_rata' => 70,
            'kkm' => null,
        ]);

        $this->actingAs($this->admin)
            ->postJson(route('penilaian-wawancara.updateStatus'), [
                'kkm' => 70
            ]);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'status_pendaftaran' => 'lulus_wawancara',
        ]);
    }

    public function test_nilai_boundary_69_dengan_kkm_70_menghasilkan_tidak_lulus()
    {
        $penilaian = PenilaianWawancara::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $this->jadwalSeleksi->id,
            'penilai_id' => $this->admin->id,
            'nilai_komunikasi' => 69,
            'nilai_motivasi' => 69,
            'nilai_kemampuan' => 69,
            'nilai_total' => 207,
            'nilai_rata_rata' => 69,
            'kkm' => null,
        ]);

        $this->actingAs($this->admin)
            ->postJson(route('penilaian-wawancara.updateStatus'), [
                'kkm' => 70
            ]);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'status_pendaftaran' => 'tidak_lulus_wawancara',
        ]);
    }
}