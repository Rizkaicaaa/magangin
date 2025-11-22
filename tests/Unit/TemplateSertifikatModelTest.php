<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\TemplateSertifikat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateSertifikatModelTest extends TestCase
{
    /** @test */
    public function model_memiliki_nama_tabel_yang_sesuai()
    {
        $model = new TemplateSertifikat;

        $this->assertEquals('template_sertifikat', $model->getTable());
    }

    /** @test */
    public function model_memiliki_fillable_yang_sesuai()
    {
        $model = new TemplateSertifikat;

        $this->assertEquals([
            'info_or_id',
            'nama_template',
            'file_template',
            'status',
        ], $model->getFillable());
    }

    /** @test */
    public function model_memiliki_casts_yang_sesuai()
    {
        $model = new TemplateSertifikat;
        $casts = $model->getCasts();

        // hanya dicek casts yang didefinisikan
        $this->assertArrayHasKey('placeholder_fields', $casts);
        $this->assertEquals('array', $casts['placeholder_fields']);
    }

    /** @test */
    public function model_memiliki_primary_key_default()
    {
        $model = new TemplateSertifikat;
        $this->assertEquals('id', $model->getKeyName());
    }

    /** @test */
    public function model_menggunakan_timestamps_default()
    {
        $model = new TemplateSertifikat;
        $this->assertTrue($model->usesTimestamps());
    }

    /** @test */
    public function relasi_ke_info_or_mengembalikan_belongs_to()
    {
        $model = new TemplateSertifikat;

        $this->assertInstanceOf(BelongsTo::class, $model->infoOr());
    }

    /** @test */
    public function relasi_ke_evaluasi_magang_mengembalikan_has_many()
    {
        $model = new TemplateSertifikat;

        $this->assertInstanceOf(HasMany::class, $model->evaluasiMagang());
    }
}