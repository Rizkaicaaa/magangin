<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('evaluasi_magang', function (Blueprint $table) {
            // Ubah kolom jadi nullable langsung
            $table->integer('template_sertifikat_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('evaluasi_magang', function (Blueprint $table) {
            // Kembalikan kolom jadi NOT NULL
            $table->integer('template_sertifikat_id')->nullable(false)->change();
        });
    }
};
