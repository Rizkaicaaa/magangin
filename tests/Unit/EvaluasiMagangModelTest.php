<?php

namespace Tests\Unit;

use App\Models\EvaluasiMagang;
use Tests\TestCase;

class EvaluasiMagangModelTest extends TestCase
{
    /** @test */
    public function tabel_yang_digunakan_sesuai()
    {
        $model = new EvaluasiMagang();

        $this->assertEquals('evaluasi_magang', $model->getTable());
    }

    /** @test */
    public function atribut_fillable_sesuai()
    {
        $model = new EvaluasiMagang();

        $this->assertEquals([
            'pendaftaran_id',
            'penilai_id',
            'template_sertifikat_id',
            'nilai_kedisiplinan',
            'nilai_kerjasama',
            'nilai_inisiatif',
            'nilai_hasil_kerja',
            'nilai_total',
            'nomor_sertifikat',
            'file_sertifikat',
        ], $model->getFillable());
    }

    /** @test */
    public function atribut_casts_sesuai()
    {
        $model = new EvaluasiMagang();
        $casts = $model->getCasts();

        $this->assertEquals('decimal:2', $casts['nilai_kedisiplinan']);
        $this->assertEquals('decimal:2', $casts['nilai_kerjasama']);
        $this->assertEquals('decimal:2', $casts['nilai_inisiatif']);
        $this->assertEquals('decimal:2', $casts['nilai_hasil_kerja']);
        $this->assertEquals('decimal:2', $casts['nilai_total']);
    }

    /** @test */
    public function relasi_ke_pendaftaran_sesuai()
    {
        $model = new EvaluasiMagang();

        $relation = $model->pendaftaran();

        $this->assertEquals('pendaftaran_id', $relation->getForeignKeyName());
        $this->assertEquals('App\Models\Pendaftaran', $relation->getRelated()::class);
    }

    /** @test */
    public function relasi_ke_penilai_sesuai()
    {
        $model = new EvaluasiMagang();

        $relation = $model->penilai();

        $this->assertEquals('penilai_id', $relation->getForeignKeyName());
        $this->assertEquals('App\Models\User', $relation->getRelated()::class);
    }

    /** @test */
    public function relasi_ke_template_sertifikat_sesuai()
    {
        $model = new EvaluasiMagang();

        $relation = $model->templateSertifikat();

        $this->assertEquals('template_sertifikat_id', $relation->getForeignKeyName());
        $this->assertEquals('App\Models\TemplateSertifikat', $relation->getRelated()::class);
    }
}