<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table Dinas
        Schema::create('dinas', function (Blueprint $table) {
            $table->increments('id'); // INT UNSIGNED AUTO_INCREMENT
            $table->string('nama_dinas', 50)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('kontak_person', 50)->nullable();
            $table->integer('kuota_magang')->default(0);
            $table->enum('status', ['buka', 'tutup'])->default('buka');
            $table->timestamps();
        });

        // Add foreign key constraint to users table after dinas table is created
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('dinas_id')->references('id')->on('dinas')->onDelete('set null');
        });

        // Table Info_OR
        Schema::create('info_or', function (Blueprint $table) {
            $table->increments('id'); // INT UNSIGNED AUTO_INCREMENT
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->text('persyaratan_umum')->nullable();
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->string('periode', 50);
            $table->string('gambar', 100)->nullable();
            $table->enum('status', ['buka', 'tutup'])->default('buka');
            $table->timestamps();
        });

        // Table Jadwal Seleksi
        Schema::create('jadwal_seleksi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('info_or_id');
            $table->foreign('info_or_id')->references('id')->on('info_or')->onDelete('cascade');
            $table->date('tanggal_seleksi');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('tempat', 100)->nullable();
            $table->timestamps();
        });

        // Table Pendaftaran
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('info_or_id');
            $table->foreign('info_or_id')->references('id')->on('info_or')->onDelete('cascade');
            $table->unsignedInteger('jadwal_seleksi_id')->nullable();
            $table->foreign('jadwal_seleksi_id')->references('id')->on('jadwal_seleksi')->onDelete('set null');
            $table->unsignedInteger('pilihan_dinas_1');
            $table->foreign('pilihan_dinas_1')->references('id')->on('dinas')->onDelete('cascade');
            $table->unsignedInteger('pilihan_dinas_2')->nullable();
            $table->foreign('pilihan_dinas_2')->references('id')->on('dinas')->onDelete('set null');
            $table->text('motivasi');
            $table->text('pengalaman')->nullable();
            $table->string('file_cv', 200);
            $table->string('file_transkrip', 200);
            $table->enum('status_pendaftaran', [
                'terdaftar',
                'lulus_wawancara',
                'tidak_lulus_wawancara',
                'lulus_magang',
                'tidak_lulus_magang'
            ])->default('terdaftar');
            $table->unsignedInteger('dinas_diterima_id')->nullable();
            $table->foreign('dinas_diterima_id')->references('id')->on('dinas')->onDelete('set null');
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->timestamps();
        });

        // Table Penilaian Wawancara
        Schema::create('penilaian_wawancara', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pendaftaran_id')->unique();
            $table->foreign('pendaftaran_id')->references('id')->on('pendaftaran')->onDelete('cascade');
            $table->unsignedInteger('penilai_id');
            $table->foreign('penilai_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('nilai_komunikasi', 5, 2)->nullable();
            $table->decimal('nilai_motivasi', 5, 2)->nullable();
            $table->decimal('nilai_kemampuan', 5, 2)->nullable();
            $table->decimal('nilai_total', 5, 2)->nullable();
            $table->enum('hasil', ['lulus', 'tidak_lulus'])->nullable();
            $table->enum('status', ['belum_dinilai', 'sudah_dinilai'])->default('belum_dinilai');
            $table->timestamps();
        });

        // Table Jadwal Kegiatan
        Schema::create('jadwal_kegiatan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dinas_id');
            $table->foreign('dinas_id')->references('id')->on('dinas')->onDelete('cascade');
            $table->string('nama_kegiatan', 200);
            $table->text('deskripsi_kegiatan')->nullable();
            $table->date('tanggal_kegiatan');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('tempat', 100)->nullable();
            $table->timestamps();
        });

        // Table Template Sertifikat
        Schema::create('template_sertifikat', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('info_or_id');
            $table->foreign('info_or_id')->references('id')->on('info_or')->onDelete('cascade');
            $table->string('nama_template', 100);
            $table->string('file_template', 200);
            $table->json('placeholder_fields')->nullable();
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->timestamps();
        });

        // Table Evaluasi Magang
        Schema::create('evaluasi_magang', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pendaftaran_id')->unique();
            $table->foreign('pendaftaran_id')->references('id')->on('pendaftaran')->onDelete('cascade');
            $table->unsignedInteger('penilai_id');
            $table->foreign('penilai_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('template_sertifikat_id');
            $table->foreign('template_sertifikat_id')->references('id')->on('template_sertifikat')->onDelete('cascade');
            $table->decimal('nilai_kedisiplinan', 5, 2)->nullable();
            $table->decimal('nilai_kerjasama', 5, 2)->nullable();
            $table->decimal('nilai_inisiatif', 5, 2)->nullable();
            $table->decimal('nilai_hasil_kerja', 5, 2)->nullable();
            $table->decimal('nilai_total', 5, 2)->nullable();
            $table->enum('hasil_evaluasi', ['lulus', 'tidak_lulus']);
            $table->string('nomor_sertifikat', 50)->unique()->nullable();
            $table->string('file_sertifikat', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_magang');
        Schema::dropIfExists('template_sertifikat');
        Schema::dropIfExists('jadwal_kegiatan');
        Schema::dropIfExists('penilaian_wawancara');
        Schema::dropIfExists('pendaftaran');
        Schema::dropIfExists('jadwal_seleksi');
        Schema::dropIfExists('info_or');
        
        // Drop foreign key constraint first before dropping dinas table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dinas_id']);
        });
        
        Schema::dropIfExists('dinas');
    }
};