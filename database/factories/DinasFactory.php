<?php

namespace Database\Factories;

use App\Models\Dinas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dinas>
 */
class DinasFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Dinas::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $namaDinas = fake()->randomElement([
            'Dinas Pendidikan',
            'Dinas Kesehatan',
            'Dinas Perhubungan',
            'Dinas Pekerjaan Umum',
            'Dinas Sosial',
            'Dinas Kependudukan dan Catatan Sipil',
            'Dinas Perindustrian dan Perdagangan',
            'Dinas Pariwisata',
            'Dinas Komunikasi dan Informatika',
            'Dinas Lingkungan Hidup',
        ]);

        return [
            'nama_dinas' => $namaDinas,
            'alamat' => fake()->address(),
            'no_telp' => fake()->numerify('021-########'),
            'email' => strtolower(str_replace(' ', '', $namaDinas)) . '@' . fake()->freeEmailDomain(),
            'deskripsi' => fake()->optional()->paragraph(2),
            'kuota_peserta' => fake()->numberBetween(5, 20),
            'status' => 'aktif',
        ];
    }

    /**
     * State untuk dinas dengan status aktif
     */
    public function aktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'aktif',
        ]);
    }

    /**
     * State untuk dinas dengan status non-aktif
     */
    public function nonAktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'non_aktif',
        ]);
    }

    /**
     * State untuk dinas dengan kuota tertentu
     */
    public function withKuota(int $kuota): static
    {
        return $this->state(fn (array $attributes) => [
            'kuota_peserta' => $kuota,
        ]);
    }

    /**
     * State untuk dinas tanpa kuota (unlimited)
     */
    public function unlimitedKuota(): static
    {
        return $this->state(fn (array $attributes) => [
            'kuota_peserta' => null,
        ]);
    }
}