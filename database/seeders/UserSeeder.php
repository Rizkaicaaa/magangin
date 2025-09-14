<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua data dinas
        $dinas = DB::table('dinas')->get();

        foreach ($dinas as $d) {
            if ($d->nama_dinas === 'Dinas PSDM') {
                // User untuk Dinas PSDM → superadmin
                DB::table('users')->insert([
                    'email'          => $d->kontak_person,
                    'password'       => Hash::make('password123'),
                    'role'           => 'superadmin',
                    'nama_lengkap'   => $d->nama_dinas,
                    'nim'            => null,
                    'no_telp'        => '081234567890',
                    'tanggal_daftar' => now(),
                    'status'         => 'aktif',
                    'dinas_id'       => $d->id,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } else {
                // User untuk dinas lain → admin
                DB::table('users')->insert([
                    'email'          => $d->kontak_person,
                    'password'       => Hash::make('password123'),
                    'role'           => 'admin',
                    'nama_lengkap'   => $d->nama_dinas,
                    'nim'            => null,
                    'no_telp'        => '081234567890',
                    'tanggal_daftar' => now(),
                    'status'         => 'aktif',
                    'dinas_id'       => $d->id,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        // Superadmin global tambahan
        DB::table('users')->insert([
            'email'          => 'superadmin@magangin.test',
            'password'       => Hash::make('password123'),
            'role'           => 'superadmin',
            'nama_lengkap'   => 'Super Admin Magangin',
            'nim'            => null,
            'no_telp'        => '081234567890',
            'tanggal_daftar' => now(),
            'status'         => 'aktif',
            'dinas_id'       => null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Contoh data mahasiswa untuk testing
        DB::table('users')->insert([
            [
                'email'          => 'mahasiswa1@test.com',
                'password'       => Hash::make('password123'),
                'role'           => 'mahasiswa',
                'nama_lengkap'   => 'Ahmad Rizki Pratama',
                'nim'            => '210001',
                'no_telp'        => '081234567891',
                'tanggal_daftar' => now(),
                'status'         => 'aktif',
                'dinas_id'       => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'email'          => 'mahasiswa2@test.com',
                'password'       => Hash::make('password123'),
                'role'           => 'mahasiswa',
                'nama_lengkap'   => 'Siti Nurhaliza',
                'nim'            => '210002',
                'no_telp'        => '081234567892',
                'tanggal_daftar' => now(),
                'status'         => 'aktif',
                'dinas_id'       => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}