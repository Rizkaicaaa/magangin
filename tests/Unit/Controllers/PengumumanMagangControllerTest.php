<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; // Menggantikan fungsi global file_*
use Illuminate\Support\Facades\DB;   // Untuk mencegah QueryException jika ada interaksi DB tak terduga
use Illuminate\Support\Facades\Session; // Digunakan untuk mem-mock flashing session
use PHPUnit\Framework\Attributes\Test;
use App\Http\Controllers\PengumumanMagangController;
use Illuminate\Session\SessionManager; // Untuk mock manager
use Illuminate\Session\Store; // Untuk mock store
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Diperlukan untuk mengatasi Mockery Fatal Error

#[RunTestsInSeparateProcesses] // Tambahkan ini di setiap Controller Test yang menggunakan Mockery Overload
class PengumumanMagangControllerTest extends TestCase
{
    protected $evaluasiMock;
    protected $templateMock;
    protected $pdfFacadeMock;
    protected $sessionStoreMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mocking database untuk mencegah QueryException tak terduga
        DB::shouldReceive('connection')->andReturnSelf();
        DB::shouldReceive('statement')->andReturn(true);

        // FIX 1: Mock DB::table() untuk menangani validasi 'exists:table,id'
        $dbTableMock = Mockery::mock();
        $dbTableMock->shouldReceive('useWritePdo')->andReturnSelf();
        $dbTableMock->shouldReceive('where')->andReturnSelf();
        $dbTableMock->shouldReceive('count')->andReturn(1); // Menganggap ID selalu ada
        
        DB::shouldReceive('table')->andReturn($dbTableMock);
        
        // Mock semua Model yang digunakan
        $this->evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel');
        $this->templateMock = Mockery::mock('overload:App\Models\TemplateSertifikatModel');

        // Mock Facade Pdf
        $this->pdfFacadeMock = Mockery::mock('alias:Barryvdh\DomPDF\Facade\Pdf');

        // --- FIX SESSION MOCKING ---
        // 1. Buat Mock Session Store yang menangani flash dan get
        $this->sessionStoreMock = Mockery::mock(Store::class);
        $this->sessionStoreMock->shouldReceive('flash')->zeroOrMoreTimes();
        $this->sessionStoreMock->shouldReceive('get')->zeroOrMoreTimes(); // Default: selalu siap dipanggil
        
        // 2. FIX BAD METHOD CALL: previousUrl() dipanggil pada SessionManager/Facade
        Session::shouldReceive('previousUrl')->zeroOrMoreTimes()->andReturn('http://previous');

        // 3. Mock driver() untuk mengembalikan mock store
        Session::shouldReceive('driver')->zeroOrMoreTimes()->andReturn($this->sessionStoreMock);

        // --- END FIX SESSION MOCKING ---
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function fakeRequest(array $data)
    {
        return Request::create('/', 'POST', $data);
    }

    // --- Helper untuk Mocking File Operations ---
    protected function mockFileOperations(bool $templateExists, string $templateContent, bool $ttdExists = true)
    {
        // 1. Mock File::exists() untuk Template Path (storage_path) dan Tanda Tangan (public_path)
        File::shouldReceive('exists')
            ->with(Mockery::pattern('/.*\/app\/public\/(templates|sertifikat)\/.*/')) // Mencakup template dan ttd
            ->andReturnUsing(function ($path) use ($templateExists, $ttdExists) {
                // Tentukan apakah ini path template atau ttd
                if (str_contains($path, 'ttd')) {
                    // Path tanda tangan (public_path)
                    return $ttdExists;
                }
                // Path template (storage_path)
                return $templateExists;
            });

        // 2. Mock File::get() untuk Template Content
        if ($templateExists) {
             File::shouldReceive('get')
                 ->with(Mockery::pattern('/.*\/app\/public\/templates\/.*/'))
                 ->andReturn($templateContent);
        }

        // 3. Mock File::get() untuk Tanda Tangan Content
        if (!!$ttdExists) { // memastikan ttdExists adalah boolean/truthy
            File::shouldReceive('get')
                ->with(Mockery::pattern('/.*\/public\/images\/ttd_.*/'))
                ->andReturn('dummy_signature_data'); 
        }
    }
    // --- End Helper ---

    // --- Testing index() ---

    #[Test]
    public function index_menampilkan_data_evaluasi_dan_template()
    {
        $controller = new PengumumanMagangController();

        // Mock EvaluasiMagangModel::with('pendaftaran.user')->latest()->get()
        $this->evaluasiMock->shouldReceive('with')->once()->with('pendaftaran.user')->andReturnSelf();
        $this->evaluasiMock->shouldReceive('latest')->once()->andReturnSelf();
        $this->evaluasiMock->shouldReceive('get')->once()->andReturn(collect(['evaluasi_1']));

        // Mock TemplateSertifikatModel::orderBy('created_at', 'desc')->get()
        $this->templateMock->shouldReceive('orderBy')->once()->with('created_at', 'desc')->andReturnSelf();
        $this->templateMock->shouldReceive('get')->once()->andReturn(collect(['template_A']));

        // FIX 2: Tambahkan Mockery::any() untuk parameter ke-3 (mergeData)
        View::shouldReceive('make')->once()->with('pengumuman-magang.index', Mockery::on(function ($data) {
            return count($data['evaluasiList']) == 1 && count($data['templates']) == 1;
        }), Mockery::any())->andReturnSelf();
        
        $controller->index();
        $this->assertTrue(true); // Verifikasi melalui Mockery
    }

    // --- Testing store() ---

    #[Test]
    public function store_berhasil_membuat_sertifikat_untuk_mahasiswa_lulus()
    {
        // Aktifkan mocking Facade File
        $this->mockFileOperations(true, '<html>{{nama_mahasiswa}}</html>', true);

        $controller = new PengumumanMagangController();
        $evaluasiId = 10;
        $nilaiTotal = 80; // Lulus karena >= 75

        $request = $this->fakeRequest([
            'nomor_sertifikat' => 'S-001/PPSI/',
            'template_id' => 5,
        ]);

        // 1. Mock Pendaftaran dan User Record
        $userRecord = Mockery::mock();
        $userRecord->nama_lengkap = 'Budi Santoso';
        
        $pendaftaranRecord = Mockery::mock();
        $pendaftaranRecord->id = 1;
        $pendaftaranRecord->user = $userRecord;
        $pendaftaranRecord->shouldReceive('update')
                          ->once()
                          ->with(['status' => 'lulus_magang'])
                          ->andReturn(true);

        // 2. Mock Evaluasi Record
        $evaluasiRecord = Mockery::mock();
        $evaluasiRecord->nilai_total = $nilaiTotal;
        $evaluasiRecord->pendaftaran = $pendaftaranRecord;
        $evaluasiRecord->shouldReceive('update')
                       ->once()
                       ->with(Mockery::subset(['hasil_evaluasi' => 'Lulus']));
        $evaluasiRecord->shouldReceive('update')
                       ->once()
                       ->with(Mockery::subset([
                           'nomor_sertifikat' => 'S-001/PPSI/1',
                           'file_sertifikat' => Mockery::type('string'),
                           'template_sertifikat_id' => 5,
                       ]))->andReturn(true);


        // 3. Mock EvaluasiMagangModel::with('...')->findOrFail()
        $this->evaluasiMock->shouldReceive('with')->andReturnSelf();
        $this->evaluasiMock->shouldReceive('findOrFail')->once()->with($evaluasiId)->andReturn($evaluasiRecord);
        
        // 4. Mock TemplateSertifikatModel::findOrFail()
        $templateRecord = Mockery::mock();
        $templateRecord->id = 5;
        $templateRecord->file_template = 'templates/sertifikat.html';
        $this->templateMock->shouldReceive('findOrFail')->once()->with(5)->andReturn($templateRecord);

        // 5. Mock PDF Generation
        $pdfMock = Mockery::mock();
        $pdfMock->shouldReceive('setPaper')->once()->andReturnSelf();
        $pdfMock->shouldReceive('setOptions')->once()->andReturnSelf();
        $pdfMock->shouldReceive('output')->once()->andReturn('pdf_binary_content');
        
        $this->pdfFacadeMock->shouldReceive('loadHTML')->once()->andReturn($pdfMock);

        // 6. Mock Storage (Hanya dipanggil pada success path)
        Storage::shouldReceive('disk')->once()->with('public')->andReturnSelf();
        Storage::shouldReceive('put')->once()->with(
            'sertifikat/sertifikat_budi_santoso.pdf', // Path ekspektasi
            'pdf_binary_content'
        );

        // 7. Mock Session Flash & Get
        $this->sessionStoreMock->shouldReceive('flash')->once()->with('success', 'Sertifikat PDF berhasil dibuat dan disimpan!');
        $this->sessionStoreMock->shouldReceive('get')->once()->with('success')->andReturn('Sertifikat PDF berhasil dibuat dan disimpan!');

        $response = $controller->store($request, $evaluasiId);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect(), 'Response should be a redirect.');
        $this->assertStringContainsString('berhasil dibuat dan disimpan!', $response->getSession()->get('success') ?? '');
    }

    #[Test]
    public function store_berhasil_membuat_sertifikat_untuk_mahasiswa_tidak_lulus()
    {
        // Aktifkan mocking Facade File
        $this->mockFileOperations(true, '<html>{{nama_mahasiswa}}</html>', true);

        $controller = new PengumumanMagangController();
        $evaluasiId = 20;
        $nilaiTotal = 60; // Tidak Lulus karena < 75

        $request = $this->fakeRequest([
            'nomor_sertifikat' => 'S-002/PPSI/',
            'template_id' => 6,
        ]);

        // 1. Mock Pendaftaran dan User Record
        $userRecord = Mockery::mock();
        $userRecord->nama_lengkap = 'Joko Susilo';
        
        $pendaftaranRecord = Mockery::mock();
        $pendaftaranRecord->id = 2;
        $pendaftaranRecord->user = $userRecord;
        $pendaftaranRecord->shouldReceive('update')
                          ->once()
                          ->with(['status' => 'tidak_lulus'])
                          ->andReturn(true);

        // 2. Mock Evaluasi Record
        $evaluasiRecord = Mockery::mock();
        $evaluasiRecord->nilai_total = $nilaiTotal;
        $evaluasiRecord->pendaftaran = $pendaftaranRecord;
        $evaluasiRecord->shouldReceive('update')
                       ->once()
                       ->with(Mockery::subset(['hasil_evaluasi' => 'Tidak Lulus']));
        $evaluasiRecord->shouldReceive('update')
                       ->once()
                       ->with(Mockery::subset([
                           'nomor_sertifikat' => 'S-002/PPSI/2',
                           'file_sertifikat' => Mockery::type('string'),
                           'template_sertifikat_id' => 6,
                       ]))->andReturn(true);


        // 3. Mock EvaluasiMagangModel::with('...')->findOrFail()
        $this->evaluasiMock->shouldReceive('with')->andReturnSelf();
        $this->evaluasiMock->shouldReceive('findOrFail')->once()->with($evaluasiId)->andReturn($evaluasiRecord);
        
        // 4. Mock TemplateSertifikatModel::findOrFail()
        $templateRecord = Mockery::mock();
        $templateRecord->id = 6;
        $templateRecord->file_template = 'templates/sertifikat_fail.html';
        $this->templateMock->shouldReceive('findOrFail')->once()->with(6)->andReturn($templateRecord);

        // 5. Mock PDF Generation
        $pdfMock = Mockery::mock();
        $pdfMock->shouldReceive('setPaper')->once()->andReturnSelf();
        $pdfMock->shouldReceive('setOptions')->once()->andReturnSelf();
        $pdfMock->shouldReceive('output')->once()->andReturn('pdf_binary_content');
        
        $this->pdfFacadeMock->shouldReceive('loadHTML')->once()->andReturn($pdfMock);

        // 6. Mock Storage (Hanya dipanggil pada success path)
        Storage::shouldReceive('disk')->once()->with('public')->andReturnSelf();
        Storage::shouldReceive('put')->once()->with(
            'sertifikat/sertifikat_joko_susilo.pdf',
            'pdf_binary_content'
        );

        // 7. Mock Session Flash & Get
        $this->sessionStoreMock->shouldReceive('flash')->once()->with('success', 'Sertifikat PDF berhasil dibuat dan disimpan!');
        $this->sessionStoreMock->shouldReceive('get')->once()->with('success')->andReturn('Sertifikat PDF berhasil dibuat dan disimpan!');


        $response = $controller->store($request, $evaluasiId);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect(), 'Response should be a redirect.');
        $this->assertStringContainsString('berhasil dibuat dan disimpan!', $response->getSession()->get('success') ?? '');
    }

    #[Test]
    public function store_gagal_karena_template_html_tidak_ditemukan()
    {
        // Aktifkan mocking Facade File (templateExists = false)
        $this->mockFileOperations(false, '<html>Gagal</html>');

        $controller = new PengumumanMagangController();
        $evaluasiId = 30;

        $request = $this->fakeRequest([
            'nomor_sertifikat' => 'S-003/PPSI/',
            'template_id' => 7,
        ]);

        // 1. Mock Pendaftaran dan User Record (diperlukan untuk findOrFail)
        $userRecord = Mockery::mock();
        $userRecord->nama_lengkap = 'Dummy User';
        
        $pendaftaranRecord = Mockery::mock();
        $pendaftaranRecord->id = 3;
        $pendaftaranRecord->user = $userRecord;
        // FIX 3: Tambahkan update expectation yang terlewat untuk pendaftaran
        $pendaftaranRecord->shouldReceive('update')
                          ->once()
                          ->with(['status' => 'tidak_lulus']) // Asumsi nilainya < 75
                          ->andReturn(true);


        // 2. Mock Evaluasi Record
        $evaluasiRecord = Mockery::mock();
        $evaluasiRecord->nilai_total = 60; // Ganti ke nilai < 75 agar statusnya 'tidak_lulus'
        $evaluasiRecord->pendaftaran = $pendaftaranRecord;
        // FIX 3: Tambahkan update expectation yang terlewat untuk evaluasi
        $evaluasiRecord->shouldReceive('update')
                       ->once()
                       ->with(Mockery::subset(['hasil_evaluasi' => 'Tidak Lulus']));


        // 3. Mock EvaluasiMagangModel::with('...')->findOrFail()
        $this->evaluasiMock->shouldReceive('with')->andReturnSelf();
        $this->evaluasiMock->shouldReceive('findOrFail')->once()->with($evaluasiId)->andReturn($evaluasiRecord);
        
        // 4. Mock TemplateSertifikatModel::findOrFail()
        $templateRecord = Mockery::mock();
        $templateRecord->id = 7;
        $templateRecord->file_template = 'templates/tidak_ada.html';
        $this->templateMock->shouldReceive('findOrFail')->once()->with(7)->andReturn($templateRecord);

        // 5. Pastikan Facade PDF TIDAK dipanggil
        $this->pdfFacadeMock->shouldNotReceive('loadHTML');
        
        // 6. Mock Session Flash untuk error
        $this->sessionStoreMock->shouldReceive('flash')->once()->with('error', Mockery::type('string'));
        $this->sessionStoreMock->shouldReceive('get')->once()->with('error')->andReturn('Template tidak ditemukan di: /path/to/template'); // Mocking return value

        $response = $controller->store($request, $evaluasiId);

        $this->assertEquals(302, $response->getStatusCode());
        // Verifikasi bahwa response merupakan back() redirect (yang juga 302)
        $this->assertTrue($response->isRedirect(), 'Response should be a back() redirect.');
        $this->assertStringContainsString('Template tidak ditemukan', $response->getSession()->get('error') ?? '');
    }
    
    #[Test]
    public function store_melempar_validation_exception_bila_data_tidak_valid()
    {
        $controller = new PengumumanMagangController();
        $evaluasiId = 40;

        $this->expectException(ValidationException::class);

        // Missing nomor_sertifikat and template_id
        $request = $this->fakeRequest([
            'nomor_sertifikat' => null, 
            'template_id' => null,
        ]);

        // EvaluasiModel tidak perlu dimock karena validasi dieksekusi sebelum findOrFail
        
        $controller->store($request, $evaluasiId);
    }
}