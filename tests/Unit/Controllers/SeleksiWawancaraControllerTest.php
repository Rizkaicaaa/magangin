<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SeleksiWawancaraController;
use Carbon\Carbon;

class SeleksiWawancaraControllerTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_menampilkan_jadwal_wawancara_user()
    {
        $mockUser = (object)[
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];

        Auth::shouldReceive('user')->andReturn($mockUser);

        Carbon::setTestNow(Carbon::create(2024, 1, 15));

        $mockJadwal = Mockery::mock('alias:App\\Models\\JadwalSeleksi');
        
        $mockJadwal->shouldReceive('with')
            ->with(['infoOr', 'pendaftaran.user'])
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereHas')
            ->with('pendaftaran', Mockery::on(function ($callback) use ($mockUser) {
                $mockQuery = Mockery::mock();
                $mockQuery->shouldReceive('where')
                    ->with('user_id', $mockUser->id)
                    ->andReturnSelf();
                
                $callback($mockQuery);
                return true;
            }))
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereDate')
            ->with('tanggal_seleksi', '>=', Mockery::type(Carbon::class))
            ->andReturnSelf();

        $mockJadwal->shouldReceive('orderBy')
            ->with('tanggal_seleksi', 'asc')
            ->andReturnSelf();

        $mockJadwal->shouldReceive('get')
            ->andReturn(collect([
                (object)[
                    'id' => 1,
                    'tanggal_seleksi' => '2024-01-20',
                    'waktu_mulai' => '09:00:00',
                    'waktu_selesai' => '11:00:00'
                ],
                (object)[
                    'id' => 2,
                    'tanggal_seleksi' => '2024-01-25',
                    'waktu_mulai' => '13:00:00',
                    'waktu_selesai' => '15:00:00'
                ]
            ]));

        $controller = new SeleksiWawancaraController();
        $response = $controller->index();

        $this->assertEquals('seleksi-wawancara.index', $response->name());
        $this->assertArrayHasKey('jadwals', $response->getData());

        Carbon::setTestNow();
    }

    public function test_index_menampilkan_empty_jika_tidak_ada_jadwal()
    {
        $mockUser = (object)[
            'id' => 99,
            'name' => 'User Tanpa Jadwal',
            'email' => 'nojadwal@example.com'
        ];

        Auth::shouldReceive('user')->andReturn($mockUser);

        Carbon::setTestNow(Carbon::create(2024, 1, 15));

        $mockJadwal = Mockery::mock('alias:App\\Models\\JadwalSeleksi');
        
        $mockJadwal->shouldReceive('with')
            ->with(['infoOr', 'pendaftaran.user'])
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereHas')
            ->with('pendaftaran', Mockery::any())
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereDate')
            ->with('tanggal_seleksi', '>=', Mockery::type(Carbon::class))
            ->andReturnSelf();

        $mockJadwal->shouldReceive('orderBy')
            ->with('tanggal_seleksi', 'asc')
            ->andReturnSelf();

        $mockJadwal->shouldReceive('get')->andReturn(collect([]));

        $controller = new SeleksiWawancaraController();
        $response = $controller->index();

        $this->assertEquals('seleksi-wawancara.index', $response->name());
        $jadwals = $response->getData()['jadwals'];
        $this->assertCount(0, $jadwals);

        Carbon::setTestNow();
    }

    public function test_index_hanya_menampilkan_jadwal_hari_ini_dan_masa_depan()
    {
        $mockUser = (object)['id' => 1];
        Auth::shouldReceive('user')->andReturn($mockUser);

        Carbon::setTestNow(Carbon::create(2024, 1, 20));

        $mockJadwal = Mockery::mock('alias:App\\Models\\JadwalSeleksi');
        
        $mockJadwal->shouldReceive('with')
            ->with(['infoOr', 'pendaftaran.user'])
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereHas')
            ->with('pendaftaran', Mockery::any())
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereDate')
            ->with('tanggal_seleksi', '>=', Mockery::type(Carbon::class))
            ->andReturnSelf();

        $mockJadwal->shouldReceive('orderBy')
            ->with('tanggal_seleksi', 'asc')
            ->andReturnSelf();

        $mockJadwal->shouldReceive('get')
            ->andReturn(collect([
                (object)[
                    'id' => 1,
                    'tanggal_seleksi' => '2024-01-20', 
                ],
                (object)[
                    'id' => 2,
                    'tanggal_seleksi' => '2024-01-25', 
                ]
            ]));

        $controller = new SeleksiWawancaraController();
        $response = $controller->index();

        $this->assertEquals('seleksi-wawancara.index', $response->name());
        
        Carbon::setTestNow();
    }

    public function test_index_jadwal_diurutkan_berdasarkan_tanggal_asc()
    {
        $mockUser = (object)['id' => 1];
        Auth::shouldReceive('user')->andReturn($mockUser);

        Carbon::setTestNow(Carbon::create(2024, 1, 15));

        $mockJadwal = Mockery::mock('alias:App\\Models\\JadwalSeleksi');
        
        $mockJadwal->shouldReceive('with')
            ->with(['infoOr', 'pendaftaran.user'])
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereHas')
            ->with('pendaftaran', Mockery::any())
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereDate')
            ->with('tanggal_seleksi', '>=', Mockery::type(Carbon::class))
            ->andReturnSelf();

        $mockJadwal->shouldReceive('orderBy')
            ->once()
            ->with('tanggal_seleksi', 'asc')
            ->andReturnSelf();

        $mockJadwal->shouldReceive('get')
            ->andReturn(collect([
                (object)['id' => 1, 'tanggal_seleksi' => '2024-01-16'],
                (object)['id' => 2, 'tanggal_seleksi' => '2024-01-20'],
                (object)['id' => 3, 'tanggal_seleksi' => '2024-01-18'],
            ]));

        $controller = new SeleksiWawancaraController();
        $response = $controller->index();

        $this->assertEquals('seleksi-wawancara.index', $response->name());

        Carbon::setTestNow();
    }
}