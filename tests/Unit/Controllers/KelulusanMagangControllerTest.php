<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\KelulusanMagangController;
use App\Models\EvaluasiMagang;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini
class KelulusanMagangControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function index_menampilkan_data_kelulusan_magang_hijau()
    {
        // Mock user login
        $userMock = (object)['id' => 1];
        Auth::shouldReceive('user')->once()->andReturn($userMock);

        // Dummy EvaluasiMagang object
        $evaluasiDummy = (object)['id' => 1, 'pendaftaran_id' => 1];

        // Mock EvaluasiMagang::with()->whereHas()->first()
        $evaluasiMock = Mockery::mock('alias:' . EvaluasiMagang::class);
        $evaluasiMock->shouldReceive('with->whereHas->first')
                     ->once()
                     ->andReturn($evaluasiDummy);

        // Jalankan controller
        $controller = new KelulusanMagangController();
        $response = $controller->index();

        // Pastikan response adalah view
        $this->assertInstanceOf(View::class, $response);

        // Pastikan nama view benar
        $this->assertEquals('kelulusan-magang.index', $response->name());

        // Pastikan data evaluasi dikirim ke view
        $this->assertArrayHasKey('evaluasi', $response->getData());
        $this->assertEquals($evaluasiDummy, $response->getData()['evaluasi']);
    }
}
