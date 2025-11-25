<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\InfoOr;
use App\Models\JadwalSeleksi;
use App\Models\PenilaianWawancara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini
class PenilaianWawancaraControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_menampilkan_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pendaftaran = Pendaftaran::factory()->create();
        $jadwal = JadwalSeleksi::create([
            'info_or_id' => InfoOr::factory()->create()->id,
            'tanggal_seleksi' => '2025-01-01',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '10:00',
            'tempat' => 'Ruang A',
            'pewawancara' => 'Pak Budi',
            'pendaftaran_id' => $pendaftaran->id,
        ]);

        PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $user->id,
            'jadwal_seleksi_id' => $jadwal->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 70,
            'nilai_kemampuan' => 90,
            'nilai_total' => 240,
            'nilai_rata_rata' => 80,
            'kkm' => 75,
            'status' => 'sudah_dinilai',
        ]);

        $response = $this->get(route('penilaian-wawancara.index'));
        $response->assertStatus(200);
        $response->assertViewHas('data');
        $response->assertViewHas('kkm', 75);
    }

    public function test_create_menampilkan_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('penilaian-wawancara.create'));
        $response->assertStatus(200);
        $response->assertViewHasAll(['jadwalseleksi', 'penilaianExist']);
    }

    public function test_store_menyimpan_penilaian()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pendaftaran = Pendaftaran::factory()->create();
        $jadwal = JadwalSeleksi::create([
            'info_or_id' => InfoOr::factory()->create()->id,
            'tanggal_seleksi' => '2025-01-01',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '10:00',
            'tempat' => 'Ruang A',
            'pewawancara' => 'Pak Budi',
            'pendaftaran_id' => $pendaftaran->id,
        ]);

        $data = [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_seleksi_id' => $jadwal->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 70,
            'nilai_kemampuan' => 90,
            'kkm' => 75,
        ];

        $response = $this->post(route('penilaian-wawancara.store'), $data);

        $response->assertRedirect(route('penilaian-wawancara.index'));
        $this->assertDatabaseHas('penilaian_wawancara', [
            'pendaftaran_id' => $pendaftaran->id,
            'nilai_rata_rata' => 80,
        ]);
    }

    public function test_edit_menampilkan_form_edit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pendaftaran = Pendaftaran::factory()->create();
        $jadwal = JadwalSeleksi::create([
            'info_or_id' => InfoOr::factory()->create()->id,
            'tanggal_seleksi' => '2025-01-01',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '10:00',
            'tempat' => 'Ruang A',
            'pewawancara' => 'Pak Budi',
            'pendaftaran_id' => $pendaftaran->id,
        ]);

        $penilaian = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $user->id,
            'jadwal_seleksi_id' => $jadwal->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 70,
            'nilai_kemampuan' => 90,
            'nilai_total' => 240,
            'nilai_rata_rata' => 80,
            'kkm' => 75,
            'status' => 'sudah_dinilai',
        ]);

        $response = $this->get(route('penilaian-wawancara.edit', $penilaian));
        $response->assertStatus(200);
        $response->assertViewHasAll(['penilaianWawancara', 'peserta', 'jadwalseleksi']);
    }

    public function test_update_mengubah_penilaian()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pendaftaran = Pendaftaran::factory()->create();
        $jadwal = JadwalSeleksi::create([
            'info_or_id' => InfoOr::factory()->create()->id,
            'tanggal_seleksi' => '2025-01-01',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '10:00',
            'tempat' => 'Ruang A',
            'pewawancara' => 'Pak Budi',
            'pendaftaran_id' => $pendaftaran->id,
        ]);

        $penilaian = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $user->id,
            'jadwal_seleksi_id' => $jadwal->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 70,
            'nilai_kemampuan' => 90,
            'nilai_total' => 240,
            'nilai_rata_rata' => 80,
            'kkm' => 75,
            'status' => 'sudah_dinilai',
        ]);

        $data = [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_seleksi_id' => $jadwal->id,
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 95,
            'kkm' => 80,
        ];

        $response = $this->put(route('penilaian-wawancara.update', $penilaian), $data);

        $response->assertRedirect(route('penilaian-wawancara.index'));
        $this->assertDatabaseHas('penilaian_wawancara', [
            'pendaftaran_id' => $pendaftaran->id,
            'nilai_rata_rata' => 85,
            'kkm' => 80,
        ]);
    }

    public function test_destroy_menghapus_penilaian()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pendaftaran = Pendaftaran::factory()->create();
        $jadwal = JadwalSeleksi::create([
            'info_or_id' => InfoOr::factory()->create()->id,
            'tanggal_seleksi' => '2025-01-01',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '10:00',
            'tempat' => 'Ruang A',
            'pewawancara' => 'Pak Budi',
            'pendaftaran_id' => $pendaftaran->id,
        ]);

        $penilaian = PenilaianWawancara::create([
            'pendaftaran_id' => $pendaftaran->id,
            'penilai_id' => $user->id,
            'jadwal_seleksi_id' => $jadwal->id,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 70,
            'nilai_kemampuan' => 90,
            'nilai_total' => 240,
            'nilai_rata_rata' => 80,
            'kkm' => 75,
            'status' => 'sudah_dinilai',
        ]);

        $response = $this->delete(route('penilaian-wawancara.destroy', $penilaian));
        $response->assertRedirect(route('penilaian-wawancara.index'));

        $this->assertDatabaseMissing('penilaian_wawancara', [
            'id' => $penilaian->id,
        ]);
    }

    public function test_updateStatus_berhasil()
{
    $user = User::factory()->create();
    $this->actingAs($user); // penting biar tidak 401

    $pendaftaran = Pendaftaran::factory()->create();
    $jadwal = JadwalSeleksi::create([
        'info_or_id' => InfoOr::factory()->create()->id,
        'tanggal_seleksi' => '2025-01-01',
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '10:00',
        'tempat' => 'Ruang A',
        'pewawancara' => 'Pak Budi',
        'pendaftaran_id' => $pendaftaran->id,
    ]);

    PenilaianWawancara::create([
        'pendaftaran_id' => $pendaftaran->id,
        'penilai_id' => $user->id, // samakan dengan user login
        'jadwal_seleksi_id' => $jadwal->id,
        'nilai_komunikasi' => 80,
        'nilai_motivasi' => 70,
        'nilai_kemampuan' => 90,
        'nilai_total' => 240,
        'nilai_rata_rata' => 80,
        'kkm' => 75,
        'status' => 'sudah_dinilai',
    ]);

    // KKM diset lebih rendah dari rata-rata peserta supaya lulus
    $response = $this->postJson(route('penilaian-wawancara.updateStatus'), ['kkm' => 75]);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Status pendaftaran dan KKM berhasil diperbarui!']);

    $this->assertDatabaseHas('penilaian_wawancara', [
        'pendaftaran_id' => $pendaftaran->id,
        'kkm' => 75,
    ]);

    $this->assertDatabaseHas('pendaftaran', [
        'id' => $pendaftaran->id,
        'status_pendaftaran' => 'lulus_wawancara', // sekarang sesuai logika
    ]);
}

}