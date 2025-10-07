<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('pendaftaran_id');
            $table->timestamps();

            // Foreign key ke jadwal_seleksi
            $table->foreign('jadwal_id')
                ->references('id')
                ->on('jadwal_seleksi')
                ->onDelete('cascade');

            // Foreign key ke pendaftaran
            $table->foreign('pendaftaran_id')
                ->references('id')
                ->on('pendaftaran')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pendaftaran');
    }
};
