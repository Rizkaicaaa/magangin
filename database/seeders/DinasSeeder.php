<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DinasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dinas')->insert([
            [
                'nama_dinas' => 'Dinas PSDM',
                'deskripsi' => 'Pengembangan Sumber Daya Mahasiswa',
                'kontak_person' => 'psdm.bemfti@gmail.com',
                'kuota_magang' => 5,
                'status' => 'buka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_dinas' => 'Dinas Humas',
                'deskripsi' => 'Hubungan Masyarakat',
                'kontak_person' => 'humas.bemfti@gmail.com',
                'kuota_magang' => 5,
                'status' => 'buka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_dinas' => 'Dinas Internal',
                'deskripsi' => 'Berkaitan dengan internal BEM FTI',
                'kontak_person' => 'internal.bemfti@gmail.com',
                'kuota_magang' => 5,
                'status' => 'buka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_dinas' => 'Dinas Eksternal',
                'deskripsi' => 'Menjalin komunikasi dengan pihak luar',
                'kontak_person' => 'eksternal.bemfti@gmail.com',
                'kuota_magang' => 5,
                'status' => 'buka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}