<?php
// database/factories/InfoOrFactory.php
namespace Database\Factories;

use App\Models\InfoOr;
use Illuminate\Database\Eloquent\Factories\Factory;

class InfoOrFactory extends Factory
{
    protected $model = InfoOr::class;

    public function definition(): array
    {
        return [
            'judul' => 'Pendaftaran Magang BEM KM FTI' . $this->faker->year(),
            'deskripsi' => $this->faker->paragraph(3),
            'persyaratan_umum' => $this->faker->paragraph(2),
            'tanggal_buka' => now()->subDays(5),
            'tanggal_tutup' => now()->addDays(30),
            'periode' => now()->format('Y') . '-' . $this->faker->numberBetween(1, 2),
            'status' => 'buka',
        ];
    }

    /**
     * State untuk Info OR yang sudah ditutup
     */
    public function tutup(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'tutup',
            'tanggal_tutup' => now()->subDays(1),
        ]);
    }

    /**
     * State untuk Info OR yang aktif
     */
    public function aktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'buka',
            'tanggal_buka' => now()->subDays(5),
            'tanggal_tutup' => now()->addDays(30),
        ]);
    }
}