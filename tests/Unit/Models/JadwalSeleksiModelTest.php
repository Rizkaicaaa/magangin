<?php

namespace Tests\Unit\Models;

use App\Models\JadwalSeleksi;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

class JadwalSeleksiModelTest extends TestCase
{
    /** @test */
    public function model_memiliki_nama_tabel_yang_benar()
    {
        $model = new JadwalSeleksi;

        $this->assertEquals('jadwal_seleksi', $model->getTable());
    }

    /** @test */
    public function model_memiliki_fillable_yang_sesuai()
    {
        $model = new JadwalSeleksi;

        $this->assertEquals([
            'info_or_id',
            'pendaftaran_id',
            'tanggal_seleksi',
            'waktu_mulai',
            'waktu_selesai',
            'tempat',
            'pewawancara',
        ], $model->getFillable());
    }

    /** @test */
 /** @test */
public function model_memiliki_casts_yang_sesuai()
{
    $model = new JadwalSeleksi;

    $casts = $model->getCasts();

    $this->assertEquals('date', $casts['tanggal_seleksi']);
    $this->assertEquals('datetime', $casts['waktu_mulai']);
    $this->assertEquals('datetime', $casts['waktu_selesai']);
}


    /** @test */
    public function model_memiliki_primary_key_default()
    {
        $model = new JadwalSeleksi;

        // default Laravel: 'id'
        $this->assertEquals('id', $model->getKeyName());
    }

    /** @test */
    public function model_menggunakan_timestamps_default()
    {
        $model = new JadwalSeleksi;

        $this->assertTrue($model->usesTimestamps());
    }

    /** @test */
    public function relasi_ke_info_or_mengembalikan_belongs_to()
    {
        $model = new JadwalSeleksi;

        $this->assertInstanceOf(BelongsTo::class, $model->infoOr());
    }

    /** @test */
    public function relasi_ke_pendaftaran_mengembalikan_belongs_to()
    {
        $model = new JadwalSeleksi;

        $this->assertInstanceOf(BelongsTo::class, $model->pendaftaran());
    }

    /** @test */
    public function relasi_ke_mahasiswas_mengembalikan_belongs_to_many()
    {
        $model = new JadwalSeleksi;

        $this->assertInstanceOf(BelongsToMany::class, $model->mahasiswas());
    }

}