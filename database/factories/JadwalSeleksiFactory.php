<?php

namespace Database\Factories;

use App\Models\JadwalSeleksi;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalSeleksiFactory extends Factory
{
    protected $model = JadwalSeleksi::class;

    public function definition(): array
    {
        return [
            'info_or_id' => InfoOr::factory(),
            'pendaftaran_id' => null, 
            'tanggal_seleksi' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '17:00:00',
            'tempat' => $this->faker->address(),
            'pewawancara' => $this->faker->name(), 
        ];
    }

    public function withPewawancara(string $nama): static
    {
        return $this->state(fn (array $attributes) => [
            'pewawancara' => $nama,
        ]);
    }

    public function forPendaftaran(int $pendaftaranId): static
    {
        return $this->state(fn (array $attributes) => [
            'pendaftaran_id' => $pendaftaranId,
        ]);
    }
}