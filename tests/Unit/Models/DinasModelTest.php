<?php

namespace Tests\Unit\Models;

use App\Models\Dinas;
use Tests\TestCase;

class DinasModelTest extends TestCase
{
    /** @test */
    public function nama_tabel_sesuai()
    {
        $model = new Dinas();

        $this->assertEquals('dinas', $model->getTable());
    }

    /** @test */
    public function atribut_fillable_sesuai()
    {
        $model = new Dinas();

        $this->assertEquals([
            'nama_dinas',
            'deskripsi',
            'kontak_person',
        ], $model->getFillable());
    }

    /** @test */
    public function atribut_casts_sesuai()
    {
        $model = new Dinas();
        $casts = $model->getCasts();

        $this->assertEquals('datetime', $casts['created_at']);
        $this->assertEquals('datetime', $casts['updated_at']);
    }

    /** @test */
    public function relasi_pendaftaran_pilihan_1_sesuai()
    {
        $model = new Dinas();

        $relation = $model->pendaftaranPilihan1();

        $this->assertEquals('pilihan_dinas_1', $relation->getForeignKeyName());
        $this->assertEquals('App\Models\Pendaftaran', $relation->getRelated()::class);
    }

    /** @test */
    public function relasi_pendaftaran_pilihan_2_sesuai()
    {
        $model = new Dinas();

        $relation = $model->pendaftaranPilihan2();

        $this->assertEquals('pilihan_dinas_2', $relation->getForeignKeyName());
        $this->assertEquals('App\Models\Pendaftaran', $relation->getRelated()::class);
    }

    /** @test */
    public function relasi_pendaftaran_diterima_sesuai()
    {
        $model = new Dinas();

        $relation = $model->pendaftaranDiterima();

        $this->assertEquals('dinas_diterima_id', $relation->getForeignKeyName());
        $this->assertEquals('App\Models\Pendaftaran', $relation->getRelated()::class);
    }

    /** @test */
    public function relasi_users_sesuai()
    {
        $model = new Dinas();

        $relation = $model->users();

        $this->assertEquals('dinas_id', $relation->getForeignKeyName());
        $this->assertEquals('App\Models\User', $relation->getRelated()::class);
    }

    /** @test */
    public function accessor_total_pendaftar_pilihan_1_bekerja()
    {
        $model = new Dinas();

        // Karena tidak membuat data real, 
        // maka koleksi default kosong -> count = 0
        $this->assertEquals(0, $model->total_pendaftar_pilihan_1);
    }

    /** @test */
    public function accessor_total_pendaftar_pilihan_2_bekerja()
    {
        $model = new Dinas();

        $this->assertEquals(0, $model->total_pendaftar_pilihan_2);
    }

    /** @test */
    public function accessor_total_pendaftar_bekerja()
    {
        $model = new Dinas();

        $this->assertEquals(0, $model->total_pendaftar);
    }

    /** @test */
    public function accessor_total_diterima_bekerja()
    {
        $model = new Dinas();

        $this->assertEquals(0, $model->total_diterima);
    }
}