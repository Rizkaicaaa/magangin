<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Models\User;
use App\Models\Dinas;
use App\Models\Pendaftaran;
use App\Models\PenilaianWawancara;
use App\Models\EvaluasiMagang;

class UserMockTest extends TestCase
{
    /** @test */
    public function uji_create_user()
    {
        $user = new User([
            'email' => 'test@example.com',
            'password' => 'hashedpassword',
            'role' => 'mahasiswa',
            'nama_lengkap' => 'Habib Ahmad',
            'nim' => '2111522011',
            'no_telp' => '08123456789',
            'tanggal_daftar' => '2025-10-01',
            'status' => 'aktif',
            'dinas_id' => 1,
        ]);

        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('mahasiswa', $user->role);
        $this->assertEquals('Habib Ahmad', $user->nama_lengkap);
        $this->assertEquals('aktif', $user->status);
    }

    /** @test */
    public function uji_fillable_fields_user()
    {
        $expected = [
            'email',
            'password',
            'role',
            'nama_lengkap',
            'nim',
            'no_telp',
            'tanggal_daftar',
            'status',
            'dinas_id',
        ];

        $this->assertEquals($expected, (new User())->getFillable());
    }

    /** @test */
    public function uji_casts_field_datetime_dan_password()
    {
        $casts = (new User())->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);

        $this->assertArrayHasKey('tanggal_daftar', $casts);
        $this->assertEquals('date', $casts['tanggal_daftar']);

        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('hashed', $casts['password']);
    }

    /** @test */
    public function uji_relasi_dinas()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('dinas')->once()->andReturn(Mockery::mock(Dinas::class));

        $this->assertInstanceOf(Dinas::class, $user->dinas());
    }

    /** @test */
    public function uji_relasi_pendaftaran()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('pendaftaran')->once()->andReturn(collect([new Pendaftaran()]));

        $this->assertInstanceOf(Pendaftaran::class, $user->pendaftaran()->first());
    }

    /** @test */
    public function uji_relasi_penilaian_wawancara()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('penilaianWawancaraAsPenilai')->once()->andReturn(collect([new PenilaianWawancara()]));

        $this->assertInstanceOf(PenilaianWawancara::class, $user->penilaianWawancaraAsPenilai()->first());
    }

    /** @test */
    public function uji_relasi_evaluasi_magang()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('evaluasiMagangAsPenilai')->once()->andReturn(collect([new EvaluasiMagang()]));

        $this->assertInstanceOf(EvaluasiMagang::class, $user->evaluasiMagangAsPenilai()->first());
    }
}