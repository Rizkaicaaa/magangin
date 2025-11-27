<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\JadwalKegiatanController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;
use Mockery;
use Tests\TestCase;
use Exception;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini
class JadwalKegiatanControllerTest extends TestCase
{
    protected $controller;
    protected $mockInfoOrModel;
    protected $mockJadwalKegiatanModel;
    protected $mockPendaftaranModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Inisialisasi controller
        $this->controller = new JadwalKegiatanController();
        
        // Mock semua model sebagai class biasa
        $this->mockInfoOrModel = Mockery::mock('alias:App\Models\InfoOr');
        $this->mockJadwalKegiatanModel = Mockery::mock('alias:App\Models\JadwalKegiatan');
        $this->mockPendaftaranModel = Mockery::mock('alias:App\Models\Pendaftaran');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper untuk mock user
     */
    private function mockAuthUser($role = 'mahasiswa', $id = 1)
    {
        $user = Mockery::mock();
        $user->shouldReceive('getAttribute')->with('id')->andReturn($id);
        $user->shouldReceive('getAttribute')->with('role')->andReturn($role);
        $user->id = $id;
        $user->role = $role;
        
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('id')->andReturn($id);
        
        return $user;
    }

    /**
     * Helper untuk mock collection periode
     */
    private function mockPeriodeCollection()
    {
        $periode = Mockery::mock();
        $periode->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $periode->id = 1;
        $periode->periode = '2024/2025';
        $periode->status = 'aktif';
        $periode->tanggal_buka = '2024-01-01';
        $periode->tanggal_tutup = '2024-12-31';
        
        return collect([$periode]);
    }

    /**
     * Helper untuk mock kegiatan object
     */
    private function mockKegiatanObject($id = 1, $namaKegiatan = 'Test Kegiatan')
    {
        $kegiatan = Mockery::mock();
        $kegiatan->shouldReceive('getAttribute')->with('id')->andReturn($id);
        $kegiatan->shouldReceive('getAttribute')->with('nama_kegiatan')->andReturn($namaKegiatan);
        $kegiatan->id = $id;
        $kegiatan->nama_kegiatan = $namaKegiatan;
        $kegiatan->deskripsi_kegiatan = 'Deskripsi test';
        $kegiatan->tempat = 'Ruang A';
        $kegiatan->info_or_id = 1;

        // Mock tanggal
        $tanggal = Mockery::mock();
        $tanggal->shouldReceive('format')->with('Y-m-d')->andReturn('2024-06-01');
        $tanggal->shouldReceive('format')->with('d F Y')->andReturn('01 Juni 2024');
        $kegiatan->tanggal_kegiatan = $tanggal;

        // Mock waktu
        $waktuMulai = Mockery::mock();
        $waktuMulai->shouldReceive('format')->with('H:i')->andReturn('08:00');
        $kegiatan->waktu_mulai = $waktuMulai;

        $waktuSelesai = Mockery::mock();
        $waktuSelesai->shouldReceive('format')->with('H:i')->andReturn('10:00');
        $kegiatan->waktu_selesai = $waktuSelesai;

        // Mock timestamps
        $createdAt = Mockery::mock();
        $createdAt->shouldReceive('format')->with('Y-m-d H:i:s')->andReturn('2024-01-01 00:00:00');
        $kegiatan->created_at = $createdAt;

        $updatedAt = Mockery::mock();
        $updatedAt->shouldReceive('format')->with('Y-m-d H:i:s')->andReturn('2024-01-01 00:00:00');
        $kegiatan->updated_at = $updatedAt;

        // Mock relation
        $infoOr = Mockery::mock();
        $infoOr->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $infoOr->shouldReceive('getAttribute')->with('periode')->andReturn('2024/2025');
        $infoOr->shouldReceive('getAttribute')->with('status')->andReturn('aktif');
        $infoOr->id = 1;
        $infoOr->periode = '2024/2025';
        $infoOr->status = 'aktif';
        $kegiatan->infoOr = $infoOr;

        return $kegiatan;
    }

    /** @test */
    public function test_index_mengembalikan_view_untuk_mahasiswa_dengan_pendaftaran()
    {
        // Arrange
        $this->mockAuthUser('mahasiswa', 1);
        
        $periodeCollection = $this->mockPeriodeCollection();
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->with('id', 'periode', 'status', 'tanggal_buka', 'tanggal_tutup')->andReturnSelf()
            ->shouldReceive('orderBy')->with('id', 'desc')->andReturnSelf()
            ->shouldReceive('get')->andReturn($periodeCollection);

        $pendaftaran = Mockery::mock();
        $pendaftaran->shouldReceive('getAttribute')->with('info_or_id')->andReturn(1);
        $pendaftaran->info_or_id = 1;

        $this->mockPendaftaranModel
            ->shouldReceive('where')->with('user_id', 1)->andReturnSelf()
            ->shouldReceive('latest')->andReturnSelf()
            ->shouldReceive('first')->andReturn($pendaftaran);

        $this->mockJadwalKegiatanModel
            ->shouldReceive('query')->andReturnSelf()
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('orderBy')->with('waktu_mulai', 'asc')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('kegiatan.index', $response->getName());
        $this->assertArrayHasKey('periodes', $response->getData());
        $this->assertArrayHasKey('userRole', $response->getData());
        $this->assertArrayHasKey('selectedPeriode', $response->getData());
        $this->assertArrayHasKey('kegiatans', $response->getData());
        $this->assertEquals('mahasiswa', $response->getData()['userRole']);
        $this->assertEquals(1, $response->getData()['selectedPeriode']);
    }

    /** @test */
    public function test_index_mengembalikan_view_untuk_admin_dengan_periode_terbaru()
    {
        // Arrange
        $this->mockAuthUser('admin', 2);
        
        $periodeCollection = $this->mockPeriodeCollection();
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->with('id', 'periode', 'status', 'tanggal_buka', 'tanggal_tutup')->andReturnSelf()
            ->shouldReceive('orderBy')->with('id', 'desc')->andReturnSelf()
            ->shouldReceive('get')->andReturn($periodeCollection);

        $this->mockJadwalKegiatanModel
            ->shouldReceive('query')->andReturnSelf()
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('orderBy')->with('waktu_mulai', 'asc')->andReturnSelf()
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('kegiatan.index', $response->getName());
        $this->assertEquals('admin', $response->getData()['userRole']);
        $this->assertEquals(1, $response->getData()['selectedPeriode']);
    }

    /** @test */
    public function test_index_mengembalikan_view_untuk_superadmin_dengan_periode_terbaru()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 3);
        
        $periodeCollection = $this->mockPeriodeCollection();
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->with('id', 'periode', 'status', 'tanggal_buka', 'tanggal_tutup')->andReturnSelf()
            ->shouldReceive('orderBy')->with('id', 'desc')->andReturnSelf()
            ->shouldReceive('get')->andReturn($periodeCollection);

        $this->mockJadwalKegiatanModel
            ->shouldReceive('query')->andReturnSelf()
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('orderBy')->with('waktu_mulai', 'asc')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('superadmin', $response->getData()['userRole']);
        $this->assertEquals(1, $response->getData()['selectedPeriode']);
    }

    /** @test */
    public function test_index_menangani_exception_dengan_baik()
    {
        // Arrange
        $this->mockAuthUser('mahasiswa', 1);
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->andThrow(new Exception('Database error'));

        Log::shouldReceive('error')->once();

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertNotNull($response);
    }

    /** @test */
    public function test_getByPeriode_mengembalikan_data_kegiatan_berdasarkan_periode()
    {
        // Arrange
        $request = Request::create('/api/kegiatan/by-periode', 'GET', ['periode_id' => 1]);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn(['periode_id' => 1]);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        $periode = Mockery::mock();
        $periode->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $periode->shouldReceive('getAttribute')->with('periode')->andReturn('2024/2025');
        $periode->shouldReceive('getAttribute')->with('status')->andReturn('aktif');
        $periode->shouldReceive('getAttribute')->with('tanggal_buka')->andReturn('2024-01-01');
        $periode->shouldReceive('getAttribute')->with('tanggal_tutup')->andReturn('2024-12-31');
        $periode->id = 1;
        $periode->periode = '2024/2025';
        $periode->status = 'aktif';
        $periode->tanggal_buka = '2024-01-01';
        $periode->tanggal_tutup = '2024-12-31';

        $this->mockInfoOrModel
            ->shouldReceive('findOrFail')->with(1)->andReturn($periode);

        $kegiatan = $this->mockKegiatanObject(1, 'Test Kegiatan');
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('orderBy')->with('waktu_mulai', 'asc')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([$kegiatan]));

        // Act
        $response = $this->controller->getByPeriode($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Data berhasil diambil', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('periode', $responseData);
        $this->assertArrayHasKey('total', $responseData);
        $this->assertEquals(1, $responseData['total']);
    }

    /** @test */
    public function test_getByPeriode_mengembalikan_error_validasi_ketika_periode_id_tidak_valid()
    {
        // Arrange
        $request = Request::create('/api/kegiatan/by-periode', 'GET', ['periode_id' => 'invalid']);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(true);
        $mockValidator->shouldReceive('errors')->andReturn(collect([
            'periode_id' => ['Parameter periode_id tidak valid']
        ]));

        Validator::shouldReceive('make')->andReturn($mockValidator);

        // Act
        $response = $this->controller->getByPeriode($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Parameter periode_id tidak valid', $responseData['message']);
        $this->assertArrayHasKey('errors', $responseData);
    }

    /** @test */
    public function test_getByPeriode_menangani_exception_dengan_baik()
    {
        // Arrange
        $request = Request::create('/api/kegiatan/by-periode', 'GET', ['periode_id' => 1]);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn(['periode_id' => 1]);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        $this->mockInfoOrModel
            ->shouldReceive('findOrFail')->andThrow(new Exception('Database error'));

        Log::shouldReceive('error')->once();

        // Act
        $response = $this->controller->getByPeriode($request);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Gagal mengambil data jadwal kegiatan', $responseData['message']);
    }

    /** @test */
    public function test_show_menampilkan_detail_kegiatan_dengan_sukses()
    {
        // Arrange
        $kegiatan = $this->mockKegiatanObject(1, 'Detail Kegiatan');
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('findOrFail')->with(1)->andReturn($kegiatan);

        // Act
        $response = $this->controller->show(1);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Data berhasil diambil', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertEquals('Detail Kegiatan', $responseData['data']['nama_kegiatan']);
        $this->assertEquals('2024-06-01', $responseData['data']['tanggal_kegiatan']);
        $this->assertEquals('08:00', $responseData['data']['waktu_mulai']);
    }

    /** @test */
    public function test_show_mengembalikan_error_ketika_kegiatan_tidak_ditemukan()
    {
        // Arrange
        $this->mockJadwalKegiatanModel
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('findOrFail')->with(999)->andThrow(new Exception('Not found'));

        Log::shouldReceive('error')->once();

        // Act
        $response = $this->controller->show(999);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Jadwal kegiatan tidak ditemukan', $responseData['message']);
    }

    /** @test */
    public function test_store_menolak_akses_untuk_non_superadmin()
    {
        // Arrange
        $this->mockAuthUser('admin', 2);
        
        $request = Request::create('/api/kegiatan', 'POST', []);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('Unauthorized', $responseData['message']);
        $this->assertStringContainsString('superadmin', $responseData['message']);
    }

    /** @test */
    public function test_store_berhasil_membuat_kegiatan_untuk_superadmin()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $requestData = [
            'info_or_id' => 1,
            'nama_kegiatan' => 'Kegiatan Baru',
            'deskripsi_kegiatan' => 'Deskripsi kegiatan baru',
            'tanggal_kegiatan' => '2025-12-01',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:00',
            'tempat' => 'Ruang A'
        ];

        $request = Request::create('/api/kegiatan', 'POST', $requestData);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn($requestData);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        // Mock tidak ada kegiatan yang bentrok
        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('where')->with('tanggal_kegiatan', '2025-12-01')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('first')->andReturn(null);

        $createdKegiatan = $this->mockKegiatanObject(1, 'Kegiatan Baru');

        $this->mockJadwalKegiatanModel
            ->shouldReceive('create')->with($requestData)->andReturn($createdKegiatan);

        Log::shouldReceive('info')->once();

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Kegiatan berhasil ditambahkan', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertEquals('Kegiatan Baru', $responseData['data']['nama_kegiatan']);
    }

    /** @test */
    public function test_store_mengembalikan_error_validasi_ketika_data_tidak_lengkap()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);
        
        $request = Request::create('/api/kegiatan', 'POST', []);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(true);
        $mockValidator->shouldReceive('errors')->andReturn(collect([
            'nama_kegiatan' => ['Nama kegiatan harus diisi'],
            'tanggal_kegiatan' => ['Tanggal kegiatan harus diisi']
        ]));

        Validator::shouldReceive('make')->andReturn($mockValidator);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(422, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Validasi gagal', $responseData['message']);
        $this->assertArrayHasKey('errors', $responseData);
    }

    /** @test */
    public function test_store_mengembalikan_error_ketika_waktu_bentrok()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $requestData = [
            'info_or_id' => 1,
            'nama_kegiatan' => 'Kegiatan Baru',
            'tanggal_kegiatan' => '2025-12-01',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:00',
        ];

        $request = Request::create('/api/kegiatan', 'POST', $requestData);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn($requestData);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        // Mock ada kegiatan yang bentrok
        $existingKegiatan = Mockery::mock();
        $existingKegiatan->shouldReceive('getAttribute')->with('nama_kegiatan')->andReturn('Kegiatan Lama');
        $existingKegiatan->nama_kegiatan = 'Kegiatan Lama';

        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('where')->with('tanggal_kegiatan', '2025-12-01')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('first')->andReturn($existingKegiatan);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(422, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('Sudah ada kegiatan lain', $responseData['message']);
        $this->assertEquals('Kegiatan Lama', $responseData['existing_kegiatan']);
    }

    /** @test */
    public function test_store_menangani_exception_dengan_baik()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $request = Request::create('/api/kegiatan', 'POST', ['info_or_id' => 1]);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn(['info_or_id' => 1]);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->andThrow(new Exception('Database error'));

        Log::shouldReceive('error')->once();

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('Gagal menyimpan kegiatan', $responseData['message']);
    }

    /** @test */
    public function test_update_menolak_akses_untuk_non_superadmin()
    {
        // Arrange
        $this->mockAuthUser('mahasiswa', 3);
        
        $request = Request::create('/api/kegiatan/1', 'PUT', []);

        // Act
        $response = $this->controller->update($request, 1);

        // Assert
        $this->assertEquals(403, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('Unauthorized', $responseData['message']);
    }

    /** @test */
    public function test_update_berhasil_memperbarui_kegiatan_untuk_superadmin()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $requestData = [
            'info_or_id' => 1,
            'nama_kegiatan' => 'Kegiatan Diperbarui',
            'deskripsi_kegiatan' => 'Deskripsi baru',
            'tanggal_kegiatan' => '2025-12-01',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang B'
        ];

        $request = Request::create('/api/kegiatan/1', 'PUT', $requestData);

        $kegiatan = $this->mockKegiatanObject(1, 'Kegiatan Lama');
        $kegiatan->shouldReceive('update')->with($requestData)->andReturn(true);
        $kegiatan->nama_kegiatan = 'Kegiatan Diperbarui';

        $this->mockJadwalKegiatanModel
            ->shouldReceive('findOrFail')->with(1)->andReturn($kegiatan);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn($requestData);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        // Mock tidak ada kegiatan yang bentrok (exclude current)
        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('where')->with('id', '!=', 1)->andReturnSelf()
            ->shouldReceive('where')->with('tanggal_kegiatan', '2025-12-01')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('first')->andReturn(null);

        Log::shouldReceive('info')->once();

        // Act
        $response = $this->controller->update($request, 1);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Kegiatan berhasil diperbarui', $responseData['message']);
        $this->assertEquals('Kegiatan Diperbarui', $responseData['data']['nama_kegiatan']);
    }

    /** @test */
    public function test_update_mengembalikan_error_validasi_ketika_data_tidak_valid()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $kegiatan = $this->mockKegiatanObject(1, 'Kegiatan Test');
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('findOrFail')->with(1)->andReturn($kegiatan);

        $request = Request::create('/api/kegiatan/1', 'PUT', []);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(true);
        $mockValidator->shouldReceive('errors')->andReturn(collect([
            'nama_kegiatan' => ['Nama kegiatan harus diisi']
        ]));

        Validator::shouldReceive('make')->andReturn($mockValidator);

        // Act
        $response = $this->controller->update($request, 1);

        // Assert
        $this->assertEquals(422, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('errors', $responseData);
    }

    /** @test */
    public function test_update_mengembalikan_error_ketika_waktu_bentrok_dengan_kegiatan_lain()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $requestData = [
            'info_or_id' => 1,
            'nama_kegiatan' => 'Kegiatan Update',
            'tanggal_kegiatan' => '2025-12-01',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:00',
        ];

        $request = Request::create('/api/kegiatan/1', 'PUT', $requestData);

        $kegiatan = $this->mockKegiatanObject(1, 'Kegiatan Lama');
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('findOrFail')->with(1)->andReturn($kegiatan);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn($requestData);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        // Mock ada kegiatan yang bentrok
        $existingKegiatan = Mockery::mock();
        $existingKegiatan->shouldReceive('getAttribute')->with('nama_kegiatan')->andReturn('Kegiatan Bentrok');
        $existingKegiatan->nama_kegiatan = 'Kegiatan Bentrok';

        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('where')->with('id', '!=', 1)->andReturnSelf()
            ->shouldReceive('where')->with('tanggal_kegiatan', '2025-12-01')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('first')->andReturn($existingKegiatan);

        // Act
        $response = $this->controller->update($request, 1);

        // Assert
        $this->assertEquals(422, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('Sudah ada kegiatan lain', $responseData['message']);
        $this->assertEquals('Kegiatan Bentrok', $responseData['existing_kegiatan']);
    }

    /** @test */
    public function test_update_menangani_exception_dengan_baik()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $this->mockJadwalKegiatanModel
            ->shouldReceive('findOrFail')->with(999)->andThrow(new Exception('Not found'));

        Log::shouldReceive('error')->once();

        $request = Request::create('/api/kegiatan/999', 'PUT', []);

        // Act
        $response = $this->controller->update($request, 999);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('Gagal memperbarui kegiatan', $responseData['message']);
    }

    /** @test */
    public function test_destroy_menolak_akses_untuk_non_superadmin()
    {
        // Arrange
        $this->mockAuthUser('admin', 2);

        // Act
        $response = $this->controller->destroy(1);

        // Assert
        $this->assertEquals(403, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('Unauthorized', $responseData['message']);
        $this->assertStringContainsString('superadmin', $responseData['message']);
    }

    /** @test */
    public function test_destroy_berhasil_menghapus_kegiatan_untuk_superadmin()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $kegiatan = Mockery::mock();
        $kegiatan->shouldReceive('getAttribute')->with('nama_kegiatan')->andReturn('Kegiatan Dihapus');
        $kegiatan->shouldReceive('delete')->andReturn(true);
        $kegiatan->nama_kegiatan = 'Kegiatan Dihapus';

        $this->mockJadwalKegiatanModel
            ->shouldReceive('findOrFail')->with(1)->andReturn($kegiatan);

        Log::shouldReceive('info')->once();

        // Act
        $response = $this->controller->destroy(1);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertStringContainsString('Kegiatan Dihapus', $responseData['message']);
        $this->assertStringContainsString('berhasil dihapus', $responseData['message']);
    }

    /** @test */
    public function test_destroy_mengembalikan_error_ketika_kegiatan_tidak_ditemukan()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $this->mockJadwalKegiatanModel
            ->shouldReceive('findOrFail')->with(999)->andThrow(new Exception('Not found'));

        Log::shouldReceive('error')->once();

        // Act
        $response = $this->controller->destroy(999);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('Gagal menghapus kegiatan', $responseData['message']);
    }

    /** @test */
    public function test_index_untuk_mahasiswa_tanpa_pendaftaran()
    {
        // Arrange
        $this->mockAuthUser('mahasiswa', 1);
        
        $periodeCollection = $this->mockPeriodeCollection();
        
        $this->mockInfoOrModel
            ->shouldReceive('select')->with('id', 'periode', 'status', 'tanggal_buka', 'tanggal_tutup')->andReturnSelf()
            ->shouldReceive('orderBy')->with('id', 'desc')->andReturnSelf()
            ->shouldReceive('get')->andReturn($periodeCollection);

        // Mock mahasiswa tidak memiliki pendaftaran
        $this->mockPendaftaranModel
            ->shouldReceive('where')->with('user_id', 1)->andReturnSelf()
            ->shouldReceive('latest')->andReturnSelf()
            ->shouldReceive('first')->andReturn(null);

        $this->mockJadwalKegiatanModel
            ->shouldReceive('query')->andReturnSelf()
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('orderBy')->with('waktu_mulai', 'asc')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('kegiatan.index', $response->getName());
        $this->assertNull($response->getData()['selectedPeriode']);
    }

    /** @test */
    public function test_index_dengan_periode_kosong()
    {
        // Arrange
        $this->mockAuthUser('admin', 2);
        
        // Mock tidak ada periode
        $this->mockInfoOrModel
            ->shouldReceive('select')->with('id', 'periode', 'status', 'tanggal_buka', 'tanggal_tutup')->andReturnSelf()
            ->shouldReceive('orderBy')->with('id', 'desc')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        $this->mockJadwalKegiatanModel
            ->shouldReceive('query')->andReturnSelf()
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('orderBy')->with('waktu_mulai', 'asc')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertNull($response->getData()['selectedPeriode']);
    }

    /** @test */
    public function test_getByPeriode_dengan_multiple_kegiatan()
    {
        // Arrange
        $request = Request::create('/api/kegiatan/by-periode', 'GET', ['periode_id' => 1]);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn(['periode_id' => 1]);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        $periode = Mockery::mock();
        $periode->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $periode->shouldReceive('getAttribute')->with('periode')->andReturn('2024/2025');
        $periode->shouldReceive('getAttribute')->with('status')->andReturn('aktif');
        $periode->shouldReceive('getAttribute')->with('tanggal_buka')->andReturn('2024-01-01');
        $periode->shouldReceive('getAttribute')->with('tanggal_tutup')->andReturn('2024-12-31');
        $periode->id = 1;
        $periode->periode = '2024/2025';
        $periode->status = 'aktif';
        $periode->tanggal_buka = '2024-01-01';
        $periode->tanggal_tutup = '2024-12-31';

        $this->mockInfoOrModel
            ->shouldReceive('findOrFail')->with(1)->andReturn($periode);

        $kegiatan1 = $this->mockKegiatanObject(1, 'Kegiatan 1');
        $kegiatan2 = $this->mockKegiatanObject(2, 'Kegiatan 2');
        $kegiatan3 = $this->mockKegiatanObject(3, 'Kegiatan 3');
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('orderBy')->with('waktu_mulai', 'asc')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([$kegiatan1, $kegiatan2, $kegiatan3]));

        // Act
        $response = $this->controller->getByPeriode($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals(3, $responseData['total']);
        $this->assertCount(3, $responseData['data']);
    }

    /** @test */
    public function test_getByPeriode_dengan_kegiatan_kosong()
    {
        // Arrange
        $request = Request::create('/api/kegiatan/by-periode', 'GET', ['periode_id' => 1]);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn(['periode_id' => 1]);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        $periode = Mockery::mock();
        $periode->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $periode->shouldReceive('getAttribute')->with('periode')->andReturn('2024/2025');
        $periode->shouldReceive('getAttribute')->with('status')->andReturn('aktif');
        $periode->shouldReceive('getAttribute')->with('tanggal_buka')->andReturn('2024-01-01');
        $periode->shouldReceive('getAttribute')->with('tanggal_tutup')->andReturn('2024-12-31');
        $periode->id = 1;
        $periode->periode = '2024/2025';
        $periode->status = 'aktif';
        $periode->tanggal_buka = '2024-01-01';
        $periode->tanggal_tutup = '2024-12-31';

        $this->mockInfoOrModel
            ->shouldReceive('findOrFail')->with(1)->andReturn($periode);
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('orderBy')->with('tanggal_kegiatan', 'asc')->andReturnSelf()
            ->shouldReceive('orderBy')->with('waktu_mulai', 'asc')->andReturnSelf()
            ->shouldReceive('get')->andReturn(collect([]));

        // Act
        $response = $this->controller->getByPeriode($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals(0, $responseData['total']);
        $this->assertEmpty($responseData['data']);
    }

    /** @test */
    public function test_show_dengan_kegiatan_tanpa_waktu_selesai()
    {
        // Arrange
        $kegiatan = $this->mockKegiatanObject(1, 'Kegiatan Tanpa Waktu Selesai');
        
        // Override waktu_selesai menjadi null
        $kegiatan->waktu_selesai = null;
        
        $this->mockJadwalKegiatanModel
            ->shouldReceive('with')->with('infoOr')->andReturnSelf()
            ->shouldReceive('findOrFail')->with(1)->andReturn($kegiatan);

        // Act
        $response = $this->controller->show(1);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertNull($responseData['data']['waktu_selesai']);
    }

    /** @test */
    public function test_store_dengan_data_minimal_tanpa_waktu_selesai()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $requestData = [
            'info_or_id' => 1,
            'nama_kegiatan' => 'Kegiatan Minimal',
            'tanggal_kegiatan' => '2025-12-01',
            'waktu_mulai' => '08:00',
        ];

        $request = Request::create('/api/kegiatan', 'POST', $requestData);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn($requestData);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', 1)->andReturnSelf()
            ->shouldReceive('where')->with('tanggal_kegiatan', '2025-12-01')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('first')->andReturn(null);

        $createdKegiatan = $this->mockKegiatanObject(1, 'Kegiatan Minimal');
        $createdKegiatan->waktu_selesai = null;

        $this->mockJadwalKegiatanModel
            ->shouldReceive('create')->with($requestData)->andReturn($createdKegiatan);

        Log::shouldReceive('info')->once();

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Kegiatan Minimal', $responseData['data']['nama_kegiatan']);
    }

    /** @test */
    public function test_update_dengan_perubahan_periode()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        $requestData = [
            'info_or_id' => 2, // Ganti ke periode berbeda
            'nama_kegiatan' => 'Kegiatan Pindah Periode',
            'deskripsi_kegiatan' => 'Deskripsi',
            'tanggal_kegiatan' => '2025-12-01',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'tempat' => 'Ruang C'
        ];

        $request = Request::create('/api/kegiatan/1', 'PUT', $requestData);

        $kegiatan = $this->mockKegiatanObject(1, 'Kegiatan Lama');
        $kegiatan->info_or_id = 1; // Periode awal
        $kegiatan->shouldReceive('update')->with($requestData)->andReturn(true);
        $kegiatan->nama_kegiatan = 'Kegiatan Pindah Periode';

        $this->mockJadwalKegiatanModel
            ->shouldReceive('findOrFail')->with(1)->andReturn($kegiatan);

        $mockValidator = Mockery::mock(ValidationValidator::class);
        $mockValidator->shouldReceive('fails')->andReturn(false);
        $mockValidator->shouldReceive('validated')->andReturn($requestData);

        Validator::shouldReceive('make')->andReturn($mockValidator);

        $this->mockJadwalKegiatanModel
            ->shouldReceive('where')->with('info_or_id', 2)->andReturnSelf()
            ->shouldReceive('where')->with('id', '!=', 1)->andReturnSelf()
            ->shouldReceive('where')->with('tanggal_kegiatan', '2025-12-01')->andReturnSelf()
            ->shouldReceive('where')->andReturnSelf()
            ->shouldReceive('first')->andReturn(null);

        Log::shouldReceive('info')->once();

        // Act
        $response = $this->controller->update($request, 1);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Kegiatan Pindah Periode', $responseData['data']['nama_kegiatan']);
    }

    /** @test */
    public function test_destroy_multiple_kegiatan_secara_berurutan()
    {
        // Arrange
        $this->mockAuthUser('superadmin', 1);

        // Kegiatan pertama
        $kegiatan1 = Mockery::mock();
        $kegiatan1->shouldReceive('getAttribute')->with('nama_kegiatan')->andReturn('Kegiatan 1');
        $kegiatan1->shouldReceive('delete')->andReturn(true);
        $kegiatan1->nama_kegiatan = 'Kegiatan 1';

        // Kegiatan kedua
        $kegiatan2 = Mockery::mock();
        $kegiatan2->shouldReceive('getAttribute')->with('nama_kegiatan')->andReturn('Kegiatan 2');
        $kegiatan2->shouldReceive('delete')->andReturn(true);
        $kegiatan2->nama_kegiatan = 'Kegiatan 2';

        $this->mockJadwalKegiatanModel
            ->shouldReceive('findOrFail')->with(1)->andReturn($kegiatan1)
            ->shouldReceive('findOrFail')->with(2)->andReturn($kegiatan2);

        Log::shouldReceive('info')->twice();

        // Act
        $response1 = $this->controller->destroy(1);
        $response2 = $this->controller->destroy(2);

        // Assert
        $this->assertEquals(200, $response1->getStatusCode());
        $this->assertEquals(200, $response2->getStatusCode());
        
        $responseData1 = $response1->getData(true);
        $responseData2 = $response2->getData(true);
        
        $this->assertTrue($responseData1['success']);
        $this->assertTrue($responseData2['success']);
        $this->assertStringContainsString('Kegiatan 1', $responseData1['message']);
        $this->assertStringContainsString('Kegiatan 2', $responseData2['message']);
    }
}