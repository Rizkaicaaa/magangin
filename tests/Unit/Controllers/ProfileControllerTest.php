<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini
class ProfileControllerTest extends TestCase
{
    protected $controller;
    protected $userMock;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ProfileController();
        
        // Mock objek User (Model) untuk digunakan di $request->user() dan Auth::user()
        $this->userMock = Mockery::mock('App\Models\User');
        
        // **Perbaikan setup:** Menggunakan makePartial agar properti dasar (seperti id) dapat diakses
        // tanpa eror, sekaligus mempertahankan status mock.
        $this->userMock->shouldAllowMockingProtectedMethods()->makePartial();
        
        // FIX BARU: Mencegah BadMethodCallException pada setAttribute (dipicu oleh $user->id = 1, dll)
        $this->userMock->shouldReceive('setAttribute')->byDefault()->andReturnUsing(function ($key, $value) {
            $this->userMock->{$key} = $value;
        });


        // KRUSIAL UNTUK MENGHENTIKAN SQL: Mock getConnection untuk mencegah Model menyentuh database.
        
        // 1a. Buat mock untuk Grammar
        $grammarMock = Mockery::mock('Illuminate\Database\Query\Grammars\Grammar');
        $grammarMock->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s'); 

        // 1b. Buat mock untuk Query Builder (objek yang dikembalikan oleh query())
        $queryBuilderMock = Mockery::mock('Illuminate\Database\Query\Builder');
        
        // Memastikan chain of calls dari Eloquent Builder ke Query Builder di-mock
        $queryBuilderMock->shouldReceive('from')->andReturnSelf(); 
        $queryBuilderMock->shouldReceive('where')->andReturnSelf(); 
        $queryBuilderMock->shouldReceive('update')->andReturn(1); 
        
        // 2. Buat mock untuk Connection
        $connectionMock = Mockery::mock('Illuminate\Database\Connection');
        
        // 3a. Konfigurasi Connection: ketika dipanggil query(), kembalikan Query Builder mock
        $connectionMock->shouldReceive('query')->andReturn($queryBuilderMock);

        // 3b. Konfigurasi Connection: ketika dipanggil getQueryGrammar(), kembalikan Grammar mock
        $connectionMock->shouldReceive('getQueryGrammar')->andReturn($grammarMock); 

        // 4. Konfigurasi User Mock: ketika dipanggil getConnection()->kembalikan Connection mock
        $this->userMock->shouldReceive('getConnection')->andReturn($connectionMock);

        // Set ID dan properti awal pada mock
        $this->userMock->id = 1;
        $this->userMock->nama_lengkap = 'Nama Lama';
        $this->userMock->email = 'lama@example.com';
        // KRUSIAL: Menetapkan properti 'exists' ke true agar dianggap sebagai record yang sudah ada.
        $this->userMock->exists = true; 
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // --- TEST CASE 1: Menguji Metode edit() ---
    
    #[Test]
    public function edit_menampilkan_view_edit_profil_dengan_data_user()
    {
        // 1. Mocking Request dan $request->user()
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')->once()->andReturn($this->userMock);
        
        // 2. Panggil metode yang diuji
        $response = $this->controller->edit($request);

        // 3. Verifikasi
        $this->assertEquals('profile.edit', $response->getName());
        $this->assertEquals($this->userMock, $response->getData()['user']);
    }

    // --- TEST CASE 2: Menguji Metode update() ---

    #[Test]
    public function update_berhasil_memperbarui_data_profil()
    {
        // 1. Mocking Request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')->andReturn($this->userMock); // Untuk validasi dan pengambilan user
        
        // 2. Mocking $request->validate
        $validatedData = [
            'nama_lengkap' => 'Nama Baru',
            'email' => 'baru@example.com',
        ];
        // Memastikan Request::validate dipanggil dan mengembalikan data valid
        $request->shouldReceive('validate')->once()->andReturn($validatedData);
        
        // 3. MOCKING UNTUK POLA $user->save() (Sesuai dengan kode controller Anda)
        
        // **FOKUS**: Hanya mock save(). Set property langsung di-handle oleh makePartial dan setAttribute mock di setUp.
        $this->userMock->shouldReceive('save')
             ->once()
             ->andReturnUsing(function () use ($validatedData) {
                 // Simulasikan pembaruan (sudah dilakukan controller), pastikan save diterima.
                 return true; 
             });

        // 4. Mocking Redirect Facade
        $redirectMock = Mockery::mock('Illuminate\Http\RedirectResponse');
        $redirectMock->shouldReceive('with')->once()->with('status', 'profile-updated')->andReturnSelf();

        Redirect::shouldReceive('route')
            ->once()
            ->with('profile.edit')
            ->andReturn($redirectMock);

        // 5. Panggil metode yang diuji
        $response = $this->controller->update($request);
        
        // 6. Verifikasi perubahan pada user mock
        // Properti ini seharusnya sudah di-set oleh kode controller.
        $this->assertEquals('Nama Baru', $this->userMock->nama_lengkap, 'Nama lengkap harus diperbarui');
        $this->assertEquals('baru@example.com', $this->userMock->email, 'Email harus diperbarui');
        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
    }

    // --- TEST CASE 3: Menguji Metode editPassword() ---

    #[Test]
    public function editPassword_menampilkan_view_edit_password_dengan_user_auth()
    {
        // 1. Mocking Auth::user()
        Auth::shouldReceive('user')->once()->andReturn($this->userMock);
        
        // 2. Panggil metode yang diuji
        $response = $this->controller->editPassword();

        // 3. Verifikasi
        $this->assertEquals('profile.edit-password', $response->getName());
        $this->assertEquals($this->userMock, $response->getData()['user']);
    }
    
    // --- TEST CASE 4: Menguji Metode updatePassword() ---

    #[Test]
    public function updatePassword_berhasil_memperbarui_password()
    {
        
        // 1. Mocking Request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')->andReturn($this->userMock); // Untuk pengambilan user
        
        // Mocking 'all' dan 'route' agar Request::validate/metode internal tidak eror
        $request->shouldReceive('all')->andReturn([]); 
        $request->shouldReceive('route')->andReturn(null); 
        
        // 2. Mocking $request->validate
        $request->shouldReceive('validate')->once()->andReturn([
            'current_password' => 'password_lama',
            'password' => 'password_baru_123',
            'password_confirmation' => 'password_baru_123',
        ]);
        
        // 3. Mocking pemanggilan update() pada objek user
        $this->userMock->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($data) {
                // Memastikan field 'password' ada dan diisi dengan hash baru (dihasilkan oleh bcrypt)
                return array_key_exists('password', $data) && is_string($data['password']) && !empty($data['password']); 
            })) 
            ->andReturnUsing(function ($data) {
                // Simulasikan pembaruan password
                return true; 
            });
            
        // 4. Mocking Redirect Facade
        $redirectMock = Mockery::mock('Illuminate\Http\RedirectResponse');
        $redirectMock->shouldReceive('with')->once()->with('status', 'password-updated')->andReturnSelf();

        Redirect::shouldReceive('route')
            ->once()
            ->with('profile.password.edit')
            ->andReturn($redirectMock);
            
        // 5. Panggil metode yang diuji
        $response = $this->controller->updatePassword($request);

        // 6. Verifikasi
        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
    }
}