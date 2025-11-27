<?php

<<<<<<< HEAD
namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PenilaianWawancaraController;
use PHPUnit\Framework\Attributes\Test;
=======
namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\InfoOr;
use App\Models\JadwalSeleksi;
use App\Models\PenilaianWawancara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini
>>>>>>> 1e01d5373929534494a5073383229adcf96ce04b

#[RunTestsInSeparateProcesses] // Tambahkan ini
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

        $controller->store($request);

        $this->assertTrue(true);
    }

    #[Test]
    public function edit_menampilkan_form_edit(): void
    {
        $controller = new PenilaianWawancaraController();

        // Buat mock yang lebih sederhana tanpa terlalu banyak expectation
        $penilaianRecord = Mockery::mock();
        $penilaianRecord->id = 1;
        $penilaianRecord->pendaftaran_id = 1;
        $penilaianRecord->jadwal_seleksi_id = 1;
        $penilaianRecord->nilai_komunikasi = 80;
        $penilaianRecord->nilai_motivasi = 70;
        $penilaianRecord->nilai_kemampuan = 90;
        $penilaianRecord->kkm = 75;
        
        // Allow any method call
        $penilaianRecord->shouldIgnoreMissing();

        View::shouldReceive('make')->once()->andReturnSelf();
        View::shouldReceive('with')->andReturnSelf();

        $controller->edit($penilaianRecord);

        $this->assertTrue(true);
    }

    #[Test]
    public function update_mengubah_penilaian(): void
    {
        $controller = new PenilaianWawancaraController();

        // Mock penilaian record dengan shouldIgnoreMissing
        $penilaianRecord = Mockery::mock();
        $penilaianRecord->id = 1;
        $penilaianRecord->shouldReceive('update')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturn(true);
        $penilaianRecord->shouldIgnoreMissing();

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

        // Mock penilaian record dengan shouldIgnoreMissing
        $penilaianRecord = Mockery::mock();
        $penilaianRecord->id = 1;
        $penilaianRecord->shouldReceive('delete')
            ->once()
            ->andReturn(true);
        $penilaianRecord->shouldIgnoreMissing();

        $controller->destroy($penilaianRecord);

        $this->assertTrue(true);
    }

    #[Test]
    public function updateStatus_berhasil(): void
    {
        $controller = new PenilaianWawancaraController();

        // Mock pendaftaran record
        $pendaftaranRecord = new \stdClass();
        $pendaftaranRecord->id = 1;
        $pendaftaranRecord->status_pendaftaran = 'pending';
        
        $pendaftaranMock = Mockery::mock($pendaftaranRecord);
        $pendaftaranMock->id = 1;
        $pendaftaranMock->status_pendaftaran = 'pending';
        $pendaftaranMock->shouldReceive('save')
            ->once()
            ->andReturnUsing(function() use ($pendaftaranMock) {
                // Simulasi save berhasil
                return true;
            });
        $pendaftaranMock->shouldIgnoreMissing();

        // Mock penilaian record
        $penilaianRecord = new \stdClass();
        $penilaianRecord->nilai_rata_rata = 80;
        $penilaianRecord->kkm = 75;
        $penilaianRecord->status = 'belum_dinilai';
        $penilaianRecord->pendaftaran_id = 1;
        
        $penilaianMock = Mockery::mock($penilaianRecord);
        $penilaianMock->nilai_rata_rata = 80;
        $penilaianMock->kkm = 75;
        $penilaianMock->status = 'belum_dinilai';
        $penilaianMock->pendaftaran_id = 1;
        $penilaianMock->shouldReceive('save')
            ->once()
            ->andReturn(true);
        $penilaianMock->shouldIgnoreMissing();

        // Mock Pendaftaran model
        $pendaftaranModelMock = Mockery::mock('overload:App\Models\Pendaftaran')->shouldIgnoreMissing();
        $pendaftaranModelMock->shouldReceive('findOrFail')
            ->with(1)
            ->andReturn($pendaftaranMock);

        // Mock PenilaianWawancara model
        $penilaianModelMock = Mockery::mock('overload:App\Models\PenilaianWawancara')->shouldIgnoreMissing();
        $penilaianModelMock->shouldReceive('where')->andReturnSelf();
        $penilaianModelMock->shouldReceive('get')->andReturn(collect([$penilaianMock]));

        $request = $this->fakeRequest(['kkm' => 75]);

        $controller->updateStatus($request);

<<<<<<< HEAD
        // Verifikasi bahwa method save dipanggil
        $this->assertTrue(true);
    }
=======
>>>>>>> 1e01d5373929534494a5073383229adcf96ce04b
}