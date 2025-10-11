<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('evaluasi_magang', function (Blueprint $table) {
            // 1️⃣ Hapus foreign key lama dulu
            $table->dropForeign(['template_sertifikat_id']);
        });

        Schema::table('evaluasi_magang', function (Blueprint $table) {
            // 2️⃣ Ubah kolom jadi nullable dan pastikan tipe-nya UNSIGNED INTEGER
            $table->unsignedInteger('template_sertifikat_id')->nullable()->change();

            // 3️⃣ Tambahkan kembali foreign key
            $table->foreign('template_sertifikat_id')
                ->references('id')
                ->on('template_sertifikat')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('evaluasi_magang', function (Blueprint $table) {
            $table->dropForeign(['template_sertifikat_id']);
        });

        Schema::table('evaluasi_magang', function (Blueprint $table) {
            $table->unsignedInteger('template_sertifikat_id')->nullable(false)->change();

            $table->foreign('template_sertifikat_id')
                ->references('id')
                ->on('template_sertifikat')
                ->onDelete('cascade');
        });
    }
};