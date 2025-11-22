<?php

namespace Tests\Unit\Models;

use App\Models\HasilSeleksi;
use Tests\TestCase;

class HasilSeleksiModelTest extends TestCase
{
    /** @test */
    public function model_memakai_nama_tabel_yang_sesuai()
    {
        $model = new HasilSeleksi;

        $this->assertEquals('hasil_seleksi', $model->getTable());
    }

    /** @test */
    public function model_memakai_primary_key_yang_sesuai()
    {
        $model = new HasilSeleksi;

        $this->assertEquals('ID_Hasil_Seleksi', $model->getKeyName());
    }

    /** @test */
    public function model_tidak_menggunakan_timestamps()
    {
        $model = new HasilSeleksi;

        $this->assertFalse($model->timestamps);
    }

    /** @test */
    public function model_memiliki_fillable_yang_sesuai()
    {
        $model = new HasilSeleksi;

        $this->assertEquals([
            'ID_Nilai_Wawancara',
            'Nilai_Total',
            'Status_Seleksi',
        ], $model->getFillable());
    }

    /** @test */
  /** @test */
    public function model_memiliki_casts_otomatis_primary_key()
    {
        $model = new HasilSeleksi;

        $this->assertEquals([
            'ID_Hasil_Seleksi' => 'int'
        ], $model->getCasts());
    }


    /** @test */
    public function model_memiliki_hidden_kosong()
    {
        $model = new HasilSeleksi;

        $this->assertEquals([], $model->getHidden());
    }

    /** @test */
    public function model_memiliki_appends_kosong()
    {
        $model = new HasilSeleksi;

        $this->assertEquals([], $model->getAppends());
    }
}