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

    /** INDEX - Test menampilkan jadwal seleksi wawancara user */
    public function test_index_menampilkan_jadwal_wawancara_user()
    {
        // Mock user yang login
        $mockUser = (object)[
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];

        Auth::shouldReceive('user')->andReturn($mockUser);

        // Set tanggal hari ini untuk testing
        Carbon::setTestNow(Carbon::create(2024, 1, 15));

        // Mock JadwalSeleksi dengan query builder chain
        $mockJadwal = Mockery::mock('alias:App\\Models\\JadwalSeleksi');
        
        // Mock with() method
        $mockJadwal->shouldReceive('with')
            ->with(['infoOr', 'pendaftaran.user'])
            ->andReturnSelf();

        // Mock whereHas() method
        $mockJadwal->shouldReceive('whereHas')
            ->with('pendaftaran', Mockery::on(function ($callback) use ($mockUser) {
                // Buat mock query untuk callback whereHas
                $mockQuery = Mockery::mock();
                $mockQuery->shouldReceive('where')
                    ->with('user_id', $mockUser->id)
                    ->andReturnSelf();
                
                // Jalankan callback
                $callback($mockQuery);
                return true;
            }))
            ->andReturnSelf();

        // Mock whereDate() method - gunakan Mockery::type() untuk Carbon object
        $mockJadwal->shouldReceive('whereDate')
            ->with('tanggal_seleksi', '>=', Mockery::type(Carbon::class))
            ->andReturnSelf();

        // Mock orderBy() method
        $mockJadwal->shouldReceive('orderBy')
            ->with('tanggal_seleksi', 'asc')
            ->andReturnSelf();

        // Mock get() method - return dummy data sebagai Collection
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

        // Instantiate controller dan panggil method index
        $controller = new SeleksiWawancaraController();
        $response = $controller->index();

        // Assertions
        $this->assertEquals('seleksi-wawancara.index', $response->name());
        $this->assertArrayHasKey('jadwals', $response->getData());

        // Reset Carbon testing
        Carbon::setTestNow();
    }

    /** INDEX - Test menampilkan empty jadwal jika user tidak punya jadwal */
    public function test_index_menampilkan_empty_jika_tidak_ada_jadwal()
    {
        // Mock user yang login
        $mockUser = (object)[
            'id' => 99,
            'name' => 'User Tanpa Jadwal',
            'email' => 'nojadwal@example.com'
        ];

        Auth::shouldReceive('user')->andReturn($mockUser);

        // Set tanggal hari ini
        Carbon::setTestNow(Carbon::create(2024, 1, 15));

        // Mock JadwalSeleksi
        $mockJadwal = Mockery::mock('alias:App\\Models\\JadwalSeleksi');
        
        $mockJadwal->shouldReceive('with')
            ->with(['infoOr', 'pendaftaran.user'])
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereHas')
            ->with('pendaftaran', Mockery::any())
            ->andReturnSelf();

        // Gunakan Mockery::type() untuk Carbon object
        $mockJadwal->shouldReceive('whereDate')
            ->with('tanggal_seleksi', '>=', Mockery::type(Carbon::class))
            ->andReturnSelf();

        $mockJadwal->shouldReceive('orderBy')
            ->with('tanggal_seleksi', 'asc')
            ->andReturnSelf();

        // Return empty collection
        $mockJadwal->shouldReceive('get')->andReturn(collect([]));

        $controller = new SeleksiWawancaraController();
        $response = $controller->index();

        // Assertions
        $this->assertEquals('seleksi-wawancara.index', $response->name());
        $jadwals = $response->getData()['jadwals'];
        $this->assertCount(0, $jadwals);

        // Reset Carbon
        Carbon::setTestNow();
    }

    /** INDEX - Test hanya menampilkan jadwal >= hari ini */
    public function test_index_hanya_menampilkan_jadwal_hari_ini_dan_masa_depan()
    {
        // Mock user
        $mockUser = (object)['id' => 1];
        Auth::shouldReceive('user')->andReturn($mockUser);

        // Set tanggal testing ke 2024-01-20
        Carbon::setTestNow(Carbon::create(2024, 1, 20));

        $mockJadwal = Mockery::mock('alias:App\\Models\\JadwalSeleksi');
        
        $mockJadwal->shouldReceive('with')
            ->with(['infoOr', 'pendaftaran.user'])
            ->andReturnSelf();

        $mockJadwal->shouldReceive('whereHas')
            ->with('pendaftaran', Mockery::any())
            ->andReturnSelf();

        // Gunakan Mockery::type() untuk Carbon object
        $mockJadwal->shouldReceive('whereDate')
            ->with('tanggal_seleksi', '>=', Mockery::type(Carbon::class))
            ->andReturnSelf();

        $mockJadwal->shouldReceive('orderBy')
            ->with('tanggal_seleksi', 'asc')
            ->andReturnSelf();

        // Return jadwal yang >= 2024-01-20 sebagai Collection
        $mockJadwal->shouldReceive('get')
            ->andReturn(collect([
                (object)[
                    'id' => 1,
                    'tanggal_seleksi' => '2024-01-20', // Hari ini
                ],
                (object)[
                    'id' => 2,
                    'tanggal_seleksi' => '2024-01-25', // Masa depan
                ]
                // Jadwal dengan tanggal < 2024-01-20 tidak muncul
            ]));

        $controller = new SeleksiWawancaraController();
        $response = $controller->index();

        $this->assertEquals('seleksi-wawancara.index', $response->name());
        
        // Reset Carbon
        Carbon::setTestNow();
    }

    /** INDEX - Test jadwal diurutkan berdasarkan tanggal ascending */
    public function test_index_jadwal_diurutkan_berdasarkan_tanggal_asc()
    {
        // Mock user
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

        // Gunakan Mockery::type() untuk Carbon object
        $mockJadwal->shouldReceive('whereDate')
            ->with('tanggal_seleksi', '>=', Mockery::type(Carbon::class))
            ->andReturnSelf();

        // Pastikan orderBy dipanggil dengan parameter yang benar
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

        // Reset Carbon
        Carbon::setTestNow();
    }
}