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
            'nama_dinas' => 'Dinas ' . $this->faker->randomElement([
                'PSDM', 
                'Internal', 
                'Eksternal', 
                'Sosmasling'
            ]),
            'deskripsi' => $this->faker->sentence(10),
            'kontak_person' => $this->faker->numerify('08##########'),
        ];
    }
}