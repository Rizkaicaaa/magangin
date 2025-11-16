<?php

namespace Tests\Unit;

use App\Models\JadwalKegiatan;
use App\Models\InfoOr;
use Mockery;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;

class JadwalKegiatanTest extends TestCase
{
    /** @test */
    public function uji_create_jadwal_kegiatan()
    {
        $data = [
            'info_or_id' => 1,
            'nama_kegiatan' => 'Orientasi Mahasiswa Baru',
            'deskripsi_kegiatan' => 'Kegiatan pengenalan kampus untuk mahasiswa baru',
            'tanggal_kegiatan' => '2024-08-15',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '12:00:00',
            'tempat' => 'Auditorium Utama',
        ];

        $jadwal = new JadwalKegiatan($data);

        $this->assertEquals(1, $jadwal->info_or_id);
        $this->assertEquals('Orientasi Mahasiswa Baru', $jadwal->nama_kegiatan);
        $this->assertEquals('Auditorium Utama', $jadwal->tempat);
    }

    /** @test */
    public function uji_fillable_fields_jadwal_kegiatan()
    {
        $model = new JadwalKegiatan();

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
    public function uji_casts_field_jadwal_kegiatan()
    {
        $model = new JadwalKegiatan();

        $expectedCasts = [
            'tanggal_kegiatan' => 'date',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];

        foreach ($expectedCasts as $key => $value) {
            $this->assertArrayHasKey($key, $model->getCasts());
            $this->assertEquals($value, $model->getCasts()[$key]);
        }
    }

    /** @test */
    public function uji_relasi_ke_info_or()
    {
        $model = new JadwalKegiatan();
        $relation = $model->infoOr();

        $this->assertEquals('info_or_id', $relation->getForeignKeyName());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    /** @test */
    public function uji_accessor_periode_dengan_info_or()
    {
        $infoOrMock = Mockery::mock(InfoOr::class);
        $infoOrMock->shouldReceive('offsetExists')->andReturn(true);
        $infoOrMock->shouldReceive('getAttribute')->with('periode')->andReturn('2024/2025');

        $model = new JadwalKegiatan();
        $model->setRelation('infoOr', $infoOrMock);

        $this->assertEquals('2024/2025', $model->periode);
    }

    /** @test */
    public function uji_accessor_periode_tanpa_info_or()
    {
        $model = new JadwalKegiatan();

        $this->assertNull($model->periode);
    }

    /** @test */
    public function uji_scope_by_periode()
    {
        $queryMock = Mockery::mock(Builder::class);
        $queryMock->shouldReceive('where')
            ->once()
            ->with('info_or_id', 1)
            ->andReturnSelf();

        $model = new JadwalKegiatan();
        $result = $model->scopeByPeriode($queryMock, 1);

        $this->assertInstanceOf(Builder::class, $result);
    }

    /** @test */
    public function uji_scope_aktif()
    {
        $queryMock = Mockery::mock(Builder::class);
        
        $queryMock->shouldReceive('whereHas')
            ->once()
            ->with('infoOr', Mockery::on(function ($closure) {
                // Test bahwa closure dipanggil dengan query builder
                $subQueryMock = Mockery::mock(Builder::class);
                $subQueryMock->shouldReceive('where')
                    ->once()
                    ->with('status', 'buka')
                    ->andReturnSelf();
                
                $closure($subQueryMock);
                return true;
            }))
            ->andReturnSelf();

        $model = new JadwalKegiatan();
        $result = $model->scopeAktif($queryMock);

        $this->assertInstanceOf(Builder::class, $result);
    }

    /** @test */
    public function uji_table_name()
    {
        $model = new JadwalKegiatan();

        $this->assertEquals('jadwal_kegiatan', $model->getTable());
    }

    /** @test */
    public function uji_uses_has_factory_trait()
    {
        $model = new JadwalKegiatan();

        $this->assertContains(
            'Illuminate\Database\Eloquent\Factories\HasFactory',
            class_uses($model)
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}