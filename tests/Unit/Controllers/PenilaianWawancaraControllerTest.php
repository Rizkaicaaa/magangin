<?php

namespace Tests\Unit\Http\Controllers;

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

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PenilaianWawancaraController;
use PHPUnit\Framework\Attributes\Test;

class PenilaianWawancaraControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fake DB manager
        DB::shouldReceive('connection')->andReturnSelf();
        DB::shouldReceive('raw')->andReturnSelf();
        DB::shouldReceive('useWritePdo')->andReturnSelf();

        // Fake query builder
        $fakeQuery = Mockery::mock();
        $fakeQuery->shouldReceive('where')->andReturnSelf();
        $fakeQuery->shouldReceive('whereIn')->andReturnSelf();
        $fakeQuery->shouldReceive('with')->andReturnSelf();
        $fakeQuery->shouldReceive('count')->andReturn(1);
        $fakeQuery->shouldReceive('exists')->andReturn(true);
        $fakeQuery->shouldReceive('pluck')->andReturn(collect([1]));
        $fakeQuery->shouldReceive('first')->andReturn(null);
        $fakeQuery->shouldReceive('get')->andReturn(collect(['dummy_penilaian']));
        $fakeQuery->shouldReceive('value')->andReturn(75);
        $fakeQuery->shouldReceive('max')->andReturn(75);
        $fakeQuery->shouldReceive('useWritePdo')->andReturnSelf();

        DB::shouldReceive('table')->andReturn($fakeQuery);
        DB::shouldReceive('select')->andReturn([]);
        DB::shouldReceive('insert')->andReturn(true);
        DB::shouldReceive('update')->andReturn(true);
        DB::shouldReceive('delete')->andReturn(true);
        DB::shouldReceive('statement')->andReturn(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function fakeRequest(array $data): Request
    {
        return Request::create('/', 'POST', $data);
    }

    #[Test]
    public function index_menampilkan_data(): void
    {
        $controller = new PenilaianWawancaraController();

        Auth::shouldReceive('user')->andReturn((object)['id' => 1]);

        $penilaianMock = Mockery::mock('overload:App\Models\PenilaianWawancara')->shouldIgnoreMissing();
        $penilaianMock->shouldReceive('with')->andReturnSelf();
        $penilaianMock->shouldReceive('get')->andReturn(collect(['dummy_penilaian']));

        View::shouldReceive('make')->once()->andReturnSelf();
        View::shouldReceive('with')->andReturnSelf();

        $controller->index();

        $this->assertTrue(true);
    }

    #[Test]
    public function create_menampilkan_form(): void
    {
        $controller = new PenilaianWawancaraController();

        Auth::shouldReceive('user')->andReturn((object)['id' => 1]);

        // Mock Pendaftaran dengan Collection
        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran')->shouldIgnoreMissing();
        $pendaftaranMock->shouldReceive('with')->andReturnSelf();
        $pendaftaranMock->shouldReceive('get')->andReturn(collect([
            (object)['id' => 1, 'nama' => 'Dummy Peserta']
        ]));

        // Mock JadwalSeleksi dengan Collection
        $jadwalMock = Mockery::mock('overload:App\Models\JadwalSeleksi')->shouldIgnoreMissing();
        $jadwalMock->shouldReceive('with')->andReturnSelf();
        $jadwalMock->shouldReceive('get')->andReturn(collect([
            (object)['id' => 1, 'tanggal' => '2024-01-01']
        ]));

        // Mock PenilaianWawancara pluck dengan Collection
        $penilaianMock = Mockery::mock('overload:App\Models\PenilaianWawancara')->shouldIgnoreMissing();
        $penilaianMock->shouldReceive('pluck')->andReturn(collect([1]));

        View::shouldReceive('make')->once()->andReturnSelf();
        View::shouldReceive('with')->andReturnSelf();

        $controller->create();

        $this->assertTrue(true);
    }

    #[Test]
    public function store_menyimpan_penilaian(): void
    {
        $controller = new PenilaianWawancaraController();

        Auth::shouldReceive('user')->andReturn((object)['id' => 1]);

        $penilaianMock = Mockery::mock('overload:App\Models\PenilaianWawancara')->shouldIgnoreMissing();
        $penilaianMock->shouldReceive('where')->andReturnSelf();
        $penilaianMock->shouldReceive('exists')->andReturnFalse();
        $penilaianMock->shouldReceive('create')->once()->andReturn((object)['id' => 1]);

        $request = $this->fakeRequest([
            'pendaftaran_id' => 1,
            'jadwal_seleksi_id' => 1,

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

        $controller->store($request);

        $this->assertTrue(true);
    }

    #[Test]
    public function edit_menampilkan_form_edit(): void
    {
        $controller = new PenilaianWawancaraController();

        // Mock penilaian record dengan properties yang lengkap
        $penilaianRecord = Mockery::mock('stdClass');
        $penilaianRecord->id = 1;
        $penilaianRecord->pendaftaran_id = 1;
        $penilaianRecord->jadwal_seleksi_id = 1;
        $penilaianRecord->nilai_komunikasi = 80;
        $penilaianRecord->nilai_motivasi = 70;
        $penilaianRecord->nilai_kemampuan = 90;
        $penilaianRecord->kkm = 75;
        $penilaianRecord->shouldReceive('getAttribute')->andReturnUsing(function($key) use ($penilaianRecord) {
            return $penilaianRecord->$key ?? null;
        });
        $penilaianRecord->shouldReceive('getAttributes')->andReturn([
            'id' => 1,
            'pendaftaran_id' => 1,
            'jadwal_seleksi_id' => 1,
            'nilai_komunikasi' => 80,
            'nilai_motivasi' => 70,
            'nilai_kemampuan' => 90,
            'kkm' => 75,
        ]);

        $penilaianMock = Mockery::mock('overload:App\Models\PenilaianWawancara')->shouldIgnoreMissing();
        $penilaianMock->shouldReceive('findOrFail')->with(1)->andReturn($penilaianRecord);

        View::shouldReceive('make')->once()->andReturnSelf();
        View::shouldReceive('with')->andReturnSelf();

        $controller->edit($penilaianRecord);

        $this->assertTrue(true);
    }

    #[Test]
    public function update_mengubah_penilaian(): void
    {
        $controller = new PenilaianWawancaraController();

        // Mock penilaian record
        $penilaianRecord = Mockery::mock('stdClass');
        $penilaianRecord->id = 1;
        $penilaianRecord->shouldReceive('update')->once()->andReturn(true);
        $penilaianRecord->shouldReceive('getAttribute')->andReturn(null);
        $penilaianRecord->shouldReceive('setAttribute')->andReturnSelf();

        $penilaianMock = Mockery::mock('overload:App\Models\PenilaianWawancara')->shouldIgnoreMissing();
        $penilaianMock->shouldReceive('findOrFail')->with(1)->andReturn($penilaianRecord);

        $request = $this->fakeRequest([
            'nilai_komunikasi' => 85,
            'nilai_motivasi' => 75,
            'nilai_kemampuan' => 95,
            'kkm' => 80,
        ]);

        $controller->update($request, $penilaianRecord);

        $this->assertTrue(true);
    }

    #[Test]
    public function destroy_menghapus_penilaian(): void
    {
        $controller = new PenilaianWawancaraController();

        // Mock penilaian record
        $penilaianRecord = Mockery::mock('stdClass');
        $penilaianRecord->id = 1;
        $penilaianRecord->shouldReceive('delete')->once()->andReturn(true);

        $penilaianMock = Mockery::mock('overload:App\Models\PenilaianWawancara')->shouldIgnoreMissing();
        $penilaianMock->shouldReceive('findOrFail')->with(1)->andReturn($penilaianRecord);

        $controller->destroy($penilaianRecord);

        $this->assertTrue(true);
    }

    #[Test]
    public function updateStatus_berhasil(): void
    {
        $controller = new PenilaianWawancaraController();

        // Mock pendaftaran record
        $pendaftaranRecord = Mockery::mock('stdClass');
        $pendaftaranRecord->id = 1;
        $pendaftaranRecord->status_pendaftaran = 'pending';
        $pendaftaranRecord->shouldReceive('save')->once()->andReturn(true);
        $pendaftaranRecord->shouldReceive('getAttribute')
            ->with('status_pendaftaran')
            ->andReturnUsing(function() use ($pendaftaranRecord) {
                return $pendaftaranRecord->status_pendaftaran;
            });
        $pendaftaranRecord->shouldReceive('setAttribute')
            ->with('status_pendaftaran', Mockery::any())
            ->andReturnUsing(function ($key, $value) use ($pendaftaranRecord) {
                $pendaftaranRecord->status_pendaftaran = $value;
                return $pendaftaranRecord;
            });

        // Mock penilaian record
        $penilaianRecord = Mockery::mock('stdClass');
        $penilaianRecord->nilai_rata_rata = 80;
        $penilaianRecord->kkm = 75;
        $penilaianRecord->status = 'belum_dinilai';
        $penilaianRecord->pendaftaran_id = 1;
        $penilaianRecord->shouldReceive('save')->once()->andReturn(true);
        $penilaianRecord->shouldReceive('getAttribute')
            ->andReturnUsing(function($key) use ($penilaianRecord) {
                return $penilaianRecord->$key ?? null;
            });
        $penilaianRecord->shouldReceive('setAttribute')
            ->andReturnUsing(function ($key, $value) use ($penilaianRecord) {
                $penilaianRecord->$key = $value;
                return $penilaianRecord;
            });

        // Mock Pendaftaran model
        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran')->shouldIgnoreMissing();
        $pendaftaranMock->shouldReceive('findOrFail')
            ->with(1)
            ->andReturn($pendaftaranRecord);

        // Mock PenilaianWawancara model
        $penilaianMock = Mockery::mock('overload:App\Models\PenilaianWawancara')->shouldIgnoreMissing();
        $penilaianMock->shouldReceive('where')->andReturnSelf();
        $penilaianMock->shouldReceive('get')->andReturn(collect([$penilaianRecord]));

        $request = $this->fakeRequest(['kkm' => 75]);

        $controller->updateStatus($request);

        // Assert bahwa kkm berubah dan status pendaftaran berubah
        $this->assertEquals(75, $penilaianRecord->kkm);
        $this->assertEquals('sudah_dinilai', $penilaianRecord->status);
        $this->assertEquals('lulus_wawancara', $pendaftaranRecord->status_pendaftaran);

    }
}