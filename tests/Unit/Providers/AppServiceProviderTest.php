<?php

namespace Tests\Unit\Providers;

use App\Providers\AppServiceProvider;
use App\Models\InfoOr;
use Illuminate\Support\Facades\View;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini

class AppServiceProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // --- TEST CASE 1: Menguji View Composer untuk login ---

    /** @test */
    public function view_composer_untuk_login_mengambil_info_or_terbaru()
    {
        // 1. Mocking Model InfoOr
        $infoOrMock = Mockery::mock('alias:App\Models\InfoOr');
        $latestInfoOr = (object)['id' => 1, 'gambar' => 'path/to/img.jpg'];
        
        // 2. Stubbing pemanggilan query InfoOr
        $infoOrMock->shouldReceive('whereNotNull')->once()->with('gambar')->andReturnSelf();
        $infoOrMock->shouldReceive('orderBy')->once()->with('created_at', 'desc')->andReturnSelf();
        $infoOrMock->shouldReceive('first')->once()->andReturn($latestInfoOr);

        // 3. Mocking View::composer() untuk menangkap closure-nya
        View::shouldReceive('composer')
            ->once()
            ->with('auth.login', Mockery::type(\Closure::class))
            ->andReturnUsing(function ($view, $callback) use ($latestInfoOr) {
                
                // 4. Buat mock view
                $viewMock = Mockery::mock('Illuminate\View\View');
                
                // 5. Pastikan metode with() dipanggil dengan data yang benar
                $viewMock->shouldReceive('with')->once()->with('infoOr', $latestInfoOr);
                
                // 6. Eksekusi closure (fungsi composer) untuk menguji logika di dalamnya
                $callback($viewMock);
            });
            
        // 7. Buat instance AppServiceProvider dan panggil boot()
        $provider = new AppServiceProvider(app());
        $provider->boot();

        // Catatan: Verifikasi terjadi di dalam Mockery::andReturnUsing
        $this->assertTrue(true); // Hanya untuk memastikan tes berjalan tanpa assertion error
    }
}