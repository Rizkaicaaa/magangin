<?php

namespace Tests\Unit\Models;

use App\Models\TemplateSertifikat;
use App\Models\InfoOr;
use App\Models\EvaluasiMagang;
use Mockery;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateSertifikatTest extends TestCase
{
    /** @test */
    public function uji_create_template_sertifikat()
    {
        // Arrange
        $data = [
            'info_or_id' => 1,
            'nama_template' => 'Template Sertifikat Magang 2025',
            'file_template' => 'template_sertifikat_2025.pdf',
            'status' => 'aktif',
        ];

        // Mock model
        $mock = Mockery::mock(TemplateSertifikat::class)->makePartial();
        $mock->shouldReceive('create')
             ->once()
             ->with($data)
             ->andReturn(new TemplateSertifikat($data));

        // Act
        $template = $mock->create($data);

        // Assert
        $this->assertInstanceOf(TemplateSertifikat::class, $template);
        $this->assertEquals($data['info_or_id'], $template->info_or_id);
        $this->assertEquals($data['nama_template'], $template->nama_template);
        $this->assertEquals($data['file_template'], $template->file_template);
        $this->assertEquals($data['status'], $template->status);
    }

    /** @test */
    public function uji_fillable_fields_template_sertifikat()
    {
        // Arrange
        $model = new TemplateSertifikat();

        // Act
        $fillable = $model->getFillable();

        // Assert
        $this->assertEquals([
            'info_or_id',
            'nama_template',
            'file_template',
            'status',
        ], $fillable);
    }

    /** @test */
    public function uji_casts_placeholder_fields_template_sertifikat()
    {
        // Arrange
        $model = new TemplateSertifikat();

        // Act
        $casts = $model->getCasts();

        // Assert
        $this->assertArrayHasKey('placeholder_fields', $casts);
        $this->assertEquals('array', $casts['placeholder_fields']);
    }

    /** @test */
    public function uji_relasi_belongs_to_info_or()
    {
        // Arrange
        $model = new TemplateSertifikat();

        // Act
        $relation = $model->infoOr();

        // Assert
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('info_or_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    /** @test */
    public function uji_relasi_has_many_evaluasi_magang()
    {
        // Arrange
        $model = new TemplateSertifikat();

        // Act
        $relation = $model->evaluasiMagang();

        // Assert
        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('template_sertifikat_id', $relation->getForeignKeyName());
    }
}