<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Dinas;
use App\Models\Pendaftaran;
use App\Models\PenilaianWawancara;
use App\Models\EvaluasiMagang;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user_instance()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'nama_lengkap' => 'Rizka Kurnia Illahi',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
        ]);
        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function it_has_expected_fillable_attributes()
    {
        $user = new User();

        $this->assertEquals([
            'email',
            'password',
            'role',
            'nama_lengkap',
            'nim',
            'no_telp',
            'tanggal_daftar',
            'status',
            'dinas_id',
        ], $user->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_dinas()
    {
        $dinas = Dinas::factory()->create();
        $user = User::factory()->create(['dinas_id' => $dinas->id]);

        $this->assertInstanceOf(Dinas::class, $user->dinas);
        $this->assertEquals($dinas->id, $user->dinas->id);
    }

    /** @test */
    public function it_has_many_pendaftaran()
    {
        $user = User::factory()->create();
        $pendaftaran = Pendaftaran::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->pendaftaran->contains($pendaftaran));
    }

    /** @test */
    public function it_has_many_penilaian_wawancara_as_penilai()
    {
        $user = User::factory()->create();
        $penilaian = PenilaianWawancara::factory()->create(['penilai_id' => $user->id]);

        $this->assertTrue($user->penilaianWawancaraAsPenilai->contains($penilaian));
    }

    /** @test */
    public function it_has_many_evaluasi_magang_as_penilai()
    {
        $user = User::factory()->create();
        $evaluasi = EvaluasiMagang::factory()->create(['penilai_id' => $user->id]);

        $this->assertTrue($user->evaluasiMagangAsPenilai->contains($evaluasi));
    }
}