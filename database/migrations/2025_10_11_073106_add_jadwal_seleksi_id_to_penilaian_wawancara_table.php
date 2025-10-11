<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaian_wawancara', function (Blueprint $table) {
            // Tambahkan kolom jadwal_seleksi_id, nullable dulu supaya aman
            $table->unsignedInteger('jadwal_seleksi_id')->nullable()->after('penilai_id');

            // Tambahkan foreign key
            $table->foreign('jadwal_seleksi_id')
                  ->references('id')
                  ->on('jadwal_seleksi')
                  ->onDelete('set null'); // kalau jadwal dihapus, biar null
        });
    }

    public function down(): void
    {
        Schema::table('penilaian_wawancara', function (Blueprint $table) {
            $table->dropForeign(['jadwal_seleksi_id']);
            $table->dropColumn('jadwal_seleksi_id');
        });
    }
};
