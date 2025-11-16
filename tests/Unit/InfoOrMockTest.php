<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Models\InfoOr;
use App\Models\JadwalKegiatan;
use App\Models\JadwalSeleksi;
use App\Models\Pendaftaran;
use App\Models\TemplateSertifikat;

class InfoOrMockTest extends TestCase
{
    /** @test */
    public function uji_create_info_or()
    {
        $infoOr = new InfoOr([
            'judul' => 'Open Recruitment UKM',
            'deskripsi' => 'Pendaftaran anggota baru UKM Teknologi',
            'persyaratan_umum' => 'Mahasiswa aktif minimal semester 1',
            'tanggal_buka' => '2025-11-01',
            'tanggal_tutup' => '2025-11-10',
            'periode' => 'Ganjil 2025',
            'gambar' => 'poster.png',
            'status' => 'dibuka',
        ]);

        $this->assertEquals('Open Recruitment UKM', $infoOr->judul);
        $this->assertEquals('dibuka', $infoOr->status);
        $this->assertEquals('Ganjil 2025', $infoOr->periode);
        $this->assertEquals('poster.png', $infoOr->gambar);
    }

    /** @test */
    public function uji_fillable_fields_info_or()
    {
        $expected = [
            'judul',
            'deskripsi',
            'persyaratan_umum',
            'tanggal_buka',
            'tanggal_tutup',
            'periode',
            'gambar',
            'status',
        ];

        $this->assertEquals($expected, (new InfoOr())->getFillable());
    }

    /** @test */
    public function uji_casts_field_tanggal()
    {
        $casts = (new InfoOr())->getCasts();

        $this->assertArrayHasKey('tanggal_buka', $casts);
        $this->assertEquals('date', $casts['tanggal_buka']);

        $this->assertArrayHasKey('tanggal_tutup', $casts);
        $this->assertEquals('date', $casts['tanggal_tutup']);
    }

    /** @test */
    public function uji_relasi_jadwal_kegiatan()
    {
        $infoOr = Mockery::mock(InfoOr::class)->makePartial();
        $infoOr->shouldReceive('jadwalKegiatan')->once()->andReturn(collect([new JadwalKegiatan()]));

        $this->assertInstanceOf(JadwalKegiatan::class, $infoOr->jadwalKegiatan()->first());
    }

    /** @test */
    public function uji_relasi_jadwal_seleksi()
    {
        $infoOr = Mockery::mock(InfoOr::class)->makePartial();
        $infoOr->shouldReceive('jadwalSeleksi')->once()->andReturn(collect([new JadwalSeleksi()]));

        $this->assertInstanceOf(JadwalSeleksi::class, $infoOr->jadwalSeleksi()->first());
    }

    /** @test */
    public function uji_relasi_pendaftaran()
    {
        $infoOr = Mockery::mock(InfoOr::class)->makePartial();
        $infoOr->shouldReceive('pendaftaran')->once()->andReturn(collect([new Pendaftaran()]));

        $this->assertInstanceOf(Pendaftaran::class, $infoOr->pendaftaran()->first());
    }

    /** @test */
    public function uji_relasi_template_sertifikat()
    {
        $infoOr = Mockery::mock(InfoOr::class)->makePartial();
        $infoOr->shouldReceive('templateSertifikat')->once()->andReturn(collect([new TemplateSertifikat()]));

        $this->assertInstanceOf(TemplateSertifikat::class, $infoOr->templateSertifikat()->first());
    }
}