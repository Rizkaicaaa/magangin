<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\PendaftarController;

class PendaftarControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper to create a mock request with validation bypass
     */
    protected function createMockRequest($data, $files = [])
    {
        $request = Mockery::mock(Request::class)->makePartial();
        
        // Mock validate to return data directly (bypass validation)
        $request->shouldReceive('validate')
            ->andReturn($data);
        
        // Mock other request methods
        $request->shouldReceive('all')->andReturn($data);
        $request->shouldReceive('except')->andReturn($data);
        $request->shouldReceive('allFiles')->andReturn($files);
        
        // Mock file methods
        foreach ($files as $key => $file) {
            $request->shouldReceive('hasFile')->with($key)->andReturn(true);
            $request->shouldReceive('file')->with($key)->andReturn($file);
        }
        
        // Set properties
        foreach ($data as $key => $value) {
            $request->$key = $value;
        }
        
        return $request;
    }

    /** @test */
    public function index_menampilkan_semua_pendaftar_dan_dropdown()
    {
        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('with->where->whereHas->orderBy->get')
                 ->andReturn(collect(['user1', 'user2']));

        $dinasMock = Mockery::mock('overload:App\Models\Dinas');
        $dinasMock->shouldReceive('all')->andReturn(collect(['dinas1', 'dinas2']));

        $infoOrMock = Mockery::mock('overload:App\Models\InfoOr');
        $infoOrMock->shouldReceive('select->distinct->orderBy->get')
                   ->andReturn(collect(['periode1', 'periode2']));

        $controller = new PendaftarController();
        $response = $controller->index();

        $viewData = $response->getData();

        $this->assertArrayHasKey('pendaftars', $viewData);
        $this->assertArrayHasKey('allDinas', $viewData);
        $this->assertArrayHasKey('allPeriode', $viewData);
    }

    /** @test */
    public function create_berhasil_mendaftar_mahasiswa()
    {
        // Setup facades
        Hash::shouldReceive('make')->once()->with('password123')->andReturn('hashed123');
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->never();

        // Create valid files
        $cvFile = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');
        $transkripFile = UploadedFile::fake()->create('transkrip.pdf', 100, 'application/pdf');
        
        // Request data
        $requestData = [
            'nama_lengkap' => 'Budi Santoso',
            'nim' => '123456',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '08123456789',
            'pilihan_dinas_1' => 1,
            'pilihan_dinas_2' => 2,
            'motivasi' => 'Motivasi saya untuk magang',
            'pengalaman' => 'Pengalaman saya di bidang IT'
        ];

        $request = $this->createMockRequest($requestData, [
            'file_cv' => $cvFile,
            'file_transkrip' => $transkripFile
        ]);

        // Mock User model
        $userMock = Mockery::mock('alias:App\Models\User');
        $userInstance = Mockery::mock();
        $userInstance->id = 1;
        $userInstance->nama_lengkap = 'Budi Santoso';
        $userInstance->email = 'budi@example.com';
        $userInstance->nim = '123456';
        $userInstance->no_telp = '08123456789';
        $userInstance->role = 'mahasiswa';
        
        $userMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function($arg) {
                return $arg['nama_lengkap'] === 'Budi Santoso' 
                    && $arg['email'] === 'budi@example.com';
            }))
            ->andReturn($userInstance);

        // Mock InfoOr model
        $infoOrMock = Mockery::mock('alias:App\Models\InfoOr');
        $infoOrInstance = Mockery::mock();
        $infoOrInstance->id = 1;
        $infoOrInstance->status = 'buka';
        
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('orderBy')->with('tanggal_buka', 'desc')->andReturnSelf();
        $queryBuilder->shouldReceive('first')->andReturn($infoOrInstance);
        
        $infoOrMock->shouldReceive('where')->with('status', 'buka')->andReturn($queryBuilder);

        // Mock Pendaftaran model
        $pendaftaranMock = Mockery::mock('alias:App\Models\Pendaftaran');
        $pendaftaranInstance = Mockery::mock();
        $pendaftaranInstance->id = 1;
        
        $pendaftaranMock->shouldReceive('create')
            ->once()
            ->andReturn($pendaftaranInstance);

        $controller = new PendaftarController();
        $response = $controller->create($request);

        $this->assertEquals(201, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Budi Santoso', $data['data']['nama_lengkap']);
        $this->assertEquals('budi@example.com', $data['data']['email']);
    }

    /** @test */
    public function create_gagal_validation_error()
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->once();
        DB::shouldReceive('rollBack')->once();

        // Create invalid request data
        $requestData = [
            'nama_lengkap' => '',
            'nim' => '',
            'email' => 'not-an-email',
            'password' => '123',
            'password_confirmation' => '1234',
        ];

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('all')->andReturn($requestData);
        $request->shouldReceive('except')->andReturn($requestData);
        $request->shouldReceive('allFiles')->andReturn([]);
        
        // Mock ValidationException with proper message bag
        $messageBag = Mockery::mock(\Illuminate\Support\MessageBag::class);
        $messageBag->shouldReceive('toArray')->andReturn([
            'nama_lengkap' => ['Nama lengkap harus diisi'],
            'nim' => ['NIM harus diisi'],
            'email' => ['Format email tidak valid'],
            'password' => ['Password minimal 8 karakter'],
        ]);
        $messageBag->shouldReceive('all')->andReturn([
            'Nama lengkap harus diisi',
            'NIM harus diisi',
            'Format email tidak valid',
            'Password minimal 8 karakter',
        ]);
        
        $mockValidator = Mockery::mock(\Illuminate\Validation\Validator::class);
        $mockValidator->shouldReceive('errors')->andReturn($messageBag);
        
        // Mock Translator dengan method get() dan choice() - accept any arguments
        $mockTranslator = Mockery::mock(\Illuminate\Contracts\Translation\Translator::class);
        $mockTranslator->shouldReceive('get')
            ->andReturn('The given data was invalid.');
        $mockTranslator->shouldReceive('choice')
            ->withAnyArgs() // Accept any arguments for choice()
            ->andReturn('(and 3 more errors)');
        
        $mockValidator->shouldReceive('getTranslator')->andReturn($mockTranslator);

        $validationException = new ValidationException($mockValidator);
        
        $request->shouldReceive('validate')
            ->andThrow($validationException);

        $controller = new PendaftarController();
        $response = $controller->create($request);

        $this->assertEquals(422, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('errors', $data);
    }

    /** @test */
    public function create_gagal_ketika_tidak_ada_info_or_aktif()
    {
        Hash::shouldReceive('make')->once()->andReturn('hashed123');
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();
        DB::shouldReceive('commit')->never();
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->once();

        // Create proper request
        $cvFile = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');
        $transkripFile = UploadedFile::fake()->create('transkrip.pdf', 100, 'application/pdf');
        
        $requestData = [
            'nama_lengkap' => 'Budi Santoso',
            'nim' => '123456',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '08123456789',
            'pilihan_dinas_1' => 1,
            'pilihan_dinas_2' => 2,
            'motivasi' => 'Motivasi saya',
            'pengalaman' => 'Pengalaman saya'
        ];

        $request = $this->createMockRequest($requestData, [
            'file_cv' => $cvFile,
            'file_transkrip' => $transkripFile
        ]);

        // Mock User
        $userMock = Mockery::mock('alias:App\Models\User');
        $userInstance = Mockery::mock();
        $userInstance->id = 1;
        $userInstance->nama_lengkap = 'Budi Santoso';
        $userInstance->email = 'budi@example.com';
        $userInstance->nim = '123456';
        
        $userMock->shouldReceive('create')->once()->andReturn($userInstance);

        // InfoOr tidak ditemukan (return null)
        $infoOrMock = Mockery::mock('alias:App\Models\InfoOr');
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('orderBy')->with('tanggal_buka', 'desc')->andReturnSelf();
        $queryBuilder->shouldReceive('first')->andReturn(null);
        
        $infoOrMock->shouldReceive('where')->with('status', 'buka')->andReturn($queryBuilder);

        $controller = new PendaftarController();
        $response = $controller->create($request);

        $this->assertEquals(400, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Tidak ada periode Info OR', $data['message']);
    }

    /** @test */
    public function create_gagal_file_upload_invalid()
    {
        Hash::shouldReceive('make')->once()->andReturn('hashed123');
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();
        DB::shouldReceive('commit')->never();
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->once();

        $requestData = [
            'nama_lengkap' => 'Budi Santoso',
            'nim' => '123456',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '08123456789',
            'pilihan_dinas_1' => 1,
            'pilihan_dinas_2' => 2,
            'motivasi' => 'Motivasi saya',
            'pengalaman' => 'Pengalaman saya'
        ];

        // Create INVALID file mock (isValid returns false)
        $invalidCvFile = Mockery::mock(UploadedFile::class);
        $invalidCvFile->shouldReceive('isValid')->andReturn(false);
        $invalidCvFile->shouldReceive('getClientOriginalName')->andReturn('cv.pdf');
        
        $transkripFile = UploadedFile::fake()->create('transkrip.pdf', 100, 'application/pdf');

        $request = $this->createMockRequest($requestData, [
            'file_cv' => $invalidCvFile,
            'file_transkrip' => $transkripFile
        ]);

        // Mock User
        $userMock = Mockery::mock('alias:App\Models\User');
        $userInstance = Mockery::mock();
        $userInstance->id = 1;
        $userInstance->nama_lengkap = 'Budi Santoso';
        $userInstance->email = 'budi@example.com';
        $userInstance->nim = '123456';
        
        $userMock->shouldReceive('create')->once()->andReturn($userInstance);

        // Mock InfoOr
        $infoOrMock = Mockery::mock('alias:App\Models\InfoOr');
        $infoOrInstance = Mockery::mock();
        $infoOrInstance->id = 1;
        $infoOrInstance->status = 'buka';
        
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('orderBy')->with('tanggal_buka', 'desc')->andReturnSelf();
        $queryBuilder->shouldReceive('first')->andReturn($infoOrInstance);
        
        $infoOrMock->shouldReceive('where')->with('status', 'buka')->andReturn($queryBuilder);

        $controller = new PendaftarController();
        $response = $controller->create($request);

        $this->assertEquals(400, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Gagal mengupload file', $data['message']);
    }

    /** @test */
    public function create_berhasil_dengan_pilihan_dinas_2_null()
    {
        Hash::shouldReceive('make')->once()->andReturn('hashed123');
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->never();

        // Create proper request with null dinas 2
        $cvFile = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');
        $transkripFile = UploadedFile::fake()->create('transkrip.pdf', 100, 'application/pdf');
        
        $requestData = [
            'nama_lengkap' => 'Andi Wijaya',
            'nim' => '654321',
            'email' => 'andi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '08987654321',
            'pilihan_dinas_1' => 1,
            'pilihan_dinas_2' => null,
            'motivasi' => 'Saya ingin belajar',
            'pengalaman' => 'Belum ada pengalaman'
        ];

        $request = $this->createMockRequest($requestData, [
            'file_cv' => $cvFile,
            'file_transkrip' => $transkripFile
        ]);

        // Mock User
        $userMock = Mockery::mock('alias:App\Models\User');
        $userInstance = Mockery::mock();
        $userInstance->id = 2;
        $userInstance->nama_lengkap = 'Andi Wijaya';
        $userInstance->email = 'andi@example.com';
        $userInstance->nim = '654321';
        $userInstance->no_telp = '08987654321';
        $userInstance->role = 'mahasiswa';
        
        $userMock->shouldReceive('create')->once()->andReturn($userInstance);

        // Mock InfoOr
        $infoOrMock = Mockery::mock('alias:App\Models\InfoOr');
        $infoOrInstance = Mockery::mock();
        $infoOrInstance->id = 1;
        $infoOrInstance->status = 'buka';
        
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('orderBy')->with('tanggal_buka', 'desc')->andReturnSelf();
        $queryBuilder->shouldReceive('first')->andReturn($infoOrInstance);
        
        $infoOrMock->shouldReceive('where')->with('status', 'buka')->andReturn($queryBuilder);

        // Mock Pendaftaran
        $pendaftaranMock = Mockery::mock('alias:App\Models\Pendaftaran');
        $pendaftaranInstance = Mockery::mock();
        $pendaftaranInstance->id = 2;
        
        $pendaftaranMock->shouldReceive('create')->once()->andReturn($pendaftaranInstance);

        $controller = new PendaftarController();
        $response = $controller->create($request);

        $this->assertEquals(201, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Andi Wijaya', $data['data']['nama_lengkap']);
    }

    /** @test */
    public function updateStatus_berhasil_update_status()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('validate')->once()->andReturn(['status' => 'lulus_wawancara']);
        $request->status = 'lulus_wawancara';

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran');
        $pendaftaranInstance = Mockery::mock();
        $pendaftaranMock->shouldReceive('where->first')->andReturn($pendaftaranInstance);
        $pendaftaranInstance->shouldReceive('update')
                           ->once()
                           ->with(['status_pendaftaran' => 'lulus_wawancara']);

        $controller = new PendaftarController();
        $response = $controller->updateStatus($request, 1);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $session = $response->getSession();
        $this->assertNotNull($session);
        $this->assertTrue($session->has('success'));
    }

    /** @test */
    public function updateStatus_gagal_pendaftaran_tidak_ditemukan()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('validate')->once()->andReturn(['status' => 'lulus_wawancara']);

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran');
        $pendaftaranMock->shouldReceive('where->first')->andReturn(null);

        $controller = new PendaftarController();
        $response = $controller->updateStatus($request, 999);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $session = $response->getSession();
        $this->assertNotNull($session);
        $this->assertTrue($session->has('error'));
    }

    /** @test */
    public function setDinasDiterima_hanya_bisa_untuk_lulus_wawancara()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('validate')->once()->andReturn(['dinas_diterima_id' => 1]);
        $request->dinas_diterima_id = 1;

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran');
        $pendaftaranInstance = Mockery::mock();
        $pendaftaranInstance->status_pendaftaran = 'terdaftar';
        $pendaftaranMock->shouldReceive('where->first')->andReturn($pendaftaranInstance);

        $controller = new PendaftarController();
        $response = $controller->setDinasDiterima($request, 1);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $session = $response->getSession();
        $this->assertNotNull($session);
        $this->assertTrue($session->has('error'));
    }

    /** @test */
    public function setDinasDiterima_berhasil_untuk_lulus_wawancara()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('validate')->once()->andReturn(['dinas_diterima_id' => 1]);
        $request->dinas_diterima_id = 1;

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran');
        $pendaftaranInstance = Mockery::mock();
        $pendaftaranInstance->status_pendaftaran = 'lulus_wawancara';
        
        $pendaftaranMock->shouldReceive('where->first')->andReturn($pendaftaranInstance);
        $pendaftaranInstance->shouldReceive('update')
                           ->once()
                           ->with(['dinas_diterima_id' => 1]);

        $controller = new PendaftarController();
        $response = $controller->setDinasDiterima($request, 1);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $session = $response->getSession();
        $this->assertNotNull($session);
        $this->assertTrue($session->has('success'));
    }

    /** @test */
    public function setDinasDiterima_gagal_pendaftaran_tidak_ditemukan()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('validate')->once()->andReturn(['dinas_diterima_id' => 1]);

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran');
        $pendaftaranMock->shouldReceive('where->first')->andReturn(null);

        $controller = new PendaftarController();
        $response = $controller->setDinasDiterima($request, 999);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $session = $response->getSession();
        $this->assertNotNull($session);
        $this->assertTrue($session->has('error'));
    }

    /** @test */
    public function viewCV_menampilkan_file_cv()
    {
        $filePath = 'pendaftaran/cv/cv.pdf';
        $fullPath = storage_path('app/public/' . $filePath);

        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        
        $pdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\ntrailer\n<<\n/Root 1 0 R\n>>\nstartxref\n0\n%%EOF";
        file_put_contents($fullPath, $pdfContent);

        $pendaftaranMock = (object) ['file_cv' => $filePath];

        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('findOrFail')->andReturn(
            (object) ['pendaftaran' => collect([$pendaftaranMock])]
        );

        $controller = new PendaftarController();
        $response = $controller->viewCV(1);

        $this->assertEquals(200, $response->getStatusCode());

        @unlink($fullPath);
    }

    /** @test */
    public function viewCV_gagal_file_tidak_ditemukan()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $pendaftaranMock = (object) ['file_cv' => 'pendaftaran/cv/notfound.pdf'];

        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('findOrFail')->andReturn(
            (object) ['pendaftaran' => collect([$pendaftaranMock])]
        );

        $controller = new PendaftarController();
        $controller->viewCV(1);
    }

    /** @test */
    public function viewTranskrip_menampilkan_file_transkrip()
    {
        $filePath = 'pendaftaran/transkrip/transkrip.pdf';
        $fullPath = storage_path('app/public/' . $filePath);

        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        file_put_contents($fullPath, 'dummy transkrip content');

        $pendaftaranMock = (object) ['file_transkrip' => $filePath];

        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('findOrFail')->andReturn(
            (object) ['pendaftaran' => collect([$pendaftaranMock])]
        );

        $controller = new PendaftarController();
        $response = $controller->viewTranskrip(1);

        $this->assertEquals(200, $response->getStatusCode());

        @unlink($fullPath);
    }

    /** @test */
    public function viewTranskrip_gagal_file_tidak_ditemukan()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $pendaftaranMock = (object) ['file_transkrip' => 'pendaftaran/transkrip/notfound.pdf'];

        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('findOrFail')->andReturn(
            (object) ['pendaftaran' => collect([$pendaftaranMock])]
        );

        $controller = new PendaftarController();
        $controller->viewTranskrip(1);
    }

    /** @test */
    public function show_menampilkan_detail_pendaftar()
    {
        $pendaftarData = (object)[
            'id' => 1,
            'nama_lengkap' => 'Budi Santoso',
            'nim' => '123456',
            'email' => 'budi@example.com',
            'role' => 'mahasiswa',
            'pendaftaran' => collect([
                (object)[
                    'id' => 1,
                    'status_pendaftaran' => 'terdaftar',
                    'motivasi' => 'Test motivasi'
                ]
            ])
        ];

        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('with->where->findOrFail')
                 ->with(1)
                 ->andReturn($pendaftarData);

        $controller = new PendaftarController();
        $response = $controller->show(1);

        $viewData = $response->getData();
        $this->assertArrayHasKey('pendaftar', $viewData);
        $this->assertEquals('Budi Santoso', $viewData['pendaftar']->nama_lengkap);
    }
}