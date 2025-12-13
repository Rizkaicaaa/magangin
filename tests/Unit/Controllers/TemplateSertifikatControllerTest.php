<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\TemplateSertifikatController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini

class TemplateSertifikatControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new TemplateSertifikatController();
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * ✓ Test case: index() mengembalikan view dengan data infoOrList
     */
    public function test_index_mengembalikan_view_dengan_data_info_or()
    {
        // Arrange - Mock InfoOr model
        $mockInfoOr = Mockery::mock('alias:App\Models\InfoOr');
        $mockInfoOr->shouldReceive('get')
            ->once()
            ->andReturn(collect([
                (object)['id' => 1, 'nama_or' => 'Info OR 1'],
                (object)['id' => 2, 'nama_or' => 'Info OR 2'],
            ]));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('template-sertifikat.index', $response->name());
        $this->assertArrayHasKey('infoOrList', $response->getData());
        $this->assertCount(2, $response->getData()['infoOrList']);
    }

    /**
     * ✓ Test case: store() berhasil dengan data valid
     */
    public function test_store_berhasil_dengan_data_valid()
    {
        // Arrange - Mock Storage
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        
        Storage::shouldReceive('makeDirectory')
            ->with('templates_sertifikat')
            ->once()
            ->andReturn(true);

        // Mock file
        $mockFile = Mockery::mock(UploadedFile::class);
        $mockFile->shouldReceive('getClientOriginalExtension')
            ->andReturn('html');
        $mockFile->shouldReceive('storeAs')
            ->with('templates_sertifikat', 'template_test.html', 'public')
            ->andReturn('templates_sertifikat/template_test.html');

        // Mock Request - PENTING: Gunakan nama model yang benar TemplateSertifikatModel
        $request = Request::create('/template-sertifikat', 'POST', [
            'nama_template' => 'Template Test',
            'info_or_id' => 1
        ]);
        
        $requestMock = Mockery::mock($request)->makePartial();
        $requestMock->shouldReceive('validate')
            ->once()
            ->andReturn(true);
        
        $requestMock->shouldReceive('file')
            ->with('file_template')
            ->andReturn($mockFile);

        // Mock TemplateSertifikatModel (NAMA YANG BENAR!)
        $mockTemplate = Mockery::mock('alias:App\Models\TemplateSertifikatModel');
        $mockTemplate->shouldReceive('create')
            ->once()
            ->with([
                'info_or_id' => 1,
                'nama_template' => 'Template Test',
                'file_template' => 'templates_sertifikat/template_test.html',
                'status' => 'aktif',
            ])
            ->andReturn((object)['id' => 1]);

        // Act
        $response = $this->controller->store($requestMock);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * ✓ Test case: store() gagal jika file bukan format HTML
     */
    public function test_store_gagal_jika_file_bukan_html()
    {
        // Arrange - Mock file dengan extension PDF
        $mockFile = Mockery::mock(UploadedFile::class);
        $mockFile->shouldReceive('getClientOriginalExtension')
            ->andReturn('pdf');

        // Mock Request
        $request = Request::create('/template-sertifikat', 'POST', [
            'nama_template' => 'Template Test',
            'info_or_id' => 1
        ]);
        
        $requestMock = Mockery::mock($request)->makePartial();
        $requestMock->shouldReceive('validate')
            ->once()
            ->andReturn(true);
        
        $requestMock->shouldReceive('file')
            ->with('file_template')
            ->andReturn($mockFile);

        // Act
        $response = $this->controller->store($requestMock);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * ✓ Test case: store() membuat nama file dengan format yang benar
     */
    public function test_store_membuat_nama_file_dengan_format_benar()
    {
        // Arrange - Mock Storage
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        
        Storage::shouldReceive('makeDirectory')
            ->with('templates_sertifikat')
            ->once()
            ->andReturn(true);

        // Mock file
        $mockFile = Mockery::mock(UploadedFile::class);
        $mockFile->shouldReceive('getClientOriginalExtension')
            ->andReturn('html');
        $mockFile->shouldReceive('storeAs')
            ->with('templates_sertifikat', 'template_dengan_spasi.html', 'public')
            ->andReturn('templates_sertifikat/template_dengan_spasi.html');

        // Mock Request dengan nama spasi
        $request = Request::create('/template-sertifikat', 'POST', [
            'nama_template' => 'Template Dengan Spasi',
            'info_or_id' => 1
        ]);
        
        $requestMock = Mockery::mock($request)->makePartial();
        $requestMock->shouldReceive('validate')
            ->once()
            ->andReturn(true);
        
        $requestMock->shouldReceive('file')
            ->with('file_template')
            ->andReturn($mockFile);

        // Mock TemplateSertifikatModel - NAMA YANG BENAR!
        $mockTemplate = Mockery::mock('alias:App\Models\TemplateSertifikatModel');
        $mockTemplate->shouldReceive('create')
            ->once()
            ->andReturn((object)['id' => 1]);

        // Act
        $response = $this->controller->store($requestMock);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * ✓ Test case: store() menyimpan data dengan status 'aktif'
     */
    public function test_store_menyimpan_data_dengan_status_aktif()
    {
        // Arrange - Mock Storage
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        
        Storage::shouldReceive('makeDirectory')
            ->andReturn(true);

        // Mock file
        $mockFile = Mockery::mock(UploadedFile::class);
        $mockFile->shouldReceive('getClientOriginalExtension')
            ->andReturn('html');
        $mockFile->shouldReceive('storeAs')
            ->andReturn('templates_sertifikat/test.html');

        // Mock Request
        $request = Request::create('/template-sertifikat', 'POST', [
            'nama_template' => 'Test',
            'info_or_id' => 1
        ]);
        
        $requestMock = Mockery::mock($request)->makePartial();
        $requestMock->shouldReceive('validate')->andReturn(true);
        $requestMock->shouldReceive('file')->andReturn($mockFile);

        // Mock TemplateSertifikatModel - NAMA YANG BENAR! + assert status aktif
        $mockTemplate = Mockery::mock('alias:App\Models\TemplateSertifikatModel');
        $mockTemplate->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['status'] === 'aktif';
            }))
            ->andReturn((object)['id' => 1]);

        // Act
        $response = $this->controller->store($requestMock);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * ✓ Test case: store() membuat directory jika belum ada
     */
    public function test_store_membuat_directory_jika_belum_ada()
    {
        // Arrange - Mock Storage dengan assertion makeDirectory dipanggil
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        
        Storage::shouldReceive('makeDirectory')
            ->with('templates_sertifikat')
            ->once()
            ->andReturn(true);

        // Mock file
        $mockFile = Mockery::mock(UploadedFile::class);
        $mockFile->shouldReceive('getClientOriginalExtension')
            ->andReturn('html');
        $mockFile->shouldReceive('storeAs')
            ->andReturn('templates_sertifikat/test.html');

        // Mock Request
        $request = Request::create('/template-sertifikat', 'POST', [
            'nama_template' => 'Test',
            'info_or_id' => 1
        ]);
        
        $requestMock = Mockery::mock($request)->makePartial();
        $requestMock->shouldReceive('validate')->andReturn(true);
        $requestMock->shouldReceive('file')->andReturn($mockFile);

        // Mock TemplateSertifikatModel - NAMA YANG BENAR!
        $mockTemplate = Mockery::mock('alias:App\Models\TemplateSertifikatModel');
        $mockTemplate->shouldReceive('create')
            ->andReturn((object)['id' => 1]);

        // Act
        $this->controller->store($requestMock);

        // Assert - makeDirectory()->once() sudah di-assert di atas
        $this->assertTrue(true);
    }
}