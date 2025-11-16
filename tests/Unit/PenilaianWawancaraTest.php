<?php

namespace Tests\Unit;

use App\Models\PenilaianWawancara;
use App\Models\Pendaftaran;
use App\Models\JadwalSeleksi;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class PenilaianWawancaraTest extends TestCase
{
    /** @test */
    public function uji_create_penilaian_wawancara()
    {
        $data = [
            'pendaftaran_id' => 1,
            'penilai_id' => 2,
            'jadwal_seleksi_id' => 3,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 85,
            'nilai_kemampuan' => 90,
            'nilai_total' => 255,
            'nilai_rata_rata' => 85.00,
            'status' => 'Lulus',
            'kkm' => 75,
        ];

        $penilaian = new PenilaianWawancara($data);

        $this->assertEquals(1, $penilaian->pendaftaran_id);
        $this->assertEquals('Lulus', $penilaian->status);
        $this->assertEquals(85.00, $penilaian->nilai_rata_rata);
    }

    /** @test */
    public function uji_fillable_fields_penilaian_wawancara()
    {
        $model = new PenilaianWawancara();

        $this->assertEquals([
            'pendaftaran_id',
            'penilai_id',
            'jadwal_seleksi_id',
            'nilai_komunikasi',
            'nilai_motivasi',
            'nilai_kemampuan',
            'nilai_total',
            'nilai_rata_rata',
            'status',
            'kkm',
        ], $model->getFillable());
    }

/** @test */
/** @test */
public function uji_casts_field_penilaian_wawancara()
{
    $model = new PenilaianWawancara();

    $expectedCasts = [
        'nilai_komunikasi' => 'integer',
        'nilai_motivasi' => 'integer',
        'nilai_kemampuan' => 'integer',
        'nilai_total' => 'integer',
        'kkm' => 'integer',
        'nilai_rata_rata' => 'decimal:2',
    ];

    // Pastikan semua expected casts ada dalam model
    foreach ($expectedCasts as $key => $value) {
        $this->assertArrayHasKey($key, $model->getCasts());
        $this->assertEquals($value, $model->getCasts()[$key]);
    }
}



    /** @test */
    public function uji_relasi_ke_pendaftaran()
    {
        $model = new PenilaianWawancara();
        $relation = $model->pendaftaran();

        $this->assertEquals('pendaftaran_id', $relation->getForeignKeyName());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    /** @test */
    public function uji_relasi_ke_jadwal()
    {
        $model = new PenilaianWawancara();
        $relation = $model->jadwal();

        $this->assertEquals('jadwal_seleksi_id', $relation->getForeignKeyName());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    /** @test */
    public function uji_relasi_ke_penilai()
    {
        $model = new PenilaianWawancara();
        $relation = $model->penilai();

        $this->assertEquals('penilai_id', $relation->getForeignKeyName());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    /** @test */
    public function uji_accessor_pewawancara_dengan_jadwal()
    {
       $jadwalMock = Mockery::mock(JadwalSeleksi::class);
$jadwalMock->shouldReceive('offsetExists')->andReturn(true);
$jadwalMock->shouldReceive('getAttribute')->with('pewawancara')->andReturn('Bapak Ahmad');

$model = new PenilaianWawancara();
$model->setRelation('jadwal', $jadwalMock);

$this->assertEquals('Bapak Ahmad', $model->pewawancara);
 }

    /** @test */
    public function uji_accessor_pewawancara_tanpa_jadwal()
    {
        $model = new PenilaianWawancara();

        $this->assertEquals('-', $model->pewawancara);
    }

/** @test */
public function uji_accessor_nama_peserta_dengan_mock()
{
    // Gunakan stdClass sebagai stub sederhana
    $userStub = new \stdClass();
    $userStub->nama_lengkap = 'Budi';

    $pendaftaranStub = new \stdClass();
    $pendaftaranStub->user = $userStub;

    // Cast ke mock agar bisa di-set sebagai relasi
    $pendaftaranMock = Mockery::mock(Pendaftaran::class);
    $pendaftaranMock->shouldReceive('offsetExists')->andReturn(true);
    $pendaftaranMock->shouldReceive('getAttribute')
        ->with('user')
        ->andReturn($userStub);

    $penilaian = new PenilaianWawancara();
    $penilaian->setRelation('pendaftaran', $pendaftaranMock);

    $this->assertEquals('Budi', $penilaian->nama_peserta);
}

    /** @test */
    public function uji_accessor_nama_peserta_tanpa_pendaftaran()
    {
        $model = new PenilaianWawancara();

        $this->assertEquals('-', $model->nama_peserta);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}