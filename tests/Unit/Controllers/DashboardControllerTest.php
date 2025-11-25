<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini

class DashboardControllerTest extends TestCase
{
    protected $controller;
    protected $mockInfoOrModel;
    protected $mockPendaftaranModel;
    protected $mockJadwalKegiatanModel;
    protected $mockDinasModel;
    protected $mockUserModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Inisialisasi controller
        $this->controller = new DashboardController();
        
        // Mock semua model sebagai class alias
        $this->mockInfoOrModel = Mockery::mock('alias:App\Models\InfoOr');
        $this->mockPendaftaranModel = Mockery::mock('alias:App\Models\Pendaftaran');
        $this->mockJadwalKegiatanModel = Mockery::mock('alias:App\Models\JadwalKegiatan');
        $this->mockDinasModel = Mockery::mock('alias:App\Models\Dinas');
        $this->mockUserModel = Mockery::mock('alias:App\Models\User');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper untuk mock authenticated user
     */
    private function mockAuthUser($role = 'mahasiswa', $id = 1, $dinasId = null)
    {
        $user = Mockery::mock();
        $user->shouldReceive('getAttribute')->with('id')->andReturn($id);
        $user->shouldReceive('getAttribute')->with('role')->andReturn($role);
        $user->shouldReceive('getAttribute')->with('dinas_id')->andReturn($dinasId);
        $user->id = $id;
        $user->role = $role;
        $user->dinas_id = $dinasId;
        
        Auth::shouldReceive('user')->andReturn($user);
        
        return $user;
    }

    /**
     * Helper untuk mock collection InfoOr
     */
    private function mockInfoOrCollection($count = 3)
    {
        $items = [];
        for ($i = 1; $i <= $count; $i++) {
            $item = Mockery::mock();
            $item->shouldReceive('getAttribute')->with('id')->andReturn($i);
            $item->shouldReceive('getAttribute')->with('judul')->andReturn("Info OR $i");
            $item->shouldReceive('getAttribute')->with('periode')->andReturn("2024/2025");
            $item->id = $i;
            $item->judul = "Info OR $i";
            $item->periode = "2024/2025";
            $items[] = $item;
        }
        
        return collect($items);
    }

    /**
     * Helper untuk mock collection Pendaftaran
     */
    private function mockPendaftaranCollection($count = 5)
    {
        $items = [];
        for ($i = 1; $i <= $count; $i++) {
            $item = Mockery::mock();
            $item->shouldReceive('getAttribute')->with('id')->andReturn($i);
            $item->shouldReceive('getAttribute')->with('user_id')->andReturn($i);
            $item->shouldReceive('getAttribute')->with('status_pendaftaran')->andReturn('terdaftar');
            $item->id = $i;
            $item->user_id = $i;
            $item->status_pendaftaran = 'terdaftar';
            
            // Mock relations
            $item->user = Mockery::mock();
            $item->infoOr = Mockery::mock();
            $item->dinasPilihan1 = Mockery::mock();
            $item->dinasPilihan2 = Mockery::mock();
            $item->dinasDiterima = Mockery::mock();
            
            $items[] = $item;
        }
        
        return collect($items);
    }

    /**
     * Helper untuk mock collection JadwalKegiatan
     */
    private function mockJadwalKegiatanCollection($count = 3)
    {
        $items = [];
        for ($i = 1; $i <= $count; $i++) {
            $item = Mockery::mock();
            $item->shouldReceive('getAttribute')->with('id')->andReturn($i);
            $item->shouldReceive('getAttribute')->with('nama_kegiatan')->andReturn("Kegiatan $i");
            $item->id = $i;
            $item->nama_kegiatan = "Kegiatan $i";
            $item->tanggal_kegiatan = Carbon::now()->addDays($i);
            
            // Mock relation
            $item->infoOr = Mockery::mock();
            
            $items[] = $item;
        }
        
        return collect($items);
    }

    /** @test */
    public function index_mengembalikan_view_dashboard()
    {
        // Arrange
        $user = $this->mockAuthUser('mahasiswa', 1);
        
        $request = Request::create('/dashboard', 'GET');
        
        $pendaftaranCollection = $this->mockPendaftaranCollection(2);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($pendaftaranCollection);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('whereIn')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('dashboard', $response->getName());
        $this->assertArrayHasKey('user', $response->getData());
        $this->assertEquals('mahasiswa', $response->getData()['user']->role);
    }

    /** @test */
    public function index_superadmin_dengan_filter_all_menampilkan_semua_data()
    {
        // Arrange
        $user = $this->mockAuthUser('superadmin', 1);
        
        $request = Request::create('/dashboard', 'GET', ['info_or_id' => 'all']);
        
        $infoOrCollection = $this->mockInfoOrCollection(3);
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection);
        
        $this->mockPendaftaranModel
            ->shouldReceive('count')->andReturn(50)
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockPendaftaranCollection(10));
        
        $this->mockDinasModel
            ->shouldReceive('count')->andReturn(10);
        
        $this->mockInfoOrModel
            ->shouldReceive('count')->andReturn(5);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('count')->andReturn(20)
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockJadwalKegiatanCollection(8));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $viewData = $response->getData();
        
        $this->assertEquals('all', $viewData['selectedInfoOr']);
        $this->assertTrue($viewData['showFilter']);
        $this->assertTrue($viewData['canManage']);
        $this->assertEquals(50, $viewData['totalPendaftar']);
        $this->assertEquals(10, $viewData['totalDinas']);
        $this->assertEquals(5, $viewData['totalInfo']);
        $this->assertEquals(20, $viewData['totalKegiatan']);
    }

    /** @test */
    public function index_superadmin_dengan_filter_periode()
    {
        // Arrange
        $user = $this->mockAuthUser('superadmin', 1);
        
        $request = Request::create('/dashboard', 'GET', ['info_or_id' => '1']);
        
        $infoOrCollection = $this->mockInfoOrCollection(3);
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection);
        
        $selectedInfoOr = Mockery::mock();
        $selectedInfoOr->id = 1;
        $selectedInfoOr->judul = 'Info OR 2024';
        
        $this->mockInfoOrModel
            ->shouldReceive('find')->with('1')->andReturn($selectedInfoOr);
        
        // Mock untuk count total pendaftar
        $this->mockPendaftaranModel
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('count')->andReturn(25);
        
        // Mock untuk pendaftar terbaru
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockPendaftaranCollection(10));
        
        // Mock untuk additional stats - setiap status
        $this->mockPendaftaranModel
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'terdaftar')->andReturnSelf()
            ->shouldReceive('count')->andReturn(10);
        
        $this->mockPendaftaranModel
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(8);
        
        $this->mockPendaftaranModel
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(2);
        
        $this->mockPendaftaranModel
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(5);
        
        $this->mockPendaftaranModel
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        $this->mockDinasModel
            ->shouldReceive('count')->andReturn(10);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('count')->andReturn(5)
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockJadwalKegiatanCollection(5));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertEquals('1', $viewData['selectedInfoOr']);
        $this->assertNotNull($viewData['selectedInfoOrData']);
        $this->assertEquals(25, $viewData['totalPendaftar']);
        $this->assertEquals(5, $viewData['totalKegiatan']);
        $this->assertArrayHasKey('additionalStats', $viewData);
        $this->assertArrayHasKey('terdaftar', $viewData['additionalStats']);
        $this->assertArrayHasKey('pendaftar_lulus_wawancara', $viewData['additionalStats']);
        $this->assertArrayHasKey('pendaftar_ditolak', $viewData['additionalStats']);
    }

    /** @test */
    public function index_admin_tanpa_dinas_id_mengembalikan_data_kosong()
    {
        // Arrange
        $user = $this->mockAuthUser('admin', 2, null); // tanpa dinas_id
        
        $request = Request::create('/dashboard', 'GET');

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertEquals(0, $viewData['totalPendaftar']);
        $this->assertEquals(0, $viewData['totalDinas']);
        $this->assertEquals(0, $viewData['totalInfo']);
        $this->assertEquals(0, $viewData['totalKegiatan']);
        $this->assertCount(0, $viewData['pendaftarTerbaru']);
        $this->assertCount(0, $viewData['kegiatanTerdekat']);
        $this->assertFalse($viewData['showFilter']);
    }

    /** @test */
    public function index_admin_dengan_dinas_id_menampilkan_data_dinas_mereka()
    {
        // Arrange
        $user = $this->mockAuthUser('admin', 2, 5);
        
        $request = Request::create('/dashboard', 'GET', ['info_or_id' => 'all']);
        
        $infoOrCollection = $this->mockInfoOrCollection(2);
        
        // Mock whereHas untuk InfoOr
        $this->mockInfoOrModel
            ->shouldReceive('whereHas')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection);
        
        // Mock baseQuery untuk total count
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('count')->andReturn(15);
        
        // Mock untuk pendaftar terbaru
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->with('created_at', 'desc')->andReturnSelf()
            ->shouldReceive('limit')->with(10)->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockPendaftaranCollection(10));
        
        // Mock untuk additionalStats - masing-masing status dengan clone
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'terdaftar')->andReturnSelf()
            ->shouldReceive('count')->andReturn(5);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(3);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(2);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(4);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(1);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('whereIn')->andReturnSelf()
            ->shouldReceive('count')->andReturn(8)
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockJadwalKegiatanCollection(8));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertEquals('all', $viewData['selectedInfoOr']);
        $this->assertEquals(15, $viewData['totalPendaftar']);
        $this->assertEquals(1, $viewData['totalDinas']);
        $this->assertEquals(2, $viewData['totalInfo']);
        $this->assertTrue($viewData['showFilter']);
    }

    /** @test */
    public function index_admin_dengan_filter_periode_spesifik()
    {
        // Arrange
        $user = $this->mockAuthUser('admin', 2, 5);
        
        $request = Request::create('/dashboard', 'GET', ['info_or_id' => '1']);
        
        $infoOrCollection = $this->mockInfoOrCollection(2);
        
        $this->mockInfoOrModel
            ->shouldReceive('whereHas')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection)
            ->shouldReceive('find')->with('1')->andReturn($infoOrCollection->first());
        
        // Mock baseQuery untuk total count
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('count')->andReturn(10);
        
        // Mock untuk pendaftar terbaru
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('orderBy')->with('created_at', 'desc')->andReturnSelf()
            ->shouldReceive('limit')->with(10)->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockPendaftaranCollection(10));
        
        // Mock untuk additionalStats
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'terdaftar')->andReturnSelf()
            ->shouldReceive('count')->andReturn(4);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(3);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(1);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(2);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('whereIn')->andReturnSelf()
            ->shouldReceive('count')->andReturn(5)
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockJadwalKegiatanCollection(5));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertEquals('1', $viewData['selectedInfoOr']);
        $this->assertNotNull($viewData['selectedInfoOrData']);
        $this->assertEquals(10, $viewData['totalPendaftar']);
    }

    /** @test */
    public function index_admin_menampilkan_statistik_tambahan()
    {
        // Arrange
        $user = $this->mockAuthUser('admin', 2, 5);
        
        $request = Request::create('/dashboard', 'GET', ['info_or_id' => '1']);
        
        $infoOrCollection = $this->mockInfoOrCollection(1);
        
        $this->mockInfoOrModel
            ->shouldReceive('whereHas')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection)
            ->shouldReceive('find')->with('1')->andReturn($infoOrCollection->first());
        
        // Mock untuk total count
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('count')->andReturn(12);
        
        // Mock untuk pendaftar terbaru
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('orderBy')->with('created_at', 'desc')->andReturnSelf()
            ->shouldReceive('limit')->with(10)->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));
        
        // Mock untuk additionalStats - terdaftar
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'terdaftar')->andReturnSelf()
            ->shouldReceive('count')->andReturn(6);
        
        // Mock untuk lulus_wawancara
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(3);
        
        // Mock untuk tidak_lulus_wawancara
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(2);
        
        // Mock untuk lulus_magang
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(1);
        
        // Mock untuk tidak_lulus_magang
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('whereIn')->andReturnSelf()
            ->shouldReceive('count')->andReturn(4)
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertArrayHasKey('additionalStats', $viewData);
        $this->assertArrayHasKey('terdaftar', $viewData['additionalStats']);
        $this->assertArrayHasKey('pendaftar_lulus_wawancara', $viewData['additionalStats']);
        $this->assertArrayHasKey('pendaftar_ditolak', $viewData['additionalStats']);
        $this->assertArrayHasKey('pendaftar_lulus_magang', $viewData['additionalStats']);
        $this->assertArrayHasKey('pendaftar_tidak_lulus_magang', $viewData['additionalStats']);
    }

    /** @test */
    public function index_mahasiswa_menampilkan_pendaftaran_dan_kegiatan_mereka()
    {
        // Arrange
        $user = $this->mockAuthUser('mahasiswa', 3);
        
        $request = Request::create('/dashboard', 'GET');
        
        $pendaftaran1 = Mockery::mock();
        $pendaftaran1->shouldReceive('getAttribute')->with('status_pendaftaran')->andReturn('lulus_wawancara');
        $pendaftaran1->shouldReceive('getAttribute')->with('info_or_id')->andReturn(1);
        $pendaftaran1->status_pendaftaran = 'lulus_wawancara';
        $pendaftaran1->info_or_id = 1;
        $pendaftaran1->infoOr = Mockery::mock();
        $pendaftaran1->dinasPilihan1 = Mockery::mock();
        $pendaftaran1->dinasPilihan2 = Mockery::mock();
        $pendaftaran1->dinasDiterima = Mockery::mock();
        
        $pendaftaran2 = Mockery::mock();
        $pendaftaran2->shouldReceive('getAttribute')->with('status_pendaftaran')->andReturn('terdaftar');
        $pendaftaran2->shouldReceive('getAttribute')->with('info_or_id')->andReturn(2);
        $pendaftaran2->status_pendaftaran = 'terdaftar';
        $pendaftaran2->info_or_id = 2;
        $pendaftaran2->infoOr = Mockery::mock();
        $pendaftaran2->dinasPilihan1 = Mockery::mock();
        $pendaftaran2->dinasPilihan2 = Mockery::mock();
        $pendaftaran2->dinasDiterima = Mockery::mock();
        
        $pendaftaranCollection = collect([$pendaftaran1, $pendaftaran2]);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->with('user_id', 3)->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($pendaftaranCollection);
        
        $kegiatanCollection = $this->mockJadwalKegiatanCollection(3);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('whereIn')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($kegiatanCollection);

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertEquals('mahasiswa', $viewData['user']->role);
        $this->assertEquals(3, $viewData['totalKegiatan']);
        $this->assertCount(2, $viewData['pendaftaranUser']);
        $this->assertCount(3, $viewData['kegiatanTerdekat']);
        $this->assertEquals('all', $viewData['selectedInfoOr']);
        $this->assertFalse($viewData['showFilter']);
    }

    /** @test */
    public function index_mahasiswa_hanya_menampilkan_kegiatan_dari_periode_lulus_wawancara()
    {
        // Arrange
        $user = $this->mockAuthUser('mahasiswa', 3);
        
        $request = Request::create('/dashboard', 'GET');
        
        $pendaftaran1 = Mockery::mock();
        $pendaftaran1->shouldReceive('getAttribute')->with('status_pendaftaran')->andReturn('lulus_wawancara');
        $pendaftaran1->shouldReceive('getAttribute')->with('info_or_id')->andReturn(1);
        $pendaftaran1->status_pendaftaran = 'lulus_wawancara';
        $pendaftaran1->info_or_id = 1;
        $pendaftaran1->infoOr = Mockery::mock();
        $pendaftaran1->dinasPilihan1 = Mockery::mock();
        $pendaftaran1->dinasPilihan2 = Mockery::mock();
        $pendaftaran1->dinasDiterima = Mockery::mock();
        
        $pendaftaran2 = Mockery::mock();
        $pendaftaran2->shouldReceive('getAttribute')->with('status_pendaftaran')->andReturn('tidak_lulus_wawancara');
        $pendaftaran2->shouldReceive('getAttribute')->with('info_or_id')->andReturn(2);
        $pendaftaran2->status_pendaftaran = 'tidak_lulus_wawancara';
        $pendaftaran2->info_or_id = 2;
        $pendaftaran2->infoOr = Mockery::mock();
        $pendaftaran2->dinasPilihan1 = Mockery::mock();
        $pendaftaran2->dinasPilihan2 = Mockery::mock();
        $pendaftaran2->dinasDiterima = Mockery::mock();
        
        $pendaftaranCollection = collect([$pendaftaran1, $pendaftaran2]);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->with('user_id', 3)->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($pendaftaranCollection);
        
        $kegiatanCollection = $this->mockJadwalKegiatanCollection(2);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('whereIn')->with('info_or_id', [1])->andReturnSelf() // Hanya periode 1
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($kegiatanCollection);

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertEquals(2, $viewData['totalKegiatan']);
        $this->assertCount(2, $viewData['kegiatanTerdekat']);
    }

    /** @test */
    public function index_mahasiswa_tanpa_pendaftaran_menampilkan_data_kosong()
    {
        // Arrange
        $user = $this->mockAuthUser('mahasiswa', 4);
        
        $request = Request::create('/dashboard', 'GET');
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->with('user_id', 4)->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('whereIn')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertEquals(0, $viewData['totalKegiatan']);
        $this->assertCount(0, $viewData['pendaftaranUser']);
        $this->assertCount(0, $viewData['kegiatanTerdekat']);
    }

    /** @test */
    public function index_user_role_diperlakukan_sama_dengan_mahasiswa()
    {
        // Arrange
        $user = $this->mockAuthUser('user', 5);
        
        $request = Request::create('/dashboard', 'GET');
        
        $pendaftaranCollection = $this->mockPendaftaranCollection(1);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->with('user_id', 5)->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($pendaftaranCollection);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('whereIn')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertEquals('user', $viewData['user']->role);
        $this->assertArrayHasKey('pendaftaranUser', $viewData);
        $this->assertArrayHasKey('kegiatanTerdekat', $viewData);
        $this->assertFalse($viewData['showFilter']);
    }

    /** @test */
    public function index_admin_dinas_diperlakukan_sama_dengan_admin()
    {
        // Arrange
        $user = $this->mockAuthUser('admin_dinas', 6, 3);
        
        $request = Request::create('/dashboard', 'GET');
        
        $infoOrCollection = $this->mockInfoOrCollection(1);
        
        $this->mockInfoOrModel
            ->shouldReceive('whereHas')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection);
        
        // Mock untuk total count
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('count')->andReturn(5);
        
        // Mock untuk pendaftar terbaru
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->with('created_at', 'desc')->andReturnSelf()
            ->shouldReceive('limit')->with(10)->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockPendaftaranCollection(5));
        
        // Mock untuk additionalStats
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'terdaftar')->andReturnSelf()
            ->shouldReceive('count')->andReturn(2);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(1);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(1);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(1);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('whereIn')->andReturnSelf()
            ->shouldReceive('count')->andReturn(3)
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->mockJadwalKegiatanCollection(3));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertEquals('admin_dinas', $viewData['user']->role);
        $this->assertEquals(5, $viewData['totalPendaftar']);
        $this->assertEquals(1, $viewData['totalDinas']);
    }

    /** @test */
    public function index_superadmin_menampilkan_pendaftar_terbaru_limit_10()
    {
        // Arrange
        $user = $this->mockAuthUser('superadmin', 1);
        
        $request = Request::create('/dashboard', 'GET', ['info_or_id' => 'all']);
        
        $infoOrCollection = $this->mockInfoOrCollection(2);
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection);
        
        $pendaftaranCollection = $this->mockPendaftaranCollection(10);
        
        $this->mockPendaftaranModel
            ->shouldReceive('count')->andReturn(100)
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('orderBy')->with('created_at', 'desc')->andReturnSelf()
            ->shouldReceive('limit')->with(10)->andReturnSelf()
            ->shouldReceive('get')->andReturn($pendaftaranCollection);
        
        $this->mockDinasModel->shouldReceive('count')->andReturn(15);
        $this->mockInfoOrModel->shouldReceive('count')->andReturn(3);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('count')->andReturn(50)
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertCount(10, $viewData['pendaftarTerbaru']);
    }

    /** @test */
    public function index_superadmin_menampilkan_kegiatan_terdekat_limit_8()
    {
        // Arrange
        $user = $this->mockAuthUser('superadmin', 1);
        
        $request = Request::create('/dashboard', 'GET', ['info_or_id' => 'all']);
        
        $infoOrCollection = $this->mockInfoOrCollection(2);
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection);
        
        $this->mockPendaftaranModel
            ->shouldReceive('count')->andReturn(50)
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));
        
        $this->mockDinasModel->shouldReceive('count')->andReturn(10);
        $this->mockInfoOrModel->shouldReceive('count')->andReturn(3);
        
        $kegiatanCollection = $this->mockJadwalKegiatanCollection(8);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('count')->andReturn(20)
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('where')->with('tanggal_kegiatan', '>=', Mockery::any())->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('limit')->with(8)->andReturnSelf()
            ->shouldReceive('get')->andReturn($kegiatanCollection);

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertCount(8, $viewData['kegiatanTerdekat']);
    }

    /** @test */
    public function index_superadmin_hanya_menampilkan_kegiatan_mendatang()
    {
        // Arrange
        $user = $this->mockAuthUser('superadmin', 1);
        
        $request = Request::create('/dashboard', 'GET', ['info_or_id' => '1']);
        
        $infoOrCollection = $this->mockInfoOrCollection(1);
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection)
            ->shouldReceive('find')->with('1')->andReturn($infoOrCollection->first());
        
        $this->mockPendaftaranModel
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('count')->andReturn(10)
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));
        
        $this->mockDinasModel->shouldReceive('count')->andReturn(5);
        
        // Mock kegiatan mendatang
        $futureKegiatan = Mockery::mock();
        $futureKegiatan->id = 1;
        $futureKegiatan->nama_kegiatan = 'Kegiatan Mendatang';
        $futureKegiatan->tanggal_kegiatan = Carbon::now()->addDays(5);
        $futureKegiatan->infoOr = Mockery::mock();
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', '1')->andReturnSelf()
            ->shouldReceive('count')->andReturn(1)
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('where')->with('tanggal_kegiatan', '>=', Mockery::type('Illuminate\Support\Carbon'))->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('limit')->with(8)->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([$futureKegiatan]));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertCount(1, $viewData['kegiatanTerdekat']);
    }

    /** @test */
    public function index_mahasiswa_dengan_multiple_pendaftaran_berbeda_status()
    {
        // Arrange
        $user = $this->mockAuthUser('mahasiswa', 3);
        
        $request = Request::create('/dashboard', 'GET');
        
        $pendaftaran1 = Mockery::mock();
        $pendaftaran1->shouldReceive('getAttribute')->with('status_pendaftaran')->andReturn('lulus_wawancara');
        $pendaftaran1->shouldReceive('getAttribute')->with('info_or_id')->andReturn(1);
        $pendaftaran1->status_pendaftaran = 'lulus_wawancara';
        $pendaftaran1->info_or_id = 1;
        $pendaftaran1->infoOr = Mockery::mock();
        $pendaftaran1->dinasPilihan1 = Mockery::mock();
        $pendaftaran1->dinasPilihan2 = Mockery::mock();
        $pendaftaran1->dinasDiterima = Mockery::mock();
        
        $pendaftaran2 = Mockery::mock();
        $pendaftaran2->shouldReceive('getAttribute')->with('status_pendaftaran')->andReturn('lulus_wawancara');
        $pendaftaran2->shouldReceive('getAttribute')->with('info_or_id')->andReturn(2);
        $pendaftaran2->status_pendaftaran = 'lulus_wawancara';
        $pendaftaran2->info_or_id = 2;
        $pendaftaran2->infoOr = Mockery::mock();
        $pendaftaran2->dinasPilihan1 = Mockery::mock();
        $pendaftaran2->dinasPilihan2 = Mockery::mock();
        $pendaftaran2->dinasDiterima = Mockery::mock();
        
        $pendaftaran3 = Mockery::mock();
        $pendaftaran3->shouldReceive('getAttribute')->with('status_pendaftaran')->andReturn('tidak_lulus_wawancara');
        $pendaftaran3->shouldReceive('getAttribute')->with('info_or_id')->andReturn(3);
        $pendaftaran3->status_pendaftaran = 'tidak_lulus_wawancara';
        $pendaftaran3->info_or_id = 3;
        $pendaftaran3->infoOr = Mockery::mock();
        $pendaftaran3->dinasPilihan1 = Mockery::mock();
        $pendaftaran3->dinasPilihan2 = Mockery::mock();
        $pendaftaran3->dinasDiterima = Mockery::mock();
        
        $pendaftaranCollection = collect([$pendaftaran1, $pendaftaran2, $pendaftaran3]);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->with('user_id', 3)->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($pendaftaranCollection);
        
        $kegiatanCollection = $this->mockJadwalKegiatanCollection(5);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('whereIn')->with('info_or_id', [1, 2])->andReturnSelf() // Hanya yang lulus
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn($kegiatanCollection);

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertCount(3, $viewData['pendaftaranUser']);
        $this->assertEquals(5, $viewData['totalKegiatan']);
    }

    /** @test */
    public function index_mengembalikan_structure_data_yang_konsisten()
    {
        // Arrange
        $user = $this->mockAuthUser('superadmin', 1);
        
        $request = Request::create('/dashboard', 'GET');
        
        $infoOrCollection = $this->mockInfoOrCollection(1);
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn($infoOrCollection);
        
        $this->mockPendaftaranModel
            ->shouldReceive('count')->andReturn(10)
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));
        
        $this->mockDinasModel->shouldReceive('count')->andReturn(5);
        $this->mockInfoOrModel->shouldReceive('count')->andReturn(2);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('count')->andReturn(8)
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertArrayHasKey('user', $viewData);
        $this->assertArrayHasKey('selectedInfoOr', $viewData);
        $this->assertArrayHasKey('selectedInfoOrData', $viewData);
        $this->assertArrayHasKey('allInfoOr', $viewData);
        $this->assertArrayHasKey('showFilter', $viewData);
        $this->assertArrayHasKey('totalPendaftar', $viewData);
        $this->assertArrayHasKey('totalDinas', $viewData);
        $this->assertArrayHasKey('totalInfo', $viewData);
        $this->assertArrayHasKey('totalKegiatan', $viewData);
    }

    /** @test */
    public function index_admin_tanpa_info_or_terkait_menampilkan_filter_false()
    {
        // Arrange
        $user = $this->mockAuthUser('admin', 2, 5);
        
        $request = Request::create('/dashboard', 'GET');
        
        // Mock tidak ada InfoOr yang terkait dengan dinas admin
        $this->mockInfoOrModel
            ->shouldReceive('whereHas')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));
        
        // Mock untuk total count
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        // Mock untuk pendaftar terbaru
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->with('created_at', 'desc')->andReturnSelf()
            ->shouldReceive('limit')->with(10)->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));
        
        // Mock untuk additionalStats
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'terdaftar')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_wawancara')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        $this->mockPendaftaranModel
            ->shouldReceive('with')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('where')->with('status_pendaftaran', 'tidak_lulus_magang')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('whereIn')->andReturnSelf()
            ->shouldReceive('count')->andReturn(0)
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('orderBy')->andReturnSelf()
            ->shouldReceive('limit')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index($request);

        // Assert
        $viewData = $response->getData();
        
        $this->assertFalse($viewData['showFilter']);
        $this->assertEquals(0, $viewData['totalInfo']);
    }

    /** @test */
    public function index_dengan_berbagai_role_mengembalikan_view_yang_sama()
    {
        // Test untuk berbagai role
        $roles = ['superadmin', 'admin', 'admin_dinas', 'mahasiswa', 'user'];
        
        foreach ($roles as $role) {
            // Arrange
            $dinasId = in_array($role, ['admin', 'admin_dinas']) ? 1 : null;
            $user = $this->mockAuthUser($role, 1, $dinasId);
            
            $request = Request::create('/dashboard', 'GET');
            
            // Mock data sesuai kebutuhan role
            if ($role === 'superadmin') {
                $this->mockInfoOrModel
                    ->shouldReceive('select')->andReturnSelf()
                    ->shouldReceive('orderBy')->andReturnSelf()
                    ->shouldReceive('get')->andReturn(collect([]));
                
                $this->mockPendaftaranModel->shouldReceive('count')->andReturn(0);
                $this->mockDinasModel->shouldReceive('count')->andReturn(0);
                $this->mockInfoOrModel->shouldReceive('count')->andReturn(0);
                $this->mockJadwalKegiatanModel->shouldReceive('count')->andReturn(0);
            } elseif (in_array($role, ['admin', 'admin_dinas'])) {
                $this->mockInfoOrModel
                    ->shouldReceive('whereHas')->andReturnSelf()
                    ->shouldReceive('orderBy')->andReturnSelf()
                    ->shouldReceive('get')->andReturn(collect([]));
                
                $mockQuery = Mockery::mock();
                $mockQuery->shouldReceive('count')->andReturn(0);
                $mockQuery->shouldReceive('orderBy')->andReturnSelf();
                $mockQuery->shouldReceive('limit')->andReturnSelf();
                $mockQuery->shouldReceive('get')->andReturn(collect([]));
                
                $this->mockPendaftaranModel
                    ->shouldReceive('with')->andReturnSelf()
                    ->shouldReceive('where')->andReturnSelf()
                    ->andReturn($mockQuery);
                
                $this->mockJadwalKegiatanModel
                    ->shouldReceive('whereIn')->andReturnSelf()
                    ->shouldReceive('count')->andReturn(0);
            } else {
                $this->mockPendaftaranModel
                    ->shouldReceive('with')->andReturnSelf()
                    ->shouldReceive('where')->andReturnSelf()
                    ->shouldReceive('orderBy')->andReturnSelf()
                    ->shouldReceive('get')->andReturn(collect([]));
            }
            
            $this->mockPendaftaranModel
                ->shouldReceive('with')->andReturnSelf()
                ->shouldReceive('orderBy')->andReturnSelf()
                ->shouldReceive('limit')->andReturnSelf()
                ->shouldReceive('get')->andReturn(collect([]));
            
            $this->mockJadwalKegiatanModel
                ->shouldReceive('with')->andReturnSelf()
                ->shouldReceive('whereIn')->andReturnSelf()
                ->shouldReceive('where')->andReturnSelf()
                ->shouldReceive('orderBy')->andReturnSelf()
                ->shouldReceive('limit')->andReturnSelf()
                ->shouldReceive('get')->andReturn(collect([]));
            
            // Act
            $response = $this->controller->index($request);
            
            // Assert
            $this->assertInstanceOf(View::class, $response, "Failed for role: {$role}");
            $this->assertEquals('dashboard', $response->getName(), "Failed for role: {$role}");
        }
    }
}