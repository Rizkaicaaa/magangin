<?php

namespace Tests\Unit\Models;

use App\Models\InfoOr;
use Tests\TestCase;

class InfoOrModelTest extends TestCase
{
    /** @test */
    public function model_memiliki_nama_tabel_yang_benar()
    {
        $model = new InfoOr;

        $this->assertEquals('info_or', $model->getTable());
    }

    /** @test */
    public function model_memiliki_fillable_yang_benar()
    {
        $model = new InfoOr;

        $this->assertEquals([
            'judul',
            'deskripsi',
            'persyaratan_umum',
            'tanggal_buka',
            'tanggal_tutup',
            'periode',
            'gambar',
            'status',
        ], $model->getFillable());
    }

    /** @test */
    public function model_memiliki_casts_yang_benar()
    {
        $model = new InfoOr;

        $this->assertEquals([
            'tanggal_buka' => 'date',
            'tanggal_tutup' => 'date',
            'id' => 'int', // otomatis dari laravel
        ], $model->getCasts());
    }

    /** @test */
    public function relasi_ke_jadwal_kegiatan_harus_has_many()
    {
        $model = new InfoOr;

        $relasi = $model->jadwalKegiatan();

        $this->assertEquals('info_or_id', $relasi->getForeignKeyName());
        $this->assertEquals('id', $relasi->getLocalKeyName());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relasi);
    }

    /** @test */
    public function relasi_ke_jadwal_seleksi_harus_has_many()
    {
        $model = new InfoOr;

        $relasi = $model->jadwalSeleksi();

        $this->assertEquals('info_or_id', $relasi->getForeignKeyName());
        $this->assertEquals('id', $relasi->getLocalKeyName());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relasi);
    }

    /** @test */
    public function relasi_ke_pendaftaran_harus_has_many()
    {
        $model = new InfoOr;

        $relasi = $model->pendaftaran();

        $this->assertEquals('info_or_id', $relasi->getForeignKeyName());
        $this->assertEquals('id', $relasi->getLocalKeyName());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relasi);
    }

    /** @test */
    public function relasi_ke_template_sertifikat_harus_has_many()
    {
        $model = new InfoOr;

        $relasi = $model->templateSertifikat();

        $this->assertEquals('info_or_id', $relasi->getForeignKeyName());
        $this->assertEquals('id', $relasi->getLocalKeyName());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relasi);
    }
}