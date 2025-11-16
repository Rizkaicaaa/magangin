<?php

namespace Database\Factories;

use App\Models\Dinas;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'role' => $this->faker->randomElement(['superadmin', 'admin', 'mahasiswa']),
            'nama_lengkap' => $this->faker->name(),
            'nim' => $this->faker->optional()->numerify('22########'),
            'no_telp' => $this->faker->phoneNumber(),
            'tanggal_daftar' => $this->faker->date(),
            'status' => $this->faker->randomElement(['aktif', 'non_aktif']),
            'dinas_id' => Dinas::factory(), // relasi ke Dinas
            'remember_token' => Str::random(10),
        ];
    }
}