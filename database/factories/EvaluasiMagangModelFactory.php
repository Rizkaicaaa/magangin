<?php

namespace Database\Factories;

use App\Models\EvaluasiMagangModel;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluasiMagangModelFactory extends Factory
{
    protected $model = EvaluasiMagangModel::class;

    public function definition(): array
    {
        return [
            'pendaftaran_id' => Pendaftaran::factory(),
            'penilai_id' => User::factory()->create(['role' => 'admin']),
            'nilai_kedisiplinan' => 80,
            'nilai_kerjasama' => 80,
            'nilai_inisiatif' => 80,
            'nilai_hasil_kerja' => 80,
            'nilai_total' => 80,
            'hasil_evaluasi' => 'Lulus',
        ];
    }
}