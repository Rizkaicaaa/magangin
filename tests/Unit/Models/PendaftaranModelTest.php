<?php

namespace Tests\Unit\Models;

use App\Models\Pendaftaran;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;
use ReflectionClass;
use ReflectionMethod;

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

        $relation = $model->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    /** @test */
    public function relasi_ke_info_or_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $relation = $model->infoOr();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    /** @test */
    public function relasi_ke_jadwal_seleksi_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $relation = $model->jadwalSeleksi();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    /** @test */
    public function relasi_ke_dinas_pilihan_1_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $relation = $model->dinasPilihan1();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    /** @test */
    public function relasi_ke_dinas_pilihan_2_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $relation = $model->dinasPilihan2();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    /** @test */
    public function relasi_ke_dinas_diterima_mengembalikan_belongs_to()
    {
        $model = new Pendaftaran;

        $relation = $model->dinasDiterima();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    /** @test */
    public function relasi_ke_penilaian_wawancara_mengembalikan_has_one()
    {
        $model = new Pendaftaran;

        $relation = $model->penilaianWawancara();

        $this->assertInstanceOf(HasOne::class, $relation);
    }

    /** @test */
    public function relasi_ke_evaluasi_magang_mengembalikan_has_one()
    {
        // Gunakan Reflection untuk mengecek tanpa memanggil method secara langsung
        $reflection = new ReflectionClass(Pendaftaran::class);
        $method = $reflection->getMethod('evaluasiMagang');
        
        // Pastikan method ada
        $this->assertTrue($method->isPublic());
        
        // Cek source code method untuk memastikan return hasOne
        $filename = $reflection->getFileName();
        $startLine = $method->getStartLine() - 1;
        $endLine = $method->getEndLine();
        $length = $endLine - $startLine;
        
        $source = file($filename);
        $methodCode = implode("", array_slice($source, $startLine, $length));
        
        // Pastikan menggunakan hasOne
        $this->assertStringContainsString('hasOne', $methodCode);
        $this->assertStringContainsString('EvaluasiMagang::class', $methodCode);
        $this->assertStringContainsString('pendaftaran_id', $methodCode);
    }

    /** @test */
    public function relasi_ke_jadwals_mengembalikan_belongs_to_many()
    {
        $model = new Pendaftaran;

        $relation = $model->jadwals();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }

}