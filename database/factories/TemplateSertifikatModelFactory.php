<?php

namespace Database\Factories;

use App\Models\TemplateSertifikatModel;
use App\Models\InfoOr;
use Illuminate\Database\Eloquent\Factories\Factory;

class TemplateSertifikatModelFactory extends Factory
{
    protected $model = TemplateSertifikatModel::class;

    public function definition(): array
    {
        return [
            'info_or_id' => InfoOr::factory()->aktif(),
            'nama_template' => 'Template Sertifikat Magang',
            'file_template' => 'template-sertifikat.pdf',
            'status' => 'aktif',
            'placeholder_fields' => [],
        ];
    }
}
