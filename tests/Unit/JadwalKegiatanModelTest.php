<?php

namespace Tests\Unit\Models;

use App\Models\JadwalKegiatan;
use Tests\TestCase;

class JadwalKegiatanModelTest extends TestCase
{
    /** @test */
    public function model_memiliki_nama_tabel_yang_benar()
    {
        $model = new JadwalKegiatan;

        $this->assertEquals('jadwal_kegiatan', $model->getTable());
    }

    /** @test */
    public function model_memiliki_fillable_yang_benar()
    {
        $model = new JadwalKegiatan;

        $this->assertEquals([
            'info_or_id',
            'nama_kegiatan',
            'deskripsi_kegiatan',
            'tanggal_kegiatan',
            'waktu_mulai',
            'waktu_selesai',
            'tempat',
        ], $model->getFillable());
    }

    /** @test */
    public function model_memiliki_casts_yang_benar()
    {
        $model = new JadwalKegiatan;

        $this->assertEquals([
            'tanggal_kegiatan' => 'date',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
            'id' => 'int', // otomatis ditambahkan Laravel
        ], $model->getCasts());
    }

    /** @test */
    public function relasi_ke_info_or_harus_belongs_to()
    {
        $model = new JadwalKegiatan;

        $relasi = $model->infoOr();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $relasi
        );

        $this->assertEquals('info_or_id', $relasi->getForeignKeyName());
        $this->assertEquals('id', $relasi->getOwnerKeyName());
    }

    /** @test */
    public function accessor_periode_mengambil_data_dari_relasi_info_or()
    {
        $model = new JadwalKegiatan;

        // Karena ini murni unit test, cukup isi relasi secara manual
        $mockInfoOr = new \stdClass;
        $mockInfoOr->periode = '2025';

        // set relasi manual tanpa DB
        $model->setRelation('infoOr', $mockInfoOr);

        $this->assertEquals('2025', $model->periode);
    }

    /** @test */
   /** @test */
public function scope_by_periode_menambahkan_kondisi_where_yang_benar()
{
    $query = JadwalKegiatan::query();

    $scoped = (new JadwalKegiatan)->scopeByPeriode($query, 10);

    $this->assertStringContainsString(
        'info_or_id',
        $scoped->toSql()
    );

    $this->assertEquals([10], $scoped->getBindings());
}

    /** @test */
    public function scope_aktif_memiliki_query_where_has_yang_benar()
    {
        $query = JadwalKegiatan::query();

        $scoped = (new JadwalKegiatan)->scopeAktif($query);

        // Karena whereHas kompleks, cukup cek apakah terdapat "exists"
        $this->assertStringContainsString('exists', $scoped->toSql());
    }
}