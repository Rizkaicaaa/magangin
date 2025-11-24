<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\KelulusanWawancaraController;
use App\Models\PenilaianWawancara;

class KelulusanWawancaraControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function index_menampilkan_view_dengan_penilaian()
    {
        // Mock Auth::id()
        Auth::shouldReceive('id')->once()->andReturn(1);

        // Dummy data PenilaianWawancara
        $dummyPenilaian = (object)['id' => 1, 'nilai' => 90];

        // Mock PenilaianWawancara::whereHas()->first()
        $penilaianMock = Mockery::mock('alias:App\Models\PenilaianWawancara');
        $penilaianMock->shouldReceive('whereHas->first')
                      ->once()
                      ->andReturn($dummyPenilaian);

        // Jalankan controller
        $controller = new KelulusanWawancaraController();
        $response = $controller->index();

        // Assert bahwa response adalah view
        $this->assertInstanceOf(View::class, $response);

        // Pastikan view yang dipanggil benar
        $this->assertEquals('kelulusan-wawancara.index', $response->name());

        // Pastikan data dikirim ke view
        $this->assertArrayHasKey('penilaian', $response->getData());
        $this->assertEquals($dummyPenilaian, $response->getData()['penilaian']);
    }
}
