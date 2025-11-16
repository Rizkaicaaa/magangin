<?php

namespace Tests\Unit\Models;

use App\Models\EvaluasiMagang;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\TemplateSertifikat;
use Mockery;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiMagangTest extends TestCase
{
    /** @test */
    public function uji_create_evaluasi_magang()
    {
        // Arrange
        $data = [
            'pendaftaran_id' => 1,
            'penilai_id' => 2,
            'template_sertifikat_id' => 3,
            'nilai_kedisiplinan' => 90.50,
            'nilai_kerjasama' => 85.75,
            'nilai_inisiatif' => 88.00,
            'nilai_hasil_kerja' => 92.25,
            'nilai_total' => 89.63,
            'nomor_sertifikat' => 'CERT-2025-001',
            'file_sertifikat' => 'sertifikat_2025.pdf',
        ];

        // Mock model
        $mock = Mockery::mock(EvaluasiMagang::class)->makePartial();
        $mock->shouldReceive('create')
             ->once()
             ->with($data)
             ->andReturn(new EvaluasiMagang($data));

        // Act
        $evaluasi = $mock->create($data);

        // Assert
        $this->assertInstanceOf(EvaluasiMagang::class, $evaluasi);
        $this->assertEquals($data['pendaftaran_id'], $evaluasi->pendaftaran_id);
        $this->assertEquals($data['penilai_id'], $evaluasi->penilai_id);
        $this->assertEquals($data['template_sertifikat_id'], $evaluasi->template_sertifikat_id);
        $this->assertEquals($data['nilai_kedisiplinan'], $evaluasi->nilai_kedisiplinan);
        $this->assertEquals($data['nilai_total'], $evaluasi->nilai_total);
        $this->assertEquals($data['nomor_sertifikat'], $evaluasi->nomor_sertifikat);
    }

    /** @test */
    public function uji_fillable_fields_evaluasi_magang()
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
    public function uji_casts_fields_evaluasi_magang()
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
    public function uji_relasi_belongs_to_pendaftaran()
    {
        $model = new EvaluasiMagang();

        $relation = $model->pendaftaran();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('pendaftaran_id', $relation->getForeignKeyName());
    }

    /** @test */
    public function uji_relasi_belongs_to_penilai()
    {
        $model = new EvaluasiMagang();

        $relation = $model->penilai();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('penilai_id', $relation->getForeignKeyName());
    }

    /** @test */
    public function uji_relasi_belongs_to_template_sertifikat()
    {
        $model = new EvaluasiMagang();

        $relation = $model->templateSertifikat();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('template_sertifikat_id', $relation->getForeignKeyName());
    }
}