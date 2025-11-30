<?php
namespace Database\Factories;

use App\Models\InfoOr;
use Illuminate\Database\Eloquent\Factories\Factory;

class InfoOrFactory extends Factory
{
protected $model = InfoOr::class;

public function definition(): array
{
$tahunSekarang = now()->year;
$tahunDepan = $tahunSekarang + 1;

return [
'judul' => 'Pendaftaran Orientasi ' . $tahunSekarang,
'deskripsi' => 'Deskripsi singkat orientasi',
'persyaratan_umum' => 'Persyaratan umum peserta',
'periode' => $tahunSekarang . '/' . $tahunDepan,
'status' => 'buka',
'tanggal_buka' => now()->subDays(5),
'tanggal_tutup' => now()->addDays(30),
'gambar' => null,
];
}

public function buka(): static
{
return $this->state(fn (array $attributes) => [
'status' => 'buka',
'tanggal_buka' => now()->subDays(5),
'tanggal_tutup' => now()->addDays(30),
]);
}

public function tutup(): static
{
return $this->state(fn (array $attributes) => [
'status' => 'tutup',
'tanggal_buka' => now()->subDays(60),
'tanggal_tutup' => now()->subDays(1),
]);
}

public function periode(string $periode): static
{
return $this->state(fn (array $attributes) => [
'periode' => $periode,
]);
}
}