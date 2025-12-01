<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EvaluasiMagangController;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini

class EvaluasiMagangControllerTest extends TestCase
{
    protected function setUp(): void
{
    parent::setUp();

    // Fake DB manager
    \DB::shouldReceive('connection')->andReturnSelf();
    \DB::shouldReceive('raw')->andReturnSelf();
    \DB::shouldReceive('useWritePdo')->andReturnSelf(); // untuk DB::connection()

    // ⭐ FAKE QUERY BUILDER
    $fakeQuery = Mockery::mock();
    $fakeQuery->shouldReceive('where')->andReturnSelf();
    $fakeQuery->shouldReceive('whereIn')->andReturnSelf();
    $fakeQuery->shouldReceive('with')->andReturnSelf();
    $fakeQuery->shouldReceive('count')->andReturn(1);
    $fakeQuery->shouldReceive('exists')->andReturn(true);
    $fakeQuery->shouldReceive('first')->andReturn(null);
    $fakeQuery->shouldReceive('get')->andReturn(collect());
    $fakeQuery->shouldReceive('value')->andReturn(1);

    // ⭐ INI YANG WAJIB DITAMBAHKAN
    $fakeQuery->shouldReceive('useWritePdo')->andReturnSelf();

    // DB::table() → fake query builder
    \DB::shouldReceive('table')->andReturn($fakeQuery);

    // Block method lain
    \DB::shouldReceive('select')->andReturn([]);
    \DB::shouldReceive('insert')->andReturn(true);
    \DB::shouldReceive('update')->andReturn(true);
    \DB::shouldReceive('delete')->andReturn(true);
    \DB::shouldReceive('statement')->andReturn(true);
}


    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function fakeRequest(array $data)
    {
        return Request::create('/', 'POST', $data);
    }

    #[Test]
    public function index_menampilkan_data_pendaftar_dan_penilaian()
    {
        $controller = new EvaluasiMagangController();

        Auth::shouldReceive('user')->andReturn((object)['id' => 1, 'dinas_id' => 10]);

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran')->shouldIgnoreMissing();
        $pendaftaranMock->shouldReceive('where')->andReturnSelf();
        $pendaftaranMock->shouldReceive('whereIn')->andReturnSelf();
        $pendaftaranMock->shouldReceive('with')->andReturnSelf();
        $pendaftaranMock->shouldReceive('get')->andReturn(collect(['dummy_pendaftar']));

        $evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel')->shouldIgnoreMissing();
        $evaluasiMock->shouldReceive('get')->andReturn(collect(['dummy_penilaian']));

        View::shouldReceive('make')->once()->andReturnSelf();
        View::shouldReceive('with')->andReturnSelf();

        $controller->index();

        $this->assertTrue(true);
    }

    #[Test]
    public function store_create_penilaian_baru()
    {
        $controller = new EvaluasiMagangController();

        $request = $this->fakeRequest([
            'pendaftaran_id' => 1,
            'nilai_kedisiplinan' => 80,
            'nilai_kerjasama' => 90,
            'nilai_inisiatif' => 70,
            'nilai_hasil_kerja' => 80,
        ]);

        Auth::shouldReceive('user')->andReturn((object)['id' => 5]);

        $pendaftaranRecord = Mockery::mock();
        $pendaftaranRecord->shouldReceive('update')->once();

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran')->shouldIgnoreMissing();
        $pendaftaranMock->shouldReceive('where')->andReturnSelf();
        $pendaftaranMock->shouldReceive('count')->andReturn(1);
        $pendaftaranMock->shouldReceive('exists')->andReturnTrue();
        $pendaftaranMock->shouldReceive('first')->andReturn($pendaftaranRecord);
        $pendaftaranMock->shouldReceive('find')->andReturn($pendaftaranRecord);
        $pendaftaranMock->shouldReceive('findOrFail')->andReturn($pendaftaranRecord);

        $evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel')->shouldIgnoreMissing();
        $evaluasiMock->shouldReceive('create')->once();

        $response = $controller->storeOrUpdate($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    #[Test]
    public function store_update_penilaian_jika_penilaian_id_ada()
    {
        $controller = new EvaluasiMagangController();

        $request = $this->fakeRequest([
            'pendaftaran_id' => 1,
            'penilaian_id' => 7,
            'nilai_kedisiplinan' => 50,
            'nilai_kerjasama' => 50,
            'nilai_inisiatif' => 50,
            'nilai_hasil_kerja' => 50,
        ]);

        Auth::shouldReceive('user')->andReturn((object)['id' => 10]);

        $pendaftaranRecord = Mockery::mock();
        $pendaftaranRecord->shouldReceive('update')->once();

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran')->shouldIgnoreMissing();
        $pendaftaranMock->shouldReceive('where')->andReturnSelf();
        $pendaftaranMock->shouldReceive('count')->andReturn(1);
        $pendaftaranMock->shouldReceive('exists')->andReturnTrue();
        $pendaftaranMock->shouldReceive('first')->andReturn($pendaftaranRecord);
        $pendaftaranMock->shouldReceive('find')->andReturn($pendaftaranRecord);
        $pendaftaranMock->shouldReceive('findOrFail')->andReturn($pendaftaranRecord);

        $evaRecord = Mockery::mock();
        $evaRecord->shouldReceive('update')->once();

        $evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel')->shouldIgnoreMissing();
        $evaluasiMock->shouldReceive('findOrFail')->with(7)->andReturn($evaRecord);

        $response = $controller->storeOrUpdate($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    #[Test]
    public function store_melempar_validation_exception_bila_data_tidak_valid()
    {
        $controller = new EvaluasiMagangController();

        $this->expectException(ValidationException::class);

        $request = $this->fakeRequest([
            'pendaftaran_id' => null,
            'nilai_kedisiplinan' => 'abc',
        ]);

        $controller->storeOrUpdate($request);
    }

    #[Test]
    public function destroy_menghapus_penilaian()
    {
        $controller = new EvaluasiMagangController();

        $evaluasiRecord = Mockery::mock();
        $evaluasiRecord->shouldReceive('delete')->once();

        $evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel')->shouldIgnoreMissing();
        $evaluasiMock->shouldReceive('findOrFail')->with(99)->andReturn($evaluasiRecord);

        $response = $controller->destroy(99);

        $this->assertEquals(302, $response->getStatusCode());
    }
}