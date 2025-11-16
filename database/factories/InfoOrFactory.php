<?php

namespace Database\Factories;

use App\Models\InfoOr;
use Illuminate\Database\Eloquent\Factories\Factory;

class InfoOrFactory extends Factory
{
    protected $model = InfoOr::class;

    public function definition(): array
    {
        $tanggalBuka = $this->faker->dateTimeBetween('-1 month', 'now');
        $tanggalTutup = $this->faker->dateTimeBetween('now', '+1 month');

        return [
            'judul' => $this->faker->sentence(4),
            'deskripsi' => $this->faker->paragraph(),
            'persyaratan_umum' => $this->faker->sentence(8),
            'tanggal_buka' => $tanggalBuka,
            'tanggal_tutup' => $tanggalTutup,
            'periode' => $this->faker->randomElement(['Ganjil', 'Genap']),
            'gambar' => $this->faker->imageUrl(640, 480, 'education', true),
            'status' => $this->faker->randomElement(['buka', 'tutup']),
        ];
    }
}