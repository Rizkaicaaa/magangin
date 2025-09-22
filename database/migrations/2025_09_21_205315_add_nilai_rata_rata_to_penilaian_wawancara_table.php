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
    Schema::table('penilaian_wawancara', function (Blueprint $table) {
        $table->decimal('nilai_rata_rata', 5, 2)->nullable()->after('nilai_kemampuan');
    });
}

public function down(): void
{
    Schema::table('penilaian_wawancara', function (Blueprint $table) {
        $table->dropColumn('nilai_rata_rata');
    });
}


};
