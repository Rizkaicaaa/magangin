<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Http\Controllers\EvaluasiMagangController;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; 

#[RunTestsInSeparateProcesses] 

class EvaluasiMagangControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Buat tabel users
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->integer('dinas_id')->nullable();
                $table->timestamps();
            });
        }

        // Buat tabel dinas
        if (!Schema::hasTable('dinas')) {
            Schema::create('dinas', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama_dinas');
                $table->timestamps();
            });
            
            \DB::table('dinas')->insert([
                'id' => 10,
                'nama_dinas' => 'Dinas Test',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat tabel info_or
        if (!Schema::hasTable('info_or')) {
            Schema::create('info_or', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama');
                $table->timestamps();
            });
            
            \DB::table('info_or')->insert([
                'id' => 1,
                'nama' => 'Info Test',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat tabel template_sertifikat
        if (!Schema::hasTable('template_sertifikat')) {
            Schema::create('template_sertifikat', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama_template');
                $table->string('file_template');
                $table->integer('created_by')->nullable();
                $table->timestamps();
            });

            \DB::table('template_sertifikat')->insert([
                'id' => 1,
                'nama_template' => 'Default Template',
                'file_template' => 'default.docx',
                'created_by' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat tabel pendaftaran
        if (!Schema::hasTable('pendaftaran')) {
            Schema::create('pendaftaran', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->integer('info_or_id');
                $table->integer('jadwal_seleksi_id')->nullable();
                $table->integer('pilihan_dinas_1');
                $table->integer('pilihan_dinas_2')->nullable();
                $table->text('motivasi');
                $table->text('pengalaman')->nullable();
                $table->string('file_cv', 200);
                $table->string('file_transkrip', 200);
                $table->enum('status_pendaftaran', [
                    'terdaftar',
                    'lulus_wawancara',
                    'tidak_lulus_wawancara',
                    'lulus_magang',
                    'tidak_lulus_magang'
                ])->default('terdaftar');
                $table->integer('dinas_diterima_id')->nullable();
                $table->timestamp('tanggal_daftar')->useCurrent();
                $table->timestamps();
            });

            \DB::table('pendaftaran')->insert([
                'id' => 1,
                'user_id' => 1,
                'info_or_id' => 1,
                'pilihan_dinas_1' => 10,
                'motivasi' => 'Test motivasi',
                'file_cv' => 'cv.pdf',
                'file_transkrip' => 'transkrip.pdf',
                'status_pendaftaran' => 'lulus_wawancara',
                'dinas_diterima_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat tabel evaluasi_magang
        if (!Schema::hasTable('evaluasi_magang')) {
            Schema::create('evaluasi_magang', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('pendaftaran_id')->unique();
                $table->integer('penilai_id');
                $table->integer('template_sertifikat_id')->nullable(); // ✅ NULLABLE
                $table->decimal('nilai_kedisiplinan', 5, 2)->nullable();
                $table->decimal('nilai_kerjasama', 5, 2)->nullable();
                $table->decimal('nilai_inisiatif', 5, 2)->nullable();
                $table->decimal('nilai_hasil_kerja', 5, 2)->nullable();
                $table->decimal('nilai_total', 5, 2)->nullable();
                $table->enum('hasil_evaluasi', ['Lulus', 'Tidak Lulus']);
                $table->string('nomor_sertifikat', 50)->unique()->nullable();
                $table->string('file_sertifikat', 200)->nullable();
                $table->timestamps();
            });
        }
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('evaluasi_magang');
        Schema::dropIfExists('pendaftaran');
        Schema::dropIfExists('template_sertifikat');
        Schema::dropIfExists('info_or');
        Schema::dropIfExists('dinas');
        Schema::dropIfExists('users');
        
        Mockery::close();
        parent::tearDown();
    }

    private function fakeRequest(array $data)
    {
        return Request::create('/', 'POST', $data);
    }

    #[Test]
    public function index_menampilkan_data_pendaftar_dan_penilaian()
    {
        $controller = new EvaluasiMagangController();

        Auth::shouldReceive('user')->andReturn((object)['id' => 1, 'dinas_id' => 10]);

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran')->shouldIgnoreMissing();
        $pendaftaranMock->shouldReceive('where')->andReturnSelf();
        $pendaftaranMock->shouldReceive('with')->andReturnSelf();
        $pendaftaranMock->shouldReceive('get')->andReturn(collect(['dummy_pendaftar']));

        $evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel')->shouldIgnoreMissing();
        $evaluasiMock->shouldReceive('with')->andReturnSelf();
        $evaluasiMock->shouldReceive('get')->andReturn(collect(['dummy_penilaian']));

        View::shouldReceive('make')->once()->andReturnSelf();
        View::shouldReceive('with')->andReturnSelf();

        $controller->index();

        $this->assertTrue(true);
    }

    #[Test]
    public function store_create_penilaian_baru()
    {
        $controller = new EvaluasiMagangController();

        $request = $this->fakeRequest([
            'pendaftaran_id' => 1,
            'nilai_kedisiplinan' => 80,
            'nilai_kerjasama' => 90,
            'nilai_inisiatif' => 70,
            'nilai_hasil_kerja' => 80,
        ]);

        Auth::shouldReceive('user')->andReturn((object)['id' => 5, 'dinas_id' => 10]);

        // Mock Pendaftaran Model
        $pendaftaranRecord = Mockery::mock();
        $pendaftaranRecord->shouldReceive('update')->once();

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran')->shouldIgnoreMissing();
        $pendaftaranMock->shouldReceive('findOrFail')->with(1)->andReturn($pendaftaranRecord);

        // ✅ Mock EvaluasiMagangModel - cek duplicate dulu
        $evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel')->shouldIgnoreMissing();
        $evaluasiMock->shouldReceive('where')->with('pendaftaran_id', 1)->andReturnSelf();
        $evaluasiMock->shouldReceive('first')->andReturn(null); // ✅ Tidak ada duplicate
        $evaluasiMock->shouldReceive('create')->once()->andReturn(true);

        $response = $controller->storeOrUpdate($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    #[Test]
    public function store_update_penilaian_jika_penilaian_id_ada()
    {
        $controller = new EvaluasiMagangController();

        $request = $this->fakeRequest([
            'pendaftaran_id' => 1,
            'penilaian_id' => 7,
            'nilai_kedisiplinan' => 50,
            'nilai_kerjasama' => 50,
            'nilai_inisiatif' => 50,
            'nilai_hasil_kerja' => 50,
        ]);

        Auth::shouldReceive('user')->andReturn((object)['id' => 10, 'dinas_id' => 10]);

        // Mock Pendaftaran
        $pendaftaranRecord = Mockery::mock();
        $pendaftaranRecord->shouldReceive('update')->once();

        $pendaftaranMock = Mockery::mock('overload:App\Models\Pendaftaran')->shouldIgnoreMissing();
        $pendaftaranMock->shouldReceive('findOrFail')->with(1)->andReturn($pendaftaranRecord);

        // Mock EvaluasiMagangModel
        $evaRecord = Mockery::mock();
        $evaRecord->shouldReceive('update')->once();

        $evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel')->shouldIgnoreMissing();
        $evaluasiMock->shouldReceive('findOrFail')->with(7)->andReturn($evaRecord);

        $response = $controller->storeOrUpdate($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    #[Test]
    public function store_melempar_validation_exception_bila_data_tidak_valid()
    {
        $controller = new EvaluasiMagangController();

        $this->expectException(ValidationException::class);

        $request = $this->fakeRequest([
            'pendaftaran_id' => null,
            'nilai_kedisiplinan' => 'abc',
        ]);

        $controller->storeOrUpdate($request);
    }

    #[Test]
    public function destroy_menghapus_penilaian()
    {
        $controller = new EvaluasiMagangController();

        $evaluasiRecord = Mockery::mock();
        $evaluasiRecord->shouldReceive('delete')->once();

        $evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel')->shouldIgnoreMissing();
        $evaluasiMock->shouldReceive('findOrFail')->with(99)->andReturn($evaluasiRecord);

        $response = $controller->destroy(99);

        $this->assertEquals(302, $response->getStatusCode());
    }

    #[Test]
    public function store_tidak_membuat_duplicate_penilaian()
    {
        $controller = new EvaluasiMagangController();

        $request = $this->fakeRequest([
            'pendaftaran_id' => 1,
            'nilai_kedisiplinan' => 80,
            'nilai_kerjasama' => 90,
            'nilai_inisiatif' => 70,
            'nilai_hasil_kerja' => 80,
        ]);

        Auth::shouldReceive('user')->andReturn((object)['id' => 5, 'dinas_id' => 10]);

        // Mock EvaluasiMagangModel - sudah ada evaluasi
        $existingEvaluasi = (object)['id' => 1, 'pendaftaran_id' => 1];
        
        $evaluasiMock = Mockery::mock('overload:App\Models\EvaluasiMagangModel')->shouldIgnoreMissing();
        $evaluasiMock->shouldReceive('where')->with('pendaftaran_id', 1)->andReturnSelf();
        $evaluasiMock->shouldReceive('first')->andReturn($existingEvaluasi); // ✅ Ada duplicate
        $evaluasiMock->shouldReceive('create')->never(); // ✅ Tidak boleh create

        $response = $controller->storeOrUpdate($request);

        $this->assertEquals(302, $response->getStatusCode());
    }
}