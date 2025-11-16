<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dinas', function (Blueprint $table) {
            $table->dropColumn(['status', 'kuota_magang']);
        });
    }

    public function down(): void
    {
        Schema::table('dinas', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->integer('kuota_magang')->nullable();
        });
    }
};