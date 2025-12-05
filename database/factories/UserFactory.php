<?php

// database/factories/UserFactory.php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nama_lengkap' => $this->faker->name(),
            'nim' => $this->faker->unique()->numerify('2211####'),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'no_telp' => $this->faker->numerify('08##########'),
            'tanggal_daftar' => now(),
            'status' => 'aktif',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * State untuk admin
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'nim' => null,
        ]);
    }

    /**
     * State untuk superadmin
     */
    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'superadmin',
            'nim' => null,
        ]);
    }

    /**
     * State untuk mahasiswa
     */
    public function mahasiswa(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'mahasiswa',
        ]);
    }

    /**
     * State untuk user non aktif
     */
    public function nonAktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'non_aktif',
        ]);
    }
}