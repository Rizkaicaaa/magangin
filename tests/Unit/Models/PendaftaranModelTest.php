<?php

namespace Tests\Unit\Models;

use App\Models\Pendaftaran;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

class PendaftaranModelTest extends TestCase
{
    /** @test */
    public function model_memakai_nama_tabel_yang_sesuai()
    {
        $model = new Pendaftaran;

        $this->assertEquals('pendaftaran', $model->getTable());
    }

    /** @test */
    public function model_memiliki_fillable_yang_sesuai()
    {
        $model = new Pendaftaran;

        $this->assertEquals([
            'user_id',
            'info_or_id',
            'jadwal_seleksi_id',
            'pilihan_dinas_1',
            'pilihan_dinas_2',
            'motivasi',
            'pengalaman',
            'file_cv',
            'file_transkrip',
            'status_pendaftaran',
            'dinas_diterima_id',
            'tanggal_daftar',
        ], $model->getFillable());
    }

    /** @test */
    public function model_memiliki_casts_yang_sesuai()
    {
        $model = new Pendaftaran;

        $casts = $model->getCasts();

        $this->assertEquals('datetime', $casts['tanggal_daftar']);
    }

    /** @test */
    public function relasi_ke_user_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $this->assertInstanceOf(BelongsTo::class, $model->user());
    }

    /** @test */
    public function relasi_ke_info_or_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $this->assertInstanceOf(BelongsTo::class, $model->infoOr());
    }

    /** @test */
    public function relasi_ke_jadwal_seleksi_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $this->assertInstanceOf(BelongsTo::class, $model->jadwalSeleksi());
    }

    /** @test */
    public function relasi_ke_dinas_pilihan_1_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $this->assertInstanceOf(BelongsTo::class, $model->dinasPilihan1());
    }

    /** @test */
    public function relasi_ke_dinas_pilihan_2_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $this->assertInstanceOf(BelongsTo::class, $model->dinasPilihan2());
    }

    /** @test */
    public function relasi_ke_dinas_diterima_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $this->assertInstanceOf(BelongsTo::class, $model->dinasDiterima());
    }

    /** @test */
    public function relasi_ke_penilaian_wawancara_mengembalikan_has_one()
    {
        $model = new Pendaftaran;

        $this->assertInstanceOf(HasOne::class, $model->penilaianWawancara());
    }

    /** @test */
    public function relasi_ke_evaluasi_magang_mengembalikan_has_one()
    {
        $model = new Pendaftaran;

        $this->assertInstanceOf(HasOne::class, $model->evaluasiMagang());
    }

    /** @test */
    public function relasi_ke_jadwals_mengembalikan_belongs_to_many()
    {
        $model = new Pendaftaran;

        $this->assertInstanceOf(BelongsToMany::class, $model->jadwals());
    }
}