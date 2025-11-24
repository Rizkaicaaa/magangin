<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\KelulusanWawancaraController;

class PengumumanMagangControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function index_hijau()
    {
        $controller = Mockery::mock(KelulusanWawancaraController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Mock method protected untuk data wawancara
        $controller->shouldReceive('getHasilWawancara')->once()->andReturn(['wawancara1']);

        $response = $controller->index();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('kelulusan-wawancara.index', $response->name());
        $this->assertArrayHasKey('hasilWawancara', $response->getData());
    }

    /** @test */
    public function store_hijau()
    {
        $request = Request::create('/kelulusan-wawancara/1', 'POST', [
            'status' => 'Lulus',
            'catatan' => 'Sangat baik'
        ]);

        Storage::fake('public');

        // Mock PDF supaya tidak bikin file nyata
        $pdfMock = Mockery::mock();
        $pdfMock->shouldReceive('setPaper')->andReturnSelf();
        $pdfMock->shouldReceive('setOptions')->andReturnSelf();
        $pdfMock->shouldReceive('output')->andReturn('PDF_BINARY');

        Pdf::shouldReceive('loadHTML')->once()->andReturn($pdfMock);

        $controller = Mockery::mock(KelulusanWawancaraController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Mock protected method supaya store tidak akses database
        $controller->shouldReceive('findWawancaraById')->andReturn(
            (object)[
                'id' => 1,
                'peserta' => (object)[
                    'nama_lengkap' => 'Intan Salma',
                    'update' => true
                ],
                'update' => true
            ]
        );

        $response = $controller->store($request, 1);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }
}
