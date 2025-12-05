<?php
namespace Database\Factories;

use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\Dinas;
use App\Models\InfoOr;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendaftaranFactory extends Factory
{
    protected $model = Pendaftaran::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'info_or_id' => InfoOr::factory(),
            'pilihan_dinas_1' => Dinas::factory(),
            'pilihan_dinas_2' => $this->faker->boolean(70) ? Dinas::factory() : null,
            'motivasi' => $this->faker->paragraph(3),
            'pengalaman' => $this->faker->boolean(80) ? $this->faker->paragraph(2) : null,
            'file_cv' => 'pendaftaran/cv/dummy_cv.pdf',
            'file_transkrip' => 'pendaftaran/transkrip/dummy_transkrip.pdf',
            'status_pendaftaran' => 'terdaftar',
            'tanggal_daftar' => now(),
        ];
    }

    /**
     * State untuk pendaftar yang lulus wawancara
     */
    public function lulusWawancara(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_pendaftaran' => 'lulus_wawancara',
        ]);
    }

    /**
     * State untuk pendaftar yang tidak lulus wawancara
     */
    public function tidakLulusWawancara(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_pendaftaran' => 'tidak_lulus_wawancara',
        ]);
    }

    /**
     * State untuk pendaftar yang lulus magang
     */
    public function lulusMagang(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_pendaftaran' => 'lulus_magang',
            'dinas_diterima_id' => Dinas::factory(),
        ]);
    }

    /**
     * State untuk pendaftar yang tidak lulus magang
     */
    public function tidakLulusMagang(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_pendaftaran' => 'tidak_lulus_magang',
        ]);
    }
}