<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PenilaianWawancaraController;

class PenilaianWawancaraControllerTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** INDEX */
    public function test_index_menampilkan_data()
    {
        $mockPW = Mockery::mock('alias:App\\Models\\PenilaianWawancara');
        $mockPW->shouldReceive('with')->with('pendaftaran.user','jadwal')->andReturnSelf();
        $mockPW->shouldReceive('get')->andReturn(collect([(object)['id'=>1]]));

        DB::shouldReceive('table')->with('penilaian_wawancara')->andReturnSelf();
        DB::shouldReceive('max')->with('kkm')->andReturn(75);

        $controller = new PenilaianWawancaraController();
        $response = $controller->index();

        $this->assertEquals('penilaian-wawancara.index', $response->name());
    }

    /** CREATE */
    public function test_create_menampilkan_form()
    {
        $mockJadwal = Mockery::mock('alias:App\\Models\\JadwalSeleksi');
        $mockJadwal->shouldReceive('with')->with('pendaftaran.user')->andReturnSelf();
        $mockJadwal->shouldReceive('get')->andReturn(collect([(object)['id'=>1]]));

        $mockPW = Mockery::mock('alias:App\\Models\\PenilaianWawancara');
        $mockPW->shouldReceive('pluck')->with('pendaftaran_id')->andReturn(collect([1, 2]));

        $controller = new PenilaianWawancaraController();
        $response = $controller->create();

        $this->assertEquals('penilaian-wawancara.create', $response->name());
    }

    /** STORE */
    public function test_store_menyimpan_penilaian()
    {
        $req = Request::create('/', 'POST', [
            'pendaftaran_id' => 1,
            'jadwal_seleksi_id' => 10,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 70,
            'nilai_kemampuan' => 90,
            'kkm' => 75,
        ]);

        // Mock DB connection untuk validator exists
        $mockQuery = Mockery::mock();
        $mockQuery->shouldReceive('useWritePdo')->andReturnSelf();
        $mockQuery->shouldReceive('where')->andReturnSelf();
        $mockQuery->shouldReceive('whereIn')->andReturnSelf();
        $mockQuery->shouldReceive('count')->andReturn(1);

        $mockConnection = Mockery::mock();
        $mockConnection->shouldReceive('table')->andReturn($mockQuery);

        DB::shouldReceive('connection')->andReturn($mockConnection);

        // Mock PenilaianWawancara
        $mockPW = Mockery::mock('overload:App\Models\PenilaianWawancara');
        $mockPW->shouldReceive('where')->with('pendaftaran_id', 1)->andReturnSelf();
        $mockPW->shouldReceive('exists')->andReturn(false);
        $mockPW->shouldReceive('create')->andReturn((object)['id' => 1]);

        $controller = new PenilaianWawancaraController();
        $response = $controller->store($req);
        
        $this->assertEquals(302, $response->status());
    }

    /** EDIT */
    public function test_edit_menampilkan_form_edit()
    {
        $mockPendaftaran = Mockery::mock('alias:App\\Models\\Pendaftaran');
        $mockPendaftaran->shouldReceive('with')->with('user')->andReturnSelf();
        $mockPendaftaran->shouldReceive('get')->andReturn(collect([(object)['id'=>1]]));

        $mockJadwal = Mockery::mock('alias:App\\Models\\JadwalSeleksi');
        $mockJadwal->shouldReceive('all')->andReturn(collect([(object)['id'=>1]]));

        $model = Mockery::mock('alias:App\\Models\\PenilaianWawancara');
        $model->id = 1;

        $controller = new PenilaianWawancaraController();
        $response = $controller->edit($model);

        $this->assertEquals('penilaian-wawancara.edit', $response->name());
    }

    /** UPDATE */
    public function test_update_mengubah_penilaian()
    {
        $req = Request::create('/', 'PUT', [
            'pendaftaran_id'=>1,
            'jadwal_seleksi_id'=>10,
            'nilai_komunikasi'=>80,
            'nilai_motivasi'=>85,
            'nilai_kemampuan'=>90,
            'kkm'=>75
        ]);

        // Mock DB connection untuk validator exists
        $mockQuery = Mockery::mock();
        $mockQuery->shouldReceive('useWritePdo')->andReturnSelf();
        $mockQuery->shouldReceive('where')->andReturnSelf();
        $mockQuery->shouldReceive('whereIn')->andReturnSelf();
        $mockQuery->shouldReceive('count')->andReturn(1);

        $mockConnection = Mockery::mock();
        $mockConnection->shouldReceive('table')->andReturn($mockQuery);

        DB::shouldReceive('connection')->andReturn($mockConnection);

        // Mock model
        $mockPW = Mockery::mock('overload:App\\Models\\PenilaianWawancara');
        $mockPW->shouldReceive('update')->once()->andReturn(true);

        $controller = new PenilaianWawancaraController();
        $response = $controller->update($req, $mockPW);

        $this->assertEquals(302, $response->status());
    }

    /** DESTROY */
    public function test_destroy_menghapus_penilaian()
    {
        $model = Mockery::mock('alias:App\\Models\\PenilaianWawancara');
        $model->shouldReceive('delete')->once()->andReturnTrue();

        $controller = new PenilaianWawancaraController();
        $response = $controller->destroy($model);

        $this->assertEquals(302, $response->status());
    }

    /** SHOW */
    public function test_show_menampilkan_detail()
    {
        $mockPW = Mockery::mock('alias:App\\Models\\PenilaianWawancara');
        $mockPW->shouldReceive('with')->with([
            'pendaftaran.user',
            'pendaftaran.dinasPilihan1',
            'pendaftaran.dinasPilihan2',
            'jadwal'
        ])->andReturnSelf();
        $mockPW->shouldReceive('findOrFail')->with(1)->andReturn((object)['id'=>1]);

        $controller = new PenilaianWawancaraController();
        $response = $controller->show(1);

        $this->assertEquals('penilaian-wawancara.show', $response->name());
    }

    /** UPDATE STATUS */
    public function test_updateStatus_berhasil()
    {
        $req = Request::create('/', 'POST', ['kkm'=>75]);

        // Mock item penilaian dengan method update()
        $mockItem = Mockery::mock();
        $mockItem->pendaftaran_id = 1;
        $mockItem->nilai_rata_rata = 80;
        $mockItem->shouldReceive('update')->with(['kkm' => 75])->andReturn(true);

        $mockPW = Mockery::mock('alias:App\\Models\\PenilaianWawancara');
        $mockPW->shouldReceive('with')->with('pendaftaran')->andReturnSelf();
        $mockPW->shouldReceive('get')->andReturn(collect([$mockItem]));

        $mockPendaftaran = Mockery::mock('overload:App\\Models\\Pendaftaran');
        $mockPendaftaran->shouldReceive('where')->with('id', 1)->andReturnSelf();
        $mockPendaftaran->shouldReceive('update')->with(['status_pendaftaran' => 'lulus_wawancara'])->andReturnTrue();

        $controller = new PenilaianWawancaraController();
        $response = $controller->updateStatus($req);

        $this->assertEquals(200, $response->status());
    }
}