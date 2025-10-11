<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penilaian_wawancara', function (Blueprint $table) {
            $table->decimal('kkm', 5, 2)->nullable()->after('nilai_rata_rata');
        });
    }

    public function down(): void
    {
        Schema::table('penilaian_wawancara', function (Blueprint $table) {
            $table->dropColumn('kkm');
        });
    }
};
