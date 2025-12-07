<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use App\Models\JadwalSeleksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class JadwalSeleksiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $mahasiswa;
    protected $dinas;
    protected $infoOr;
    protected $pendaftaran;
    protected static $counter = 0;

    protected function setUp(): void
    {
        parent::setUp();
        
        self::$counter++;

        $this->dinas = Dinas::create([
            'nama_dinas' => 'Dinas Test ' . self::$counter . '-' . uniqid() . '-' . time(),
            'deskripsi' => 'Deskripsi Test',
            'kontak_person' => '08123456789',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin' . self::$counter . '@test.com',
            'nama_lengkap' => 'Admin Test ' . self::$counter,
            'dinas_id' => $this->dinas->id,
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa' . self::$counter . '@test.com',
            'nama_lengkap' => 'Mahasiswa Test ' . self::$counter,
            'dinas_id' => null,
        ]);

        $this->infoOr = InfoOr::factory()->create([
            'periode' => '2024/2025-' . self::$counter,
            'status' => 'buka',
        ]);

        $this->pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'motivasi' => 'Motivasi test untuk orientasi riset',
            'file_cv' => 'cv_test.pdf',
            'file_transkrip' => 'transkrip_test.pdf',
            'status_pendaftaran' => 'terdaftar',
            'jadwal_seleksi_id' => null,
        ]);
    }

    protected function tearDown(): void
    {
        JadwalSeleksi::query()->delete();
        Pendaftaran::query()->delete();
        User::query()->delete();
        InfoOr::query()->delete();
        Dinas::query()->delete();
        
        parent::tearDown();
    }

    public function test_admin_dapat_mengakses_halaman_index_jadwal_seleksi()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('jadwal-seleksi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('jadwal-seleksi.index');
        $response->assertViewHas('jadwals');
    }

    public function test_index_dapat_filter_berdasarkan_tanggal()
    {
        $jadwal = $this->createJadwalManual([
            'tanggal_seleksi' => '2025-12-15',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('jadwal-seleksi.index', ['tanggal' => '2025-12-15']));

        $response->assertStatus(200);
        $response->assertSee('2025-12-15');
    }

    public function test_index_dapat_search_berdasarkan_pewawancara()
    {
        $jadwal = $this->createJadwalManual([
            'pewawancara' => 'Dr. Budi Santoso',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('jadwal-seleksi.index', ['search' => 'Budi']));

        $response->assertStatus(200);
        $response->assertSee('Dr. Budi Santoso');
    }

    public function test_index_dapat_search_berdasarkan_tempat()
    {
        $jadwal = $this->createJadwalManual([
            'tempat' => 'Ruang Rapat Lt. 3',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('jadwal-seleksi.index', ['search' => 'Rapat']));

        $response->assertStatus(200);
        $response->assertSee('Ruang Rapat Lt. 3');
    }

    public function test_admin_dapat_mengakses_halaman_create_jadwal_seleksi()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('jadwal-seleksi.create'));

        $response->assertStatus(200);
        $response->assertViewIs('jadwal-seleksi.create');
        $response->assertViewHas(['infos', 'pendaftarans']);
    }

    public function test_admin_dapat_membuat_jadwal_seleksi_dengan_data_valid()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertStatus(302);
        $response->assertRedirect(route('jadwal-seleksi.index'));
        $response->assertSessionHas('success', 'Jadwal wawancara berhasil ditambahkan.');

        $this->assertDatabaseCount('jadwal_seleksi', 1);
        $jadwal = JadwalSeleksi::first();
        $this->assertEquals($this->infoOr->id, $jadwal->info_or_id);
        $this->assertEquals($this->pendaftaran->id, $jadwal->pendaftaran_id);

        $this->assertEquals('2025-12-20', \Carbon\Carbon::parse($jadwal->tanggal_seleksi)->format('Y-m-d'));
        $this->assertEquals('Dr. Ahmad', $jadwal->pewawancara);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $jadwal->id,
        ]);
    }

    public function test_validasi_info_or_id_wajib_diisi()
    {
        $dataJadwal = [
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['info_or_id']);
    }

    public function test_validasi_info_or_id_harus_exist()
    {
        $dataJadwal = [
            'info_or_id' => 99999,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['info_or_id']);
    }

    public function test_validasi_tanggal_seleksi_wajib_diisi()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['tanggal_seleksi']);
    }

    public function test_validasi_tanggal_seleksi_harus_format_date()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => 'bukan-tanggal',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['tanggal_seleksi']);
    }

    public function test_validasi_waktu_mulai_wajib_diisi()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['waktu_mulai']);
    }

    public function test_validasi_waktu_mulai_harus_format_H_i()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '9:00 AM',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['waktu_mulai']);
    }

    public function test_validasi_waktu_selesai_harus_setelah_waktu_mulai()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '11:00',
            'waktu_selesai' => '09:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['waktu_selesai']);
    }

    public function test_validasi_tempat_wajib_diisi()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['tempat']);
    }

    public function test_validasi_tempat_max_255_karakter()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => str_repeat('a', 256),
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['tempat']);
    }

    public function test_validasi_pewawancara_wajib_diisi()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['pewawancara']);
    }

    public function test_validasi_pewawancara_max_255_karakter()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => str_repeat('a', 256),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['pewawancara']);
    }

    public function test_validasi_pendaftaran_id_wajib_diisi()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['pendaftaran_id']);
    }

    public function test_validasi_pendaftaran_id_harus_exist()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => 99999,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors(['pendaftaran_id']);
    }

    public function test_admin_dapat_mengakses_halaman_edit()
    {
        $jadwal = $this->createJadwalManual();

        $response = $this->actingAs($this->admin)
            ->get(route('jadwal-seleksi.edit', $jadwal->id));

        $response->assertStatus(200);
        $response->assertViewIs('jadwal-seleksi.edit');
        $response->assertViewHas(['jadwalSeleksi', 'infos', 'pendaftarans']);
    }

    public function test_halaman_edit_menampilkan_pendaftaran_yang_belum_dijadwalkan()
    {
        $pendaftaranBaru = Pendaftaran::create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'motivasi' => 'Motivasi test untuk orientasi riset',
            'file_cv' => 'cv_test_2.pdf',
            'file_transkrip' => 'transkrip_test_2.pdf',
            'status_pendaftaran' => 'terdaftar',
            'jadwal_seleksi_id' => null,
        ]);

        $jadwal = $this->createJadwalManual();

        $response = $this->actingAs($this->admin)
            ->get(route('jadwal-seleksi.edit', $jadwal->id));

        $response->assertStatus(200);
        $response->assertViewHas('pendaftarans', function ($pendaftarans) use ($pendaftaranBaru) {
            return $pendaftarans->contains('id', $pendaftaranBaru->id);
        });
    }

    public function test_admin_dapat_update_jadwal_seleksi()
    {
        $jadwal = $this->createJadwalManual([
            'pewawancara' => 'Dr. Lama',
        ]);

        $dataUpdate = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-25',
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
            'tempat' => 'Ruang Baru',
            'pewawancara' => 'Dr. Baru',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('jadwal-seleksi.update', $jadwal->id), $dataUpdate);

        $response->assertStatus(302);
        $response->assertRedirect(route('jadwal-seleksi.index'));
        $response->assertSessionHas('success', 'Jadwal seleksi berhasil diperbarui.');

        $this->assertDatabaseHas('jadwal_seleksi', [
            'id' => $jadwal->id,
            'pewawancara' => 'Dr. Baru',
            'tempat' => 'Ruang Baru',
        ]);
    }

    public function test_admin_dapat_menghapus_jadwal_seleksi()
    {
        $jadwal = $this->createJadwalManual();

        $this->pendaftaran->update(['jadwal_seleksi_id' => $jadwal->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('jadwal-seleksi.destroy', $jadwal->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('jadwal-seleksi.index'));
        $response->assertSessionHas('success', 'Jadwal seleksi berhasil dihapus.');

        $this->assertDatabaseMissing('jadwal_seleksi', [
            'id' => $jadwal->id,
        ]);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => null,
        ]);
    }

    public function test_admin_dapat_melihat_detail_jadwal_seleksi()
    {
        $jadwal = $this->createJadwalManual();

        $response = $this->actingAs($this->admin)
            ->get(route('jadwal-seleksi.show', $jadwal->id));

        $response->assertStatus(200);
        $response->assertViewIs('jadwal-seleksi.show');
        $response->assertViewHas('jadwal');
    }

    public function test_pendaftaran_terupdate_ketika_jadwal_dibuat()
    {
        $dataJadwal = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Ahmad',
        ];

        $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $jadwal = JadwalSeleksi::latest()->first();

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $this->pendaftaran->id,
            'jadwal_seleksi_id' => $jadwal->id,
        ]);
    }

    public function test_pendaftaran_terupdate_ketika_jadwal_diupdate()
    {
        $pendaftaranBaru = Pendaftaran::create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $this->infoOr->id,
            'pilihan_dinas_1' => $this->dinas->id,
            'motivasi' => 'Motivasi test untuk orientasi riset',
            'file_cv' => 'cv_test_2.pdf',
            'file_transkrip' => 'transkrip_test_2.pdf',
            'status_pendaftaran' => 'terdaftar',
            'jadwal_seleksi_id' => null,
        ]);

        $jadwal = $this->createJadwalManual();

        $dataUpdate = [
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $pendaftaranBaru->id,
            'tanggal_seleksi' => '2025-12-25',
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
            'tempat' => 'Ruang Baru',
            'pewawancara' => 'Dr. Baru',
        ];

        $this->actingAs($this->admin)
            ->put(route('jadwal-seleksi.update', $jadwal->id), $dataUpdate);

        $this->assertDatabaseHas('pendaftaran', [
            'id' => $pendaftaranBaru->id,
            'jadwal_seleksi_id' => $jadwal->id,
        ]);
    }

    public function test_validasi_semua_field_wajib_saat_store()
    {
        $dataJadwal = [];

        $response = $this->actingAs($this->admin)
            ->post(route('jadwal-seleksi.store'), $dataJadwal);

        $response->assertSessionHasErrors([
            'info_or_id',
            'tanggal_seleksi',
            'waktu_mulai',
            'waktu_selesai',
            'tempat',
            'pewawancara',
            'pendaftaran_id',
        ]);
    }

    public function test_validasi_semua_field_wajib_saat_update()
    {
        $jadwal = $this->createJadwalManual();

        $dataUpdate = [];

        $response = $this->actingAs($this->admin)
            ->put(route('jadwal-seleksi.update', $jadwal->id), $dataUpdate);

        $response->assertSessionHasErrors([
            'info_or_id',
            'tanggal_seleksi',
            'waktu_mulai',
            'waktu_selesai',
            'tempat',
            'pewawancara',
            'pendaftaran_id',
        ]);
    }

    protected function createJadwalManual($attributes = [])
    {
        return JadwalSeleksi::create(array_merge([
            'info_or_id' => $this->infoOr->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'tanggal_seleksi' => '2025-12-15',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang Rapat',
            'pewawancara' => 'Dr. Test',
        ], $attributes));
    }
}