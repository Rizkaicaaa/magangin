<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\InfoOr;
use App\Models\JadwalSeleksi;
use App\Models\Dinas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini


class SeleksiWawancaraTest extends TestCase
{
    use RefreshDatabase;

    private function createBaseData()
    {
        $today = Carbon::today();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $infoOr = InfoOr::factory()->create();

        $dinas1 = Dinas::create([
            'nama_dinas' => 'Dinas Pendidikan',
            'deskripsi' => 'Deskripsi A',
            'kontak_person' => 'Budi',
            'kuota_magang' => 10,
            'status' => 'buka',
        ]);

        $dinas2 = Dinas::create([
            'nama_dinas' => 'Dinas Kesehatan',
            'deskripsi' => 'Deskripsi B',
            'kontak_person' => 'Ani',
            'kuota_magang' => 10,
            'status' => 'buka',
        ]);

        $pendaftaranUser = Pendaftaran::create([
            'user_id' => $user->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas1->id,
            'pilihan_dinas_2' => $dinas2->id,
            'motivasi' => 'motivasi',
            'pengalaman' => 'pengalaman',
            'file_cv' => 'cv.pdf',
            'file_transkrip' => 'transkrip.pdf',
            'status_pendaftaran' => 'terdaftar',
            'tanggal_daftar' => now(),
        ]);

        $pendaftaranOther = Pendaftaran::create([
            'user_id' => $otherUser->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas1->id,
            'pilihan_dinas_2' => $dinas2->id,
            'motivasi' => 'motivasi',
            'pengalaman' => 'pengalaman',
            'file_cv' => 'cv.pdf',
            'file_transkrip' => 'transkrip.pdf',
            'status_pendaftaran' => 'terdaftar',
            'tanggal_daftar' => now(),
        ]);

        return compact('user', 'otherUser', 'infoOr', 'pendaftaranUser', 'pendaftaranOther', 'today');
    }

    #[Test]
    public function user_melihat_jadwal_miliknya_saja()
    {
        extract($this->createBaseData());

        $jadwalUser = JadwalSeleksi::create([
            'pendaftaran_id' => $pendaftaranUser->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => $today->copy()->addDay(),
            'waktu_mulai' => '10:00:00',
            'waktu_selesai' => '11:00:00',
            'tempat' => 'Ruang 1',
            'pewawancara' => 'Budi Tester',
        ]);

        $response = $this->actingAs($user)->get('/seleksi-wawancara');

        $response->assertStatus(200)
                 ->assertViewHas('jadwals', fn($jadwals) =>
                     $jadwals->contains($jadwalUser)
                 );
    }

    #[Test]
    public function jadwal_user_lain_tidak_tampil()
    {
        extract($this->createBaseData());

        $jadwalOther = JadwalSeleksi::create([
            'pendaftaran_id' => $pendaftaranOther->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => $today->copy()->addDay(),
            'waktu_mulai' => '12:00:00',
            'waktu_selesai' => '13:00:00',
            'tempat' => 'Ruang 2',
            'pewawancara' => 'Ani Tester',
        ]);

        $response = $this->actingAs($user)->get('/seleksi-wawancara');

        $response->assertViewHas('jadwals', fn($jadwals) =>
            !$jadwals->contains($jadwalOther)
        );
    }

    #[Test]
    public function jadwal_lama_tidak_tampil()
    {
        extract($this->createBaseData());

        $jadwalLama = JadwalSeleksi::create([
            'pendaftaran_id' => $pendaftaranUser->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => $today->copy()->subDay(),
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '10:00:00',
            'tempat' => 'Ruang 3',
            'pewawancara' => 'Cici Tester',
        ]);

        $response = $this->actingAs($user)->get('/seleksi-wawancara');

        $response->assertViewHas('jadwals', fn($jadwals) =>
            !$jadwals->contains($jadwalLama)
        );
    }

    #[Test]
    public function ketika_belum_ada_jadwal_user_mendapatkan_kosong()
    {
        extract($this->createBaseData());

        $response = $this->actingAs($user)->get('/seleksi-wawancara');

        $response->assertStatus(200)
                 ->assertViewHas('jadwals', fn($jadwals) =>
                     $jadwals->count() === 0
                 );
    }

    #[Test]
    public function jadwal_urut_dari_tanggal_terdekat()
    {
        extract($this->createBaseData());

        $jadwal1 = JadwalSeleksi::create([
            'pendaftaran_id' => $pendaftaranUser->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => $today->copy()->addDays(5),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'A',
            'pewawancara' => 'P1',
        ]);

        $jadwal2 = JadwalSeleksi::create([
            'pendaftaran_id' => $pendaftaranUser->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => $today->copy()->addDays(1),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'B',
            'pewawancara' => 'P2',
        ]);

        $response = $this->actingAs($user)->get('/seleksi-wawancara');

        $response->assertViewHas('jadwals', function ($jadwals) use ($jadwal1, $jadwal2) {
            return $jadwals->first()->id === $jadwal2->id
                && $jadwals->last()->id === $jadwal1->id;
        });
    }

    #[Test]
    public function user_tidak_melihat_jadwal_jika_belum_daftar()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/seleksi-wawancara');

        $response->assertStatus(200)
                 ->assertViewHas('jadwals', fn($jadwals) =>
                     $jadwals->count() === 0
                 );
    }

    #[Test]
    public function jadwal_hari_ini_tetap_tampil()
    {
        extract($this->createBaseData());

        $jadwalToday = JadwalSeleksi::create([
            'pendaftaran_id' => $pendaftaranUser->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => $today,
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '09:00',
            'tempat' => 'Ruang Today',
            'pewawancara' => 'Today Tester',
        ]);

        $response = $this->actingAs($user)->get('/seleksi-wawancara');

        $response->assertViewHas('jadwals', fn($jadwals) =>
            $jadwals->contains($jadwalToday)
        );
    }

    #[Test]
    public function user_harus_login_untuk_mengakses_halaman_jadwal()
    {
        $response = $this->get('/seleksi-wawancara');

        $response->assertRedirect('/login');
    }

    #[Test]
    public function setiap_jadwal_memiliki_field_yang_lengkap()
    {
        extract($this->createBaseData());

        $jadwal = JadwalSeleksi::create([
            'pendaftaran_id' => $pendaftaranUser->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => $today->copy()->addDay(),
            'waktu_mulai' => '07:00',
            'waktu_selesai' => '08:00',
            'tempat' => 'Ruang 99',
            'pewawancara' => 'Tester Lengkap',
        ]);

        $response = $this->actingAs($user)->get('/seleksi-wawancara');

        $response->assertViewHas('jadwals', function ($jadwals) {
            if ($jadwals->isEmpty()) return false;

            $j = $jadwals->first()->toArray();

            return array_key_exists('tanggal_seleksi', $j)
                && array_key_exists('waktu_mulai', $j)
                && array_key_exists('waktu_selesai', $j)
                && array_key_exists('tempat', $j)
                && array_key_exists('pewawancara', $j);
        });
    }

    #[Test]
    public function user_tidak_bisa_melihat_jadwal_dengan_pendaftaran_id_yang_bukan_miliknya()
    {
        extract($this->createBaseData());

        $jadwalOther = JadwalSeleksi::create([
            'pendaftaran_id' => $pendaftaranOther->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => $today->copy()->addDay(),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Hack',
            'pewawancara' => 'X',
        ]);

        $response = $this->actingAs($user)->get('/seleksi-wawancara?force='.$jadwalOther->id);

        $response->assertStatus(200)
                 ->assertViewHas('jadwals', fn($jadwals) =>
                    !$jadwals->contains($jadwalOther)
                 );
    }

    #[Test]
    public function halaman_seleksi_menggunakan_view_yang_benar()
    {
        extract($this->createBaseData());

        $response = $this->actingAs($user)->get('/seleksi-wawancara');

        $response->assertViewIs('seleksi-wawancara.index');
    }
}
