<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal_seleksi', function (Blueprint $table) {
            $table->string('pewawancara')->after('tempat'); 
            // after('tempat') biar kolomnya muncul setelah kolom tempat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_seleksi', function (Blueprint $table) {
            $table->dropColumn('pewawancara');
        });
    }
};
