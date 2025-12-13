<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InfoOrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('info_or')->insert([
            'judul' => 'Pendaftaran Magang BEM FTI 2025',
            'deskripsi' => 'Program magang di Badan Eksekutif Mahasiswa Fakultas Teknologi Informasi untuk mengembangkan soft skill dan hard skill mahasiswa melalui pengalaman berorganisasi.',
            'persyaratan_umum' => "1. Mahasiswa aktif FTI UKSW\n2. IPK minimal 3.00\n3. Memiliki motivasi tinggi\n4. Dapat bekerja dalam tim\n5. Bersedia mengikuti program selama 6 bulan",
            'tanggal_buka' => Carbon::now()->format('Y-m-d'),
            'tanggal_tutup' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'periode' => 'Semester Genap 2024/2025',
            'gambar' => 'images/poster.jpg',
            'status' => 'buka',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}