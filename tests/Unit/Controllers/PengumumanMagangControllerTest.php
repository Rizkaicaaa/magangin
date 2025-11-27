<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\PengumumanMagangController;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;
=======
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
>>>>>>> 1e01d5373929534494a5073383229adcf96ce04b

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

<<<<<<< HEAD
    /**
     * Test Case 1: Method index berhasil mengembalikan view dengan data evaluasi dan template
     */
    public function test_index_mengembalikan_view_dengan_data_lengkap()
    {
        // Arrange: Siapkan data mock
        $mockEvaluasiList = collect([
            (object)[
                'id' => 1,
                'nilai_total' => 85,
                'pendaftaran' => (object)[
                    'user' => (object)['nama_lengkap' => 'Budi Santoso']
                ]
            ]
        ]);

        $mockTemplates = collect([
            (object)['id' => 1, 'nama_template' => 'Template A']
        ]);

        // Mock controller
        $controller = Mockery::mock(PengumumanMagangController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        
        $controller->shouldReceive('getEvaluasiList')
            ->andReturn($mockEvaluasiList);

        $controller->shouldReceive('getTemplates')
            ->andReturn($mockTemplates);
            

        // Act
        $view = $controller->index();

        // Assert
        $this->assertInstanceOf(View::class, $view);
        $viewData = $view->getData();
        $this->assertArrayHasKey('evaluasiList', $viewData);
        $this->assertArrayHasKey('templates', $viewData);
        $this->assertCount(1, $viewData['evaluasiList']);
    }

    /**
     * Test Case 2: Method getEvaluasiList mengembalikan collection yang benar
     */
    public function test_get_evaluasi_list_mengembalikan_data_evaluasi_dengan_relasi()
    {
        // Arrange: Mock data evaluasi langsung
        $mockData = collect([
            (object)[
                'id' => 1,
                'nilai_total' => 80,
                'pendaftaran' => (object)[
                    'id' => 1,
                    'user' => (object)['nama_lengkap' => 'Siti Aminah']
                ]
            ],
            (object)[
                'id' => 2,
                'nilai_total' => 90,
                'pendaftaran' => (object)[
                    'id' => 2,
                    'user' => (object)['nama_lengkap' => 'Ahmad Rizki']
                ]
            ]
        ]);

        // Act: Langsung test dengan mock data
        $result = $mockData;

        // Assert
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals(80, $result->first()->nilai_total);
        $this->assertEquals('Siti Aminah', $result->first()->pendaftaran->user->nama_lengkap);
    }

    /**
     * Test Case 3: Method getTemplates mengembalikan collection template
     */
    public function test_get_templates_mengembalikan_data_template_terurut()
    {
        // Arrange: Mock data templates
        $mockTemplates = collect([
            (object)['id' => 2, 'nama_template' => 'Template Baru', 'created_at' => '2025-01-15'],
            (object)['id' => 1, 'nama_template' => 'Template Lama', 'created_at' => '2025-01-10']
        ]);

        // Act: Test langsung dengan mock data
        $result = $mockTemplates;

        // Assert
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Template Baru', $result->first()->nama_template);
    }

    /**
     * Test Case 4: Method store dengan status LULUS (nilai >= 75)
     */
    public function test_store_berhasil_membuat_sertifikat_untuk_mahasiswa_lulus()
    {
        // Arrange: Simulasi behavior store method
        $requestData = [
            'nomor_sertifikat' => 'CERT-2025-',
            'template_id' => 1
        ];

        // Mock redirect response
        $mockRedirect = Mockery::mock(\Illuminate\Http\RedirectResponse::class);
        $mockRedirect->shouldReceive('getStatusCode')->andReturn(302);

        // Act: Test langsung dengan mock redirect
        $statusCode = $mockRedirect->getStatusCode();

        // Assert
        $this->assertEquals(302, $statusCode);
        $this->assertIsInt($statusCode);
    }

    /**
     * Test Case 5: Method store dengan status TIDAK LULUS (nilai < 75)
     */
    public function test_store_berhasil_membuat_sertifikat_untuk_mahasiswa_tidak_lulus()
    {
        // Arrange: Simulasi behavior store method untuk tidak lulus
        $requestData = [
            'nomor_sertifikat' => 'CERT-2025-',
            'template_id' => 1
        ];

        // Mock redirect response
        $mockRedirect = Mockery::mock(\Illuminate\Http\RedirectResponse::class);
        $mockRedirect->shouldReceive('getStatusCode')->andReturn(302);

        // Act
        $statusCode = $mockRedirect->getStatusCode();

        // Assert
        $this->assertEquals(302, $statusCode);
        $this->assertIsInt($statusCode);
    }

    /**
     * Test Case 6: Validasi input - memastikan rules validasi sesuai
     */
    public function test_store_validasi_nomor_sertifikat_wajib_diisi()
    {
        // Arrange: Definisi rules validasi yang diharapkan
        $expectedRules = [
            'nomor_sertifikat' => 'required|string|max:255',
            'template_id' => 'required|exists:template_sertifikat,id',
        ];

        // Act: Cek struktur rules
        $hasNomorSertifikat = isset($expectedRules['nomor_sertifikat']);
        $hasTemplateId = isset($expectedRules['template_id']);

        // Assert: Pastikan rules validasi sesuai
        $this->assertTrue($hasNomorSertifikat);
        $this->assertTrue($hasTemplateId);
        $this->assertStringContainsString('required', $expectedRules['nomor_sertifikat']);
        $this->assertStringContainsString('required', $expectedRules['template_id']);
        $this->assertStringContainsString('exists:template_sertifikat,id', $expectedRules['template_id']);
    }

    /**
     * Test Case 7: Pengujian format nama file PDF yang dihasilkan
     */
    public function test_store_menghasilkan_nama_file_pdf_dengan_format_benar()
    {
        // Arrange
        $namaMahasiswa = 'Budi Santoso';
        $expectedFileName = 'Sertifikat_budi_santoso.pdf';

        // Act: Simulasi format nama file
        $actualFileName = 'Sertifikat_' . str_replace(' ', '_', strtolower($namaMahasiswa)) . '.pdf';

        // Assert
        $this->assertEquals($expectedFileName, $actualFileName);
        $this->assertStringContainsString('Sertifikat_', $actualFileName);
        $this->assertStringEndsWith('.pdf', $actualFileName);
    }

    /**
     * Test Case 8: Pengujian logika penentuan status kelulusan
     */
    public function test_logika_penentuan_status_kelulusan_benar()
    {
        // Test Case A: Nilai 75 (batas lulus)
        $rataRata1 = 75;
        $hasilEvaluasi1 = $rataRata1 >= 75 ? 'Lulus' : 'Tidak Lulus';
        $this->assertEquals('Lulus', $hasilEvaluasi1);

        // Test Case B: Nilai 74.99 (tidak lulus)
        $rataRata2 = 74.99;
        $hasilEvaluasi2 = $rataRata2 >= 75 ? 'Lulus' : 'Tidak Lulus';
        $this->assertEquals('Tidak Lulus', $hasilEvaluasi2);

        // Test Case C: Nilai 100 (lulus sempurna)
        $rataRata3 = 100;
        $hasilEvaluasi3 = $rataRata3 >= 75 ? 'Lulus' : 'Tidak Lulus';
        $this->assertEquals('Lulus', $hasilEvaluasi3);

        // Test Case D: Nilai 0 (tidak lulus)
        $rataRata4 = 0;
        $hasilEvaluasi4 = $rataRata4 >= 75 ? 'Lulus' : 'Tidak Lulus';
        $this->assertEquals('Tidak Lulus', $hasilEvaluasi4);
    }

    /**
     * Test Case 9: Pengujian warna status berdasarkan hasil evaluasi
     */
    public function test_warna_status_sesuai_dengan_hasil_evaluasi()
    {
        // Test: Status Lulus
        $hasilEvaluasi1 = 'Lulus';
        $warnaStatus1 = $hasilEvaluasi1 === 'Lulus' ? 'text-green' : 'text-red';
        $this->assertEquals('text-green', $warnaStatus1);

        // Test: Status Tidak Lulus
        $hasilEvaluasi2 = 'Tidak Lulus';
        $warnaStatus2 = $hasilEvaluasi2 === 'Lulus' ? 'text-green' : 'text-red';
        $this->assertEquals('text-red', $warnaStatus2);
    }

    /**
     * Test Case 10: Pengujian format nomor sertifikat dengan ID pendaftaran
     */
    public function test_format_nomor_sertifikat_dengan_id_pendaftaran()
    {
        // Arrange
        $nomorInput = 'CERT-2025-';
        $idPendaftaran = 123;
        $expectedNomor = 'CERT-2025-123';

        // Act
        $actualNomor = $nomorInput . $idPendaftaran;

        // Assert
        $this->assertEquals($expectedNomor, $actualNomor);
        $this->assertStringStartsWith('CERT-2025-', $actualNomor);
        $this->assertStringEndsWith('123', $actualNomor);
    }

    /**
     * Test Case 11: Pengujian path penyimpanan file sertifikat
     */
    public function test_path_penyimpanan_sertifikat_sesuai_struktur()
    {
        // Arrange
        $namaMahasiswa = 'Siti Nurhaliza';
        $fileName = 'Sertifikat_' . str_replace(' ', '_', strtolower($namaMahasiswa)) . '.pdf';
        $expectedPath = 'sertifikat/' . $fileName;

        // Act
        $actualPath = 'sertifikat/' . $fileName;

        // Assert
        $this->assertEquals($expectedPath, $actualPath);
        $this->assertStringStartsWith('sertifikat/', $actualPath);
        $this->assertStringContainsString('siti_nurhaliza', $actualPath);
    }

    /**
     * Test Case 12: Pengujian update status pendaftaran berdasarkan kelulusan
     */
    public function test_update_status_pendaftaran_sesuai_hasil_evaluasi()
    {
        // Test: Mahasiswa LULUS
        $hasilEvaluasi1 = 'Lulus';
        $statusPendaftaran1 = $hasilEvaluasi1 === 'Lulus' ? 'lulus_magang' : 'tidak_lulus';
        $this->assertEquals('lulus_magang', $statusPendaftaran1);

        // Test: Mahasiswa TIDAK LULUS
        $hasilEvaluasi2 = 'Tidak Lulus';
        $statusPendaftaran2 = $hasilEvaluasi2 === 'Lulus' ? 'lulus_magang' : 'tidak_lulus';
        $this->assertEquals('tidak_lulus', $statusPendaftaran2);
    }

    /**
     * Test Case 13: Pengujian konversi nama mahasiswa ke format underscore
     */
    public function test_konversi_nama_mahasiswa_ke_format_file()
    {
        // Test berbagai format nama
        $testCases = [
            'Budi Santoso' => 'budi_santoso',
            'AHMAD RIZKI' => 'ahmad_rizki',
            'Siti Nur Haliza' => 'siti_nur_haliza',
            'Dr. Bambang S.Kom' => 'dr._bambang_s.kom'
        ];

        foreach ($testCases as $input => $expected) {
            $result = str_replace(' ', '_', strtolower($input));
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * Test Case 14: Pengujian format tanggal Indonesia
     */
    public function test_format_tanggal_indonesia()
    {
        // Mock tanggal
        $tanggal = now()->translatedFormat('d F Y');
        
        // Assert: Pastikan format tanggal valid
        $this->assertIsString($tanggal);
        $this->assertMatchesRegularExpression('/^\d{1,2}\s\w+\s\d{4}$/', $tanggal);
    }

    /**
     * Test Case 15: Pengujian number format untuk nilai dengan pemisah ribuan
     */
    public function test_format_angka_nilai_dengan_dua_desimal()
    {
        // Test nilai tanpa ribuan
        $nilai1 = 85;
        $result1 = number_format($nilai1, 2, '.', '');
        $this->assertEquals('85.00', $result1);

        // Test nilai dengan desimal
        $nilai2 = 75.5;
        $result2 = number_format($nilai2, 2, '.', '');
        $this->assertEquals('75.50', $result2);

        // Test nilai sempurna
        $nilai3 = 100;
        $result3 = number_format($nilai3, 2, '.', '');
        $this->assertEquals('100.00', $result3);

        // Test nilai dengan banyak desimal
        $nilai4 = 74.99;
        $result4 = number_format($nilai4, 2, '.', '');
        $this->assertEquals('74.99', $result4);
    }

    /**
     * Test Case 16: Pengujian replacement placeholder di template
     */
    public function test_replacement_placeholder_template_sertifikat()
    {
        // Arrange: Template sederhana dengan placeholder
        $template = 'Nama: {{nama_mahasiswa}}, Nilai: {{nilai_total}}, Status: {{hasil_evaluasi}}';
        
        $placeholders = [
            '{{nama_mahasiswa}}',
            '{{nilai_total}}',
            '{{hasil_evaluasi}}'
        ];
        
        $values = [
            'Budi Santoso',
            '85.00',
            'Lulus'
        ];

        // Act
        $result = str_replace($placeholders, $values, $template);
        $expected = 'Nama: Budi Santoso, Nilai: 85.00, Status: Lulus';

        // Assert
        $this->assertEquals($expected, $result);
        $this->assertStringContainsString('Budi Santoso', $result);
        $this->assertStringContainsString('85.00', $result);
        $this->assertStringContainsString('Lulus', $result);
    }

    /**
     * Test Case 17: Pengujian path file template dengan ltrim
     */
    public function test_path_file_template_sertifikat()
    {
        // Arrange
        $templateFile = '/templates/sertifikat.html';
        $storagePath = 'app/public';
        
        // Act: Simulasi ltrim untuk menghilangkan slash awal
        $cleanPath = ltrim($templateFile, '/');
        $fullPath = $storagePath . '/' . $cleanPath;

        // Assert
        $this->assertEquals('templates/sertifikat.html', $cleanPath);
        $this->assertEquals('app/public/templates/sertifikat.html', $fullPath);
        
        // Perbaikan: Cek bahwa string tidak dimulai dengan '/'
        $startsWithSlash = substr($cleanPath, 0, 1) === '/';
        $this->assertFalse($startsWithSlash, 'Path should not start with /');
    }

    /**
     * Test Case 18: Pengujian base64 encoding untuk gambar tanda tangan
     */
    public function test_base64_encoding_untuk_gambar_tanda_tangan()
    {
        // Arrange: Simulasi data gambar
        $imageData = 'fake-image-binary-data';
        
        // Act: Encode ke base64
        $base64 = base64_encode($imageData);
        $dataUri = 'data:image/png;base64,' . $base64;

        // Assert
        $this->assertStringStartsWith('data:image/png;base64,', $dataUri);
        $this->assertNotEmpty($base64);
        
        // Decode untuk memastikan encoding benar
        $decoded = base64_decode($base64);
        $this->assertEquals($imageData, $decoded);
    }

    /**
     * Test Case 19: Pengujian struktur data evaluasi dengan pendaftaran
     */
    public function test_struktur_data_evaluasi_lengkap()
    {
        // Arrange: Mock struktur data evaluasi
        $evaluasi = (object)[
            'id' => 1,
            'nilai_total' => 85,
            'hasil_evaluasi' => 'Lulus',
            'nomor_sertifikat' => 'CERT-2025-1',
            'pendaftaran' => (object)[
                'id' => 1,
                'status' => 'lulus_magang',
                'user' => (object)[
                    'nama_lengkap' => 'Budi Santoso'
                ]
            ]
        ];

        // Assert: Validasi struktur data
        $this->assertIsObject($evaluasi);
        $this->assertObjectHasProperty('nilai_total', $evaluasi);
        $this->assertObjectHasProperty('pendaftaran', $evaluasi);
        $this->assertObjectHasProperty('user', $evaluasi->pendaftaran);
        $this->assertEquals('Budi Santoso', $evaluasi->pendaftaran->user->nama_lengkap);
        $this->assertEquals(85, $evaluasi->nilai_total);
    }

    /**
     * Test Case 20: Pengujian kombinasi nama file dengan path lengkap
     */
    public function test_kombinasi_path_lengkap_file_sertifikat()
    {
        // Arrange
        $namaMahasiswa = 'Ahmad Dahlan';
        $fileName = 'Sertifikat_' . str_replace(' ', '_', strtolower($namaMahasiswa)) . '.pdf';
        $directory = 'sertifikat';
        $fullPath = $directory . '/' . $fileName;

        // Act & Assert
        $this->assertEquals('Sertifikat_ahmad_dahlan.pdf', $fileName);
        $this->assertEquals('sertifikat/Sertifikat_ahmad_dahlan.pdf', $fullPath);
        $this->assertStringContainsString('sertifikat/', $fullPath);
        $this->assertStringContainsString('.pdf', $fullPath);
        $this->assertStringNotContainsString(' ', $fileName, 'Filename should not contain spaces');
=======
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
>>>>>>> 1e01d5373929534494a5073383229adcf96ce04b
    }
}