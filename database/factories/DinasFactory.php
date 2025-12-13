<?php
// database/factories/DinasFactory.php
namespace Database\Factories;

use App\Models\Dinas;
use Illuminate\Database\Eloquent\Factories\Factory;

class DinasFactory extends Factory
{
    protected $model = Dinas::class;

    public function definition(): array
    {
        return [
        'nama_dinas' => $this->faker->unique()->company(),
        'deskripsi' => $this->faker->sentence(),
        'kontak_person' => $this->faker->phoneNumber(),

    ];
    }
}