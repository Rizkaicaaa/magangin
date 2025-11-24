<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    protected $controller;
    protected $dinasMock;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new AuthenticatedSessionController();
        // Mocking Model Dinas
        $this->dinasMock = Mockery::mock('alias:App\Models\Dinas');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // --- TEST CASE 1: Menguji Metode create() ---
    
    /** @test */
    public function create_menampilkan_view_login_dengan_semua_data_dinas()
    {
        // 1. Stubbing data dinas
        $allDinas = collect([
            (object)['id' => 1, 'nama' => 'Dinas A'],
            (object)['id' => 2, 'nama' => 'Dinas B'],
        ]);

        // 2. Mocking pemanggilan Model Dinas::all()
        $this->dinasMock->shouldReceive('all')->once()->andReturn($allDinas);

        // 3. Panggil metode yang diuji
        $response = $this->controller->create();

        // 4. Verifikasi View dan data yang dilewatkan
        $this->assertEquals('auth.login', $response->getName());
        $this->assertEquals($allDinas, $response->getData()['allDinas']);
    }

    // --- TEST CASE 2: Menguji Metode store() ---
    
    /** @test */
    public function store_berhasil_mengotentikasi_dan_redirect_ke_dashboard()
    {
        // 1. Mocking LoginRequest (dianggap sebagai Request)
        $request = Mockery::mock('App\Http\Requests\Auth\LoginRequest');

        // 2. Mocking pemanggilan $request->authenticate()
        $request->shouldReceive('authenticate')->once();
        
        // 3. Mocking Session
        $sessionMock = Mockery::mock('Illuminate\Session\Store');
        $sessionMock->shouldReceive('regenerate')->once();
        $request->shouldReceive('session')->andReturn($sessionMock);
        
        // 4. Mocking fungsi global redirect() untuk verifikasi rute
        $redirectMock = Mockery::mock('Illuminate\Routing\Redirector');
        $redirectMock->shouldReceive('intended')
            ->once()
            ->with(route('dashboard', absolute: false))
            ->andReturn(Mockery::mock('Illuminate\Http\RedirectResponse'));
        
        // Stub fungsi global redirect() agar mengembalikan mock kita
        $this->app->instance('redirect', $redirectMock); 
        
        // 5. Panggil metode yang diuji
        $response = $this->controller->store($request);
        
        // 6. Verifikasi
        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
    }
    
    // --- TEST CASE 3: Menguji Metode destroy() ---

    /** @test */
    public function destroy_berhasil_logout_dan_redirect_ke_login()
    {
        // 1. Mocking Request
        $request = Mockery::mock(Request::class);
        
        // 2. Mocking Auth Facade: logout()
        Auth::shouldReceive('guard')->once()->with('web')->andReturnSelf();
        Auth::shouldReceive('logout')->once();
        
        // 3. Mocking Session: invalidate() dan regenerateToken()
        $sessionMock = Mockery::mock('Illuminate\Session\Store');
        $sessionMock->shouldReceive('invalidate')->once();
        $sessionMock->shouldReceive('regenerateToken')->once();
        $request->shouldReceive('session')->andReturn($sessionMock);
        
        // 4. Panggil metode yang diuji
        $response = $this->controller->destroy($request);

        // 5. Verifikasi
        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertEquals(url('/login'), $response->getTargetUrl(), 'Seharusnya me-redirect ke /login');
    }
}