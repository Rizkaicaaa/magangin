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

class TemplateSertifikatControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new TemplateSertifikatController();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test case: index() mengembalikan view dengan data infoOrList
     */
    public function test_index_mengembalikan_view_dengan_data_info_or()
    {
        // Mock InfoOr model
        $mockInfoOr = Mockery::mock('alias:App\Models\InfoOr');
        $mockInfoOr->shouldReceive('get')
            ->once()
            ->andReturn(collect([
                (object)['id' => 1, 'nama' => 'Info OR 1'],
                (object)['id' => 2, 'nama' => 'Info OR 2'],
            ]));

        // Panggil method index
        $response = $this->controller->index();

        // Assert response adalah view dengan data yang benar
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('template-sertifikat.index', $response->name());
        $this->assertArrayHasKey('infoOrList', $response->getData());
        $this->assertCount(2, $response->getData()['infoOrList']);
    }

    /**
     * Test case: store() berhasil dengan data valid
     */
    public function test_store_berhasil_dengan_data_valid()
    {
        // Mock Storage facade
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        
        Storage::shouldReceive('makeDirectory')
            ->with('templates_sertifikat')
            ->once()
            ->andReturn(true);

        // Mock file upload
        $mockFile = Mockery::mock(UploadedFile::class);
        $mockFile->shouldReceive('getClientOriginalExtension')
            ->andReturn('html');
        $mockFile->shouldReceive('storeAs')
            ->with('templates_sertifikat', 'template_test.html', 'public')
            ->andReturn('templates_sertifikat/template_test.html');

        // Mock Request dengan semua method yang dibutuhkan
        $request = Request::create('/template-sertifikat', 'POST', [
            'nama_template' => 'Template Test',
            'info_or_id' => 1
        ]);
        
        $requestMock = Mockery::mock($request)->makePartial();
        $requestMock->shouldReceive('validate')
            ->once()
            ->with([
                'nama_template' => 'required|string|max:255',
                'file_template' => 'required|file|max:2048',
                'info_or_id' => 'required|integer|exists:info_or,id',
            ])
            ->andReturn(true);
        
        $requestMock->shouldReceive('file')
            ->with('file_template')
            ->andReturn($mockFile);

        // Mock TemplateSertifikat model
        $mockTemplateSertifikat = Mockery::mock('alias:App\Models\TemplateSertifikat');
        $mockTemplateSertifikat->shouldReceive('create')
            ->once()
            ->with([
                'info_or_id' => 1,
                'nama_template' => 'Template Test',
                'file_template' => 'templates_sertifikat/template_test.html',
                'status' => 'aktif',
            ])
            ->andReturn((object)[
                'id' => 1,
                'info_or_id' => 1,
                'nama_template' => 'Template Test',
                'file_template' => 'templates_sertifikat/template_test.html',
                'status' => 'aktif',
            ]);

        // Panggil method store
        $response = $this->controller->store($requestMock);

        // Assert redirect dengan success message
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('Template sertifikat berhasil diupload!', session('success'));
    }

    /**
     * Test case: store() gagal jika file bukan format HTML
     */
    public function test_store_gagal_jika_file_bukan_html()
    {
        // Mock file upload dengan ekstensi salah
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

        // Panggil method store
        $response = $this->controller->store($requestMock);

        // Assert redirect back dengan error
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test case: store() membuat nama file dengan format yang benar
     */
    public function test_store_membuat_nama_file_dengan_format_benar()
    {
        // Mock Storage facade
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        
        Storage::shouldReceive('makeDirectory')
            ->with('templates_sertifikat')
            ->once()
            ->andReturn(true);

        // Mock file upload
        $mockFile = Mockery::mock(UploadedFile::class);
        $mockFile->shouldReceive('getClientOriginalExtension')
            ->andReturn('html');
        $mockFile->shouldReceive('storeAs')
            ->with('templates_sertifikat', 'template_dengan_spasi.html', 'public')
            ->andReturn('templates_sertifikat/template_dengan_spasi.html');

        // Mock Request dengan nama yang ada spasi
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

        // Mock TemplateSertifikat model
        $mockTemplateSertifikat = Mockery::mock('alias:App\Models\TemplateSertifikat');
        $mockTemplateSertifikat->shouldReceive('create')
            ->once()
            ->andReturn((object)['id' => 1]);

        // Panggil method store
        $response = $this->controller->store($requestMock);

        // Assert berhasil
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test case: store() menyimpan data dengan status 'aktif'
     */
    public function test_store_menyimpan_data_dengan_status_aktif()
    {
        // Mock Storage facade
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        
        Storage::shouldReceive('makeDirectory')
            ->andReturn(true);

        // Mock file upload
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

        // Mock TemplateSertifikat dengan assertion pada status
        $mockTemplateSertifikat = Mockery::mock('alias:App\Models\TemplateSertifikat');
        $mockTemplateSertifikat->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['status'] === 'aktif';
            }))
            ->andReturn((object)['id' => 1]);

        // Panggil method store
        $response = $this->controller->store($requestMock);

        // Assert berhasil
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test case: store() membuat directory jika belum ada
     */
    public function test_store_membuat_directory_jika_belum_ada()
    {
        // Mock Storage untuk memastikan makeDirectory dipanggil
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        
        Storage::shouldReceive('makeDirectory')
            ->with('templates_sertifikat')
            ->once()
            ->andReturn(true);

        // Mock file upload
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

        // Mock TemplateSertifikat
        $mockTemplateSertifikat = Mockery::mock('alias:App\Models\TemplateSertifikat');
        $mockTemplateSertifikat->shouldReceive('create')
            ->andReturn((object)['id' => 1]);

        // Panggil method store
        $this->controller->store($requestMock);

        // Assertion sudah dilakukan di shouldReceive()->once()
        $this->assertTrue(true);
    }
}