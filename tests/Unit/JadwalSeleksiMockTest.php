<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\JadwalSeleksi;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class JadwalSeleksiMockTest extends TestCase
{
    use RefreshDatabase;

   
  /** @test */
public function uji_create_jadwal_seleksi()
{
    $mockJadwal = Mockery::mock(JadwalSeleksi::class)->makePartial();

    $mockJadwal->fill([
        'info_or_id' => 1,
        'pendaftaran_id' => 1,
        'tanggal_seleksi' => '2025-10-31',
        'waktu_mulai' => '2025-10-31 08:00:00',
        'waktu_selesai' => '2025-10-31 10:00:00',
        'tempat' => 'Aula Kampus',
        'pewawancara' => 'Bapak Ahmad',
    ]);

    $this->assertEquals('Aula Kampus', $mockJadwal->tempat);
    $this->assertEquals('Bapak Ahmad', $mockJadwal->pewawancara);
}

    /** @test */
    public function uji_fillable_fields_jadwal_seleksi()
    {
        $jadwal = new JadwalSeleksi();

        $this->assertEquals([
            'info_or_id',
            'pendaftaran_id',
            'tanggal_seleksi',
            'waktu_mulai',
            'waktu_selesai',
            'tempat',
            'pewawancara',
        ], $jadwal->getFillable());
    }

/** @test */
public function uji_casts_field_jadwal_seleksi()
{
    $jadwal = new JadwalSeleksi();

    $expectedCasts = [
        'id' => 'int',
        'tanggal_seleksi' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    $this->assertEquals($expectedCasts, $jadwal->getCasts());
}


    /** @test */
    public function uji_relasi_ke_info_or()
    {
        $jadwal = new JadwalSeleksi();
        $relasi = $jadwal->infoOr();

        $this->assertEquals('info_or_id', $relasi->getForeignKeyName());
        $this->assertEquals(InfoOr::class, $relasi->getRelated()::class);
    }

    /** @test */
    public function uji_relasi_ke_pendaftaran()
    {
        $jadwal = new JadwalSeleksi();
        $relasi = $jadwal->pendaftaran();

        $this->assertEquals('pendaftaran_id', $relasi->getForeignKeyName());
        $this->assertEquals(Pendaftaran::class, $relasi->getRelated()::class);
    }

    /** @test */
    public function uji_relasi_ke_mahasiswas()
    {
        $jadwal = new JadwalSeleksi();
        $relasi = $jadwal->mahasiswas();

        $this->assertEquals('jadwal_mahasiswa', $relasi->getTable());
        $this->assertEquals(User::class, $relasi->getRelated()::class);
    }

    /** @test */
    public function uji_relasi_ke_pendaftarans_many_to_many()
    {
        $jadwal = new JadwalSeleksi();
        $relasi = $jadwal->pendaftarans();

        $this->assertEquals('jadwal_pendaftaran', $relasi->getTable());
        $this->assertEquals(Pendaftaran::class, $relasi->getRelated()::class);
    }

    /** @test */
    public function uji_mock_relasi_mahasiswas()
    {
        $mockJadwal = Mockery::mock(JadwalSeleksi::class)->makePartial();

        $mockJadwal->shouldReceive('getAttribute')
            ->with('mahasiswas')
            ->andReturn(collect([
                new User(['name' => 'Andi']),
                new User(['name' => 'Budi']),
            ]));

        $this->assertCount(2, $mockJadwal->mahasiswas);
    }

    /** @test */
    public function uji_mock_relasi_pendaftarans()
    {
        $mockJadwal = Mockery::mock(JadwalSeleksi::class)->makePartial();

        $mockJadwal->shouldReceive('getAttribute')
            ->with('pendaftarans')
            ->andReturn(collect([
                new Pendaftaran(),
                new Pendaftaran(),
            ]));

        $this->assertCount(2, $mockJadwal->pendaftarans);
    }
}