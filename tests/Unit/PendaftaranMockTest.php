<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\InfoOr;
use App\Models\JadwalSeleksi;
use App\Models\Dinas;
use App\Models\PenilaianWawancara;
use App\Models\EvaluasiMagang;

class PendaftaranMockTest extends TestCase
{
    /**
     * Tutup semua mock setelah test selesai.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function uji_create_pendaftaran()
    {
        $pendaftaran = new Pendaftaran([
            'user_id' => 1,
            'info_or_id' => 2,
            'jadwal_seleksi_id' => 3,
            'pilihan_dinas_1' => 10,
            'pilihan_dinas_2' => 11,
            'motivasi' => 'Ingin mengembangkan kemampuan profesional.',
            'pengalaman' => 'Magang di perusahaan IT.',
            'file_cv' => 'cv.pdf',
            'file_transkrip' => 'transkrip.pdf',
            'status_pendaftaran' => 'diterima',
            'dinas_diterima_id' => 10,
            'tanggal_daftar' => '2025-11-01 10:00:00',
        ]);

        $this->assertEquals('Ingin mengembangkan kemampuan profesional.', $pendaftaran->motivasi);
        $this->assertEquals('diterima', $pendaftaran->status_pendaftaran);
        $this->assertEquals(10, $pendaftaran->dinas_diterima_id);
    }

    /** @test */
    public function uji_fillable_fields_pendaftaran()
    {
        $expected = [
            'user_id',
            'info_or_id',
            'jadwal_seleksi_id',
            'pilihan_dinas_1',
            'pilihan_dinas_2',
            'motivasi',
            'pengalaman',
            'file_cv',
            'file_transkrip',
            'status_pendaftaran',
            'dinas_diterima_id',
            'tanggal_daftar',
        ];

        $this->assertEquals($expected, (new Pendaftaran())->getFillable());
    }

    /** @test */
    public function uji_casts_field_tanggal_daftar()
    {
        $casts = (new Pendaftaran())->getCasts();

        $this->assertArrayHasKey('tanggal_daftar', $casts);
        $this->assertEquals('datetime', $casts['tanggal_daftar']);
    }

    /** @test */
    public function uji_relasi_user()
    {
        $mock = Mockery::mock(Pendaftaran::class)->makePartial();
        $mock->shouldReceive('user')->once()->andReturn(new User());

        $this->assertInstanceOf(User::class, $mock->user());
    }

    /** @test */
    public function uji_relasi_info_or()
    {
        $mock = Mockery::mock(Pendaftaran::class)->makePartial();
        $mock->shouldReceive('infoOr')->once()->andReturn(new InfoOr());

        $this->assertInstanceOf(InfoOr::class, $mock->infoOr());
    }

    /** @test */
    public function uji_relasi_jadwal_seleksi()
    {
        $mock = Mockery::mock(Pendaftaran::class)->makePartial();
        $mock->shouldReceive('jadwalSeleksi')->once()->andReturn(new JadwalSeleksi());

        $this->assertInstanceOf(JadwalSeleksi::class, $mock->jadwalSeleksi());
    }

    /** @test */
    public function uji_relasi_dinas_pilihan1()
    {
        $mock = Mockery::mock(Pendaftaran::class)->makePartial();
        $mock->shouldReceive('dinasPilihan1')->once()->andReturn(new Dinas());

        $this->assertInstanceOf(Dinas::class, $mock->dinasPilihan1());
    }

    /** @test */
    public function uji_relasi_dinas_pilihan2()
    {
        $mock = Mockery::mock(Pendaftaran::class)->makePartial();
        $mock->shouldReceive('dinasPilihan2')->once()->andReturn(new Dinas());

        $this->assertInstanceOf(Dinas::class, $mock->dinasPilihan2());
    }

    /** @test */
    public function uji_relasi_dinas_diterima()
    {
        $mock = Mockery::mock(Pendaftaran::class)->makePartial();
        $mock->shouldReceive('dinasDiterima')->once()->andReturn(new Dinas());

        $this->assertInstanceOf(Dinas::class, $mock->dinasDiterima());
    }

    /** @test */
    public function uji_relasi_penilaian_wawancara()
    {
        $mock = Mockery::mock(Pendaftaran::class)->makePartial();
        $mock->shouldReceive('penilaianWawancara')->once()->andReturn(new PenilaianWawancara());

        $this->assertInstanceOf(PenilaianWawancara::class, $mock->penilaianWawancara());
    }

    /** @test */
    public function uji_relasi_evaluasi_magang()
    {
        $mock = Mockery::mock(Pendaftaran::class)->makePartial();
        $mock->shouldReceive('evaluasiMagang')->once()->andReturn(new EvaluasiMagang());

        $this->assertInstanceOf(EvaluasiMagang::class, $mock->evaluasiMagang());
    }

    /** @test */
    public function uji_relasi_jadwals_many_to_many()
    {
        $mock = Mockery::mock(Pendaftaran::class)->makePartial();
        $mock->shouldReceive('jadwals')->once()->andReturn(collect([new JadwalSeleksi()]));

        $this->assertInstanceOf(JadwalSeleksi::class, $mock->jadwals()->first());
    }
}