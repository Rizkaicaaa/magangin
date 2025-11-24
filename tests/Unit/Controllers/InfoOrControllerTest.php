<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\InfoOrController;
use App\Models\InfoOr;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses] // Diterapkan di Tingkat Kelas untuk mengatasi Fatal Error: Cannot redeclare
class InfoOrControllerTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    protected $infoOrModelMock;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Mock InfoOr model menggunakan alias (Mocking statis untuk orderBy, create, findOrFail)
        $this->infoOrModelMock = Mockery::mock('alias:' . InfoOr::class);

        // 2. Mock Validator untuk bypass validasi
        // Kita tidak lagi memanggil Validator::shouldReceive('make') di sini, 
        // karena di metode store kita akan memalsukan $request->validate() langsung.
        
        // 3. Mock Storage untuk file upload
        Storage::shouldReceive('disk')->andReturnSelf();
    }

    protected function tearDown(): void
    {
        // Pastikan Mockery ditutup dan dibersihkan
        Mockery::close();
        parent::tearDown();
    }
    
    // ==================== INDEX METHOD ====================

    /** @test */
    #[\PHPUnit\Framework\Attributes\Test]
    public function index_menampilkan_data_terurut_descending_dan_status_buka_benar()
    {
        // Arrange
        $mockInfoOrs = collect([
            (object)['id' => 3, 'status' => 'buka'],
            (object)['id' => 2, 'status' => 'tutup'],
            (object)['id' => 1, 'status' => 'tutup'],
        ]);
        
        $mockQueryBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);

        $this->infoOrModelMock
            ->shouldReceive('orderBy')
            ->once()
            ->with('id', 'desc')
            ->andReturn($mockQueryBuilder);
        
        $mockQueryBuilder->shouldReceive('get')
            ->once()
            ->andReturn($mockInfoOrs);

        $controller = new InfoOrController();

        // Act
        $response = $controller->index();

        // Assert
        $this->assertEquals('info_or.index', $response->name());
        $this->assertEquals($mockInfoOrs, $response->getData()['infoOrs']);
        $this->assertTrue($response->getData()['isInfoOpen']); 
    }

    /** @test */
    #[\PHPUnit\Framework\Attributes\Test]
    public function index_menampilkan_data_terurut_dan_info_status_tutup_jika_semua_tutup()
    {
        // Arrange
        $mockInfoOrs = collect([
            (object)['id' => 3, 'status' => 'tutup'],
            (object)['id' => 2, 'status' => 'tutup'],
        ]);

        $mockQueryBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        
        $this->infoOrModelMock
            ->shouldReceive('orderBy')
            ->once()
            ->andReturn($mockQueryBuilder);
        
        $mockQueryBuilder->shouldReceive('get')
            ->once()
            ->andReturn($mockInfoOrs);

        $controller = new InfoOrController();

        // Act
        $response = $controller->index();

        // Assert
        $this->assertFalse($response->getData()['isInfoOpen']); 
    }


    // ==================== STORE METHOD ====================

    /** @test */
    #[\PHPUnit\Framework\Attributes\Test]
    public function store_berhasil_menyimpan_info_or_dengan_gambar_dan_redirect()
    {
        // Data yang dikirimkan ke Request (semua field terisi)
        $requestInput = [
            'judul' => 'Open Recruitment BEM',
            'deskripsi' => 'Deskripsi panjang OR BEM',
            'persyaratan_umum' => 'Wajib Mahasiswa', 
            'tanggal_buka' => '2025-11-01',
            'tanggal_tutup' => '2025-11-30',
            'periode' => '2025/2026',
            'status' => 'buka',
            'gambar' => 'dummy_file_name.jpg', // Dummy untuk validator
        ];
        
        $uploadedFileName = 'test_image_hash.jpg';
        
        // Data yang diharapkan diterima oleh InfoOr::create() setelah file diproses
        $expectedCreationDataSubset = [
            'judul' => 'Open Recruitment BEM',
            'deskripsi' => 'Deskripsi panjang OR BEM',
            'persyaratan_umum' => 'Wajib Mahasiswa', 
            'tanggal_buka' => '2025-11-01',
            'tanggal_tutup' => '2025-11-30',
            'periode' => '2025/2026', 
            'status' => 'buka',
            'gambar' => 'images/' . $uploadedFileName, 
        ];

        // 1. Mock UploadedFile 
        $mockFileInstance = Mockery::mock(UploadedFile::class)->makePartial();
        $mockFileInstance->shouldReceive('isValid')->andReturn(true); 
        $mockFileInstance->shouldReceive('getClientOriginalExtension')->andReturn('jpg');
        $mockFileInstance->shouldReceive('getClientMimeType')->andReturn('image/jpeg');
        $mockFileInstance->shouldReceive('hashName')->andReturn($uploadedFileName);
        $mockFileInstance->shouldReceive('storeAs')
            ->once()
            ->with('', $uploadedFileName, 'gambar_public')
            ->andReturn($uploadedFileName); 

        // 2. Mock Objek Request (Partial Mock)
        $request = Mockery::mock(Request::class);
        
        // Data yang divalidasi dan akan digunakan untuk create (semua field dari input)
        $validatedData = $requestInput;
        // Data $data setelah $request->except('gambar') dijalankan di controller
        $dataWithoutFile = $requestInput;
        unset($dataWithoutFile['gambar']);
        
        $request->shouldReceive('validate')->andReturn($validatedData);
        $request->shouldReceive('except')->with('gambar')->andReturn($dataWithoutFile);
        $request->shouldReceive('hasFile')->with('gambar')->andReturn(true);
        $request->shouldReceive('file')->with('gambar')->andReturn($mockFileInstance);

        // 3. Mock Panggilan Statis InfoOr::create
        $this->infoOrModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::subset($expectedCreationDataSubset))
            ->andReturn((object)['id' => 1]);

        $controller = new InfoOrController();

        // Act
        $response = $controller->store($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('Info OR berhasil ditambahkan!', session('success'));
    }
    
    /** @test */
    #[\PHPUnit\Framework\Attributes\Test]
    public function store_berhasil_menyimpan_data_tanpa_persyaratan_umum_dan_periode()
    {
        // Data yang dikirim (tanpa field nullable)
        $requestInput = [
            'judul' => 'Info Minimal',
            'deskripsi' => 'Deskripsi',
            'tanggal_buka' => '2025-11-01',
            'tanggal_tutup' => '2025-11-30',
            'gambar' => 'dummy_file_name.png',
            'status' => 'buka',
        ];
        
        $uploadedFileName = 'minimal.png';
        
        // Data yang diharapkan diterima oleh InfoOr::create()
        // Kita harus menyertakan NULL untuk field nullable yang tidak ada di request,
        // karena validator Laravel akan mengembalikannya sebagai NULL.
        $expectedCreationDataSubset = [
            'judul' => 'Info Minimal',
            'deskripsi' => 'Deskripsi',
            'persyaratan_umum' => null, // Harus ada
            'tanggal_buka' => '2025-11-01',
            'tanggal_tutup' => '2025-11-30',
            'periode' => null, // Harus ada
            'status' => 'buka',
            'gambar' => 'images/' . $uploadedFileName,
        ];

        // 1. Mock UploadedFile
        $mockFileInstance = Mockery::mock(UploadedFile::class)->makePartial();
        $mockFileInstance->shouldReceive('isValid')->andReturn(true); 
        $mockFileInstance->shouldReceive('getClientOriginalExtension')->andReturn('png');
        $mockFileInstance->shouldReceive('getClientMimeType')->andReturn('image/png');
        
        $mockFileInstance->shouldReceive('hashName')->andReturn($uploadedFileName);
        $mockFileInstance->shouldReceive('storeAs')
            ->once()
            ->with('', $uploadedFileName, 'gambar_public')
            ->andReturn($uploadedFileName);

        // 2. Mock Objek Request
        $request = Mockery::mock(Request::class);
        
        // Data yang divalidasi (dengan NULL untuk field nullable yang hilang)
        $validatedData = array_merge($requestInput, ['persyaratan_umum' => null, 'periode' => null]);
        
        // Data $data setelah $request->except('gambar') dijalankan di controller
        $dataWithoutFile = $validatedData;
        unset($dataWithoutFile['gambar']);
        
        $request->shouldReceive('validate')->andReturn($validatedData);
        $request->shouldReceive('except')->with('gambar')->andReturn($dataWithoutFile);
        $request->shouldReceive('hasFile')->with('gambar')->andReturn(true);
        $request->shouldReceive('file')->with('gambar')->andReturn($mockFileInstance);
        
        // 3. Mock Panggilan Statis InfoOr::create
        $this->infoOrModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::subset($expectedCreationDataSubset))
            ->andReturn((object)['id' => 2]);

        $controller = new InfoOrController();

        // Act
        $response = $controller->store($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('Info OR berhasil ditambahkan!', session('success'));
    }
    
    // ==================== UPDATE STATUS METHOD ====================

    /** @test */
     // ==================== UPDATE STATUS METHOD ====================


    #[\PHPUnit\Framework\Attributes\Test]
    public function updateStatus_berhasil_mengubah_status_info_or_menjadi_tutup()
    {
        // Arrange
        $infoOrId = 10;
       
        // Perbaikan: Gunakan Mockery::mock(InfoOr::class) untuk memastikan mock memiliki
        // properti dan method Eloquent, seperti save().
        $mockInfoOr = Mockery::mock('InfoOr'::class);
        $mockInfoOr->id = $infoOrId;
        $mockInfoOr->status = 'buka'; // Status awal
       
        // 1. Mock findOrFail
        $this->infoOrModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($infoOrId)
            ->andReturn($mockInfoOr);
       
        // 2. Mock save() dan verifikasi status diubah menjadi 'tutup'
        // Kita tidak perlu menggunakan Mockery::on() di save(), cukup pastikan save() dipanggil
        // dan verifikasi bahwa status telah diubah pada objek mock (di baris berikutnya).
        $mockInfoOr->shouldReceive('save')
                   ->once()
                   ->andReturn(true);




        $controller = new InfoOrController();


        // Act
        $response = $controller->updateStatus($infoOrId);
       
        // Assert: Verifikasi perubahan status pada objek mock setelah controller dieksekusi
        $this->assertEquals('tutup', $mockInfoOr->status);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('Info OR berhasil ditutup!', session('success'));
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function updateStatus_mencari_infoOr_dengan_findOrFail_dan_memanggil_save()
    {
        // Arrange
        $infoOrId = 20;


        // Perbaikan: Gunakan Mockery::mock(InfoOr::class)
        $mockInfoOr = Mockery::mock('InfoOr'::class);
        $mockInfoOr->id = $infoOrId;
        $mockInfoOr->status = 'buka';
       
        // Verifikasi bahwa save dipanggil
        $mockInfoOr->shouldReceive('save')->once()->andReturn(true);


        // Verifikasi bahwa findOrFail dipanggil
        $this->infoOrModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($infoOrId)
            ->andReturn($mockInfoOr);


        $controller = new InfoOrController();


        // Act
        $controller->updateStatus($infoOrId);


        // Assert - Mockery sudah memverifikasi semua panggilan
        $this->assertTrue(true);
    }
}
