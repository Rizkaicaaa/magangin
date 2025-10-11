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
        $table->unsignedInteger('pendaftaran_id')->nullable()->after('info_or_id');
$table->foreign('pendaftaran_id')->references('id')->on('pendaftaran')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('jadwal_seleksi', function (Blueprint $table) {
        $table->dropForeign(['pendaftaran_id']);
        $table->dropColumn('pendaftaran_id');
    });
}

};
