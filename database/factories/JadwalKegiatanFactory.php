<?php
namespace Database\Factories;

use App\Models\JadwalKegiatan;
use App\Models\InfoOr;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalKegiatanFactory extends Factory
{
protected $model = JadwalKegiatan::class;

public function definition(): array
{
$tanggalKegiatan = fake()->dateTimeBetween('+1 day', '+30 days');
$waktuMulai = fake()->time('H:i:s');
$waktuSelesai = date('H:i:s', strtotime($waktuMulai) + 7200);

return [
'info_or_id' => InfoOr::factory(),
'nama_kegiatan' => fake()->randomElement([
'Seminar Orientasi',
'Workshop Kepemimpinan',
'Pengenalan Kampus',
'Outbound Training',
'Pelatihan Soft Skills',
]),
'deskripsi_kegiatan' => fake()->optional()->sentence(15),
'tanggal_kegiatan' => $tanggalKegiatan->format('Y-m-d'),
'waktu_mulai' => $waktuMulai,
'waktu_selesai' => $waktuSelesai,
'tempat' => fake()->randomElement([
'Aula Utama',
'Gedung A Lantai 2',
'Ruang Seminar',
]),
];
}

public function pagi(): static
{
return $this->state(fn (array $attributes) => [
'waktu_mulai' => '08:00:00',
'waktu_selesai' => '10:00:00',
]);
}

public function siang(): static
{
return $this->state(fn (array $attributes) => [
'waktu_mulai' => '13:00:00',
'waktu_selesai' => '15:00:00',
]);
}
}