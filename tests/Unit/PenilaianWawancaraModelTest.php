<?php

namespace Tests\Unit\Models;

use App\Models\PenilaianWawancara;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class PenilaianWawancaraModelTest extends TestCase
{
    /** @test */
    public function model_memakai_nama_tabel_yang_sesuai()
    {
        $model = new PenilaianWawancara;

        $this->assertEquals('penilaian_wawancara', $model->getTable());
    }

    /** @test */
    public function model_memiliki_fillable_yang_sesuai()
    {
        $model = new PenilaianWawancara;

        $this->assertEquals([
            'pendaftaran_id',
            'penilai_id',
            'jadwal_seleksi_id',
            'nilai_komunikasi',
            'nilai_motivasi',
            'nilai_kemampuan',
            'nilai_total',
            'nilai_rata_rata',
            'status',
            'kkm',
        ], $model->getFillable());
    }

    /** @test */
/** @test */
public function model_memiliki_casts_yang_sesuai()
{
    $model = new PenilaianWawancara;

    $casts = $model->getCasts();

    $this->assertArrayHasKey('nilai_komunikasi', $casts);
    $this->assertEquals('integer', $casts['nilai_komunikasi']);

    $this->assertArrayHasKey('nilai_motivasi', $casts);
    $this->assertEquals('integer', $casts['nilai_motivasi']);

    $this->assertArrayHasKey('nilai_kemampuan', $casts);
    $this->assertEquals('integer', $casts['nilai_kemampuan']);

    $this->assertArrayHasKey('nilai_total', $casts);
    $this->assertEquals('integer', $casts['nilai_total']);

    $this->assertArrayHasKey('kkm', $casts);
    $this->assertEquals('integer', $casts['kkm']);

    $this->assertArrayHasKey('nilai_rata_rata', $casts);
    $this->assertEquals('decimal:2', $casts['nilai_rata_rata']);
}


    /** @test */
    public function relasi_ke_pendaftaran_mengembalikan_belongs_to()
    {
        $model = new PenilaianWawancara;

        $this->assertInstanceOf(BelongsTo::class, $model->pendaftaran());
    }

    /** @test */
    public function relasi_ke_jadwal_mengembalikan_belongs_to()
    {
        $model = new PenilaianWawancara;

        $this->assertInstanceOf(BelongsTo::class, $model->jadwal());
    }

    /** @test */
    public function relasi_ke_penilai_mengembalikan_belongs_to()
    {
        $model = new PenilaianWawancara;

        $this->assertInstanceOf(BelongsTo::class, $model->penilai());
    }

    /** @test */
    public function accessor_pewawancara_mengembalikan_default_strip_jika_null()
    {
        $model = new PenilaianWawancara;

        $this->assertEquals('-', $model->pewawancara);
    }

    /** @test */
    public function accessor_nama_peserta_mengembalikan_default_strip_jika_null()
    {
        $model = new PenilaianWawancara;

        $this->assertEquals('-', $model->nama_peserta);
    }
}