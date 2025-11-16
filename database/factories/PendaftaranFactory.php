<?php

namespace Database\Factories;

use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\InfoOr;
use App\Models\Dinas;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendaftaranFactory extends Factory
{
    protected $model = Pendaftaran::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'info_or_id' => InfoOr::factory(),
            'jadwal_seleksi_id' => null,
            'pilihan_dinas_1' => Dinas::factory(),
            'pilihan_dinas_2' => Dinas::factory(),
            'motivasi' => $this->faker->paragraph(),
            'pengalaman' => $this->faker->sentence(),
            'file_cv' => 'cv_' . $this->faker->word() . '.pdf',
            'file_transkrip' => 'transkrip_' . $this->faker->word() . '.pdf',
            'status_pendaftaran' => $this->faker->randomElement([
                'terdaftar',
                'lulus_wawancara',
                'tidak_lulus_wawancara',
                'lulus_magang',
                'tidak_lulus_magang',
            ]),
            'dinas_diterima_id' => null,
            'tanggal_daftar' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}