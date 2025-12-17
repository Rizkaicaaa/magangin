<?php

namespace Database\Factories;

use App\Models\PenilaianWawancara;
use App\Models\Pendaftaran;
use App\Models\JadwalSeleksi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenilaianWawancaraFactory extends Factory
{
    protected $model = PenilaianWawancara::class;

    public function definition(): array
    {
        $nilaiKomunikasi = $this->faker->numberBetween(60, 100);
        $nilaiMotivasi = $this->faker->numberBetween(60, 100);
        $nilaiKemampuan = $this->faker->numberBetween(60, 100);
        
        $nilaiTotal = $nilaiKomunikasi + $nilaiMotivasi + $nilaiKemampuan;
        $nilaiRataRata = round($nilaiTotal / 3, 2);

        return [
            'pendaftaran_id' => Pendaftaran::factory(),
            'penilai_id' => User::factory(),
            'jadwal_seleksi_id' => JadwalSeleksi::factory(),
            'nilai_komunikasi' => $nilaiKomunikasi,
            'nilai_motivasi' => $nilaiMotivasi,
            'nilai_kemampuan' => $nilaiKemampuan,
            'kkm' => null,
            'nilai_total' => $nilaiTotal,
            'nilai_rata_rata' => $nilaiRataRata,
            'hasil' => null,
            'status' => 'sudah_dinilai',
        ];
    }

    public function belumDinilai(): static
    {
        return $this->state(fn (array $attributes) => [
            'nilai_komunikasi' => null,
            'nilai_motivasi' => null,
            'nilai_kemampuan' => null,
            'nilai_total' => 0,
            'nilai_rata_rata' => 0,
            'status' => 'belum_dinilai',
        ]);
    }

    public function withValues(int $komunikasi, int $motivasi, int $kemampuan): static
    {
        $total = $komunikasi + $motivasi + $kemampuan;
        $rataRata = round($total / 3, 2);

        return $this->state(fn (array $attributes) => [
            'nilai_komunikasi' => $komunikasi,
            'nilai_motivasi' => $motivasi,
            'nilai_kemampuan' => $kemampuan,
            'nilai_total' => $total,
            'nilai_rata_rata' => $rataRata,
            'status' => 'sudah_dinilai',
        ]);
    }

    public function lulus(int $kkm = 70): static
    {
        return $this->state(fn (array $attributes) => [
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 90,
            'nilai_kemampuan' => 88,
            'nilai_total' => 263,
            'nilai_rata_rata' => 87.67,
            'kkm' => $kkm,
            'hasil' => 'lulus',
            'status' => 'sudah_dinilai',
        ]);
    }

    public function tidakLulus(int $kkm = 70): static
    {
        return $this->state(fn (array $attributes) => [
            'nilai_komunikasi' => 60,
            'nilai_motivasi' => 65,
            'nilai_kemampuan' => 62,
            'nilai_total' => 187,
            'nilai_rata_rata' => 62.33,
            'kkm' => $kkm,
            'hasil' => 'tidak_lulus',
            'status' => 'sudah_dinilai',
        ]);
    }
}