<?php
namespace Database\Factories;

use App\Models\Pendaftaran;
use App\Models\User;
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
'jadwal_seleksi_id' => null,
'pilihan_dinas_1' => null,
'pilihan_dinas_2' => null,
'motivasi' => 'Motivasi singkat',
'pengalaman' => 'Pengalaman singkat',
'file_cv' => null,
'file_transkrip' => null,
'status_pendaftaran' => 'pending',
'dinas_diterima_id' => null,
'tanggal_daftar' => now(),
];
}

public function untukUser(int $userId): static
{
return $this->state(fn (array $attributes) => [
'user_id' => $userId,
]);
}

public function untukPeriode(int $infoOrId): static
{
return $this->state(fn (array $attributes) => [
'info_or_id' => $infoOrId,
]);
}
}