<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Models\Dinas;
use App\Models\Pendaftaran;
use App\Models\User;

class DinasMockTest extends TestCase
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
    public function uji_create_dinas()
    {
        $dinas = new Dinas([
            'nama_dinas' => 'Dinas Kominfo',
            'deskripsi' => 'Bidang komunikasi dan informatika',
            'kontak_person' => '08123456789',
        ]);

        $this->assertEquals('Dinas Kominfo', $dinas->nama_dinas);
        $this->assertEquals('Bidang komunikasi dan informatika', $dinas->deskripsi);
        $this->assertEquals('08123456789', $dinas->kontak_person);
    }

    /** @test */
    public function uji_fillable_fields_dinas()
    {
        $dinas = new Dinas();

        $this->assertEquals(
            ['nama_dinas', 'deskripsi', 'kontak_person'],
            $dinas->getFillable()
        );
    }

    /** @test */
    public function uji_casts_field_datetime()
    {
        $dinas = new Dinas();

        $casts = $dinas->getCasts();

        $this->assertArrayHasKey('created_at', $casts);
        $this->assertArrayHasKey('updated_at', $casts);
        $this->assertEquals('datetime', $casts['created_at']);
        $this->assertEquals('datetime', $casts['updated_at']);
    }

    /** @test */
    public function uji_metode_relasi_dinas()
    {
        $dinas = new Dinas();

        $this->assertTrue(method_exists($dinas, 'pendaftaranPilihan1'));
        $this->assertTrue(method_exists($dinas, 'pendaftaranPilihan2'));
        $this->assertTrue(method_exists($dinas, 'pendaftaranDiterima'));
        $this->assertTrue(method_exists($dinas, 'users'));
    }

    /** @test */
    public function uji_total_pendaftar_pilihan1()
    {
        $mockDinas = Mockery::mock(Dinas::class)->makePartial();

        $mockDinas->shouldReceive('getAttribute')
            ->with('pendaftaranPilihan1')
            ->andReturn(collect([
                new Pendaftaran(),
                new Pendaftaran(),
                new Pendaftaran(),
            ]));

        $this->assertEquals(3, $mockDinas->total_pendaftar_pilihan1);
    }

    /** @test */
    public function uji_total_pendaftar_pilihan2()
    {
        $mockDinas = Mockery::mock(Dinas::class)->makePartial();

        $mockDinas->shouldReceive('getAttribute')
            ->with('pendaftaranPilihan2')
            ->andReturn(collect([
                new Pendaftaran(),
                new Pendaftaran(),
            ]));

        $this->assertEquals(2, $mockDinas->total_pendaftar_pilihan2);
    }

    /** @test */
    public function uji_total_seluruh_pendaftar()
    {
        $mockDinas = Mockery::mock(Dinas::class)->makePartial();

        $mockDinas->shouldReceive('getAttribute')
            ->with('pendaftaranPilihan1')
            ->andReturn(collect([
                new Pendaftaran(),
                new Pendaftaran(),
            ]));

        $mockDinas->shouldReceive('getAttribute')
            ->with('pendaftaranPilihan2')
            ->andReturn(collect([
                new Pendaftaran(),
                new Pendaftaran(),
                new Pendaftaran(),
            ]));

        $this->assertEquals(5, $mockDinas->total_pendaftar);
    }

    /** @test */
    public function uji_total_diterima()
    {
        $mockDinas = Mockery::mock(Dinas::class)->makePartial();

        $mockDinas->shouldReceive('getAttribute')
            ->with('pendaftaranDiterima')
            ->andReturn(collect([
                new Pendaftaran(),
            ]));

        $this->assertEquals(1, $mockDinas->total_diterima);
    }

    /** @test */
    public function uji_relasi_users()
    {
        $mockDinas = Mockery::mock(Dinas::class)->makePartial();

        $mockDinas->shouldReceive('getAttribute')
            ->with('users')
            ->andReturn(collect([
                new User(),
                new User(),
            ]));

        $this->assertCount(2, $mockDinas->users);
    }
}