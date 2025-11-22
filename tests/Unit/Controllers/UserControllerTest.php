<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    protected $userModelMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock User model SEKALI SAJA di setUp
        $this->userModelMock = Mockery::mock('alias:App\Models\User');
        
        // Mock Validator untuk bypass validasi di semua test
        Validator::shouldReceive('make')
            ->andReturnUsing(function ($data, $rules) {
                $validator = Mockery::mock('Illuminate\Validation\Validator');
                $validator->shouldReceive('validate')->andReturn($data);
                $validator->shouldReceive('validated')->andReturn($data);
                $validator->shouldReceive('fails')->andReturn(false);
                $validator->shouldReceive('errors')->andReturn(collect([]));
                return $validator;
            });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // ==================== INDEX METHOD ====================
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function index_menampilkan_daftar_user_terurut_descending()
    {
        // Arrange - Persiapan data
        $mockUsers = collect([
            (object)['id' => 3, 'nama_lengkap' => 'User Ketiga', 'email' => 'user3@test.com'],
            (object)['id' => 2, 'nama_lengkap' => 'User Kedua', 'email' => 'user2@test.com'],
            (object)['id' => 1, 'nama_lengkap' => 'User Pertama', 'email' => 'user1@test.com'],
        ]);

        $this->userModelMock
            ->shouldReceive('orderBy')
            ->once()
            ->with('id', 'desc')
            ->andReturnSelf();
            
        $this->userModelMock
            ->shouldReceive('get')
            ->once()
            ->andReturn($mockUsers);

        $controller = new UserController();

        // Act - Eksekusi method
        $response = $controller->index();

        // Assert - Verifikasi hasil
        $this->assertEquals('user.index', $response->name());
        $this->assertEquals($mockUsers, $response->getData()['users']);
        $this->assertCount(3, $response->getData()['users']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function index_mengembalikan_view_dengan_data_users()
    {
        // Arrange
        $mockUsers = collect([]);

        $this->userModelMock
            ->shouldReceive('orderBy')
            ->once()
            ->with('id', 'desc')
            ->andReturnSelf();
            
        $this->userModelMock
            ->shouldReceive('get')
            ->once()
            ->andReturn($mockUsers);

        $controller = new UserController();

        // Act
        $response = $controller->index();

        // Assert
        $this->assertEquals('user.index', $response->name());
        $this->assertIsObject($response->getData()['users']);
    }

    // ==================== STORE METHOD ====================

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_berhasil_membuat_user_baru_dengan_data_lengkap()
    {
        // Arrange
        $requestData = [
            'nama_lengkap' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'mahasiswa',
            'nim' => '1234567890',
            'no_telp' => '081234567890',
            'status' => 'aktif',
            'dinas_id' => 1,
        ];

        $request = new Request($requestData);
        $request->setMethod('POST');
        
        Hash::shouldReceive('make')
            ->once()
            ->with('password123')
            ->andReturn('hashed_password_123');

        $this->userModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['nama_lengkap'] === 'John Doe' &&
                       $data['email'] === 'john@example.com' &&
                       $data['password'] === 'hashed_password_123' &&
                       $data['role'] === 'mahasiswa' &&
                       $data['nim'] === '1234567890' &&
                       $data['no_telp'] === '081234567890' &&
                       $data['status'] === 'aktif' &&
                       $data['dinas_id'] === 1 &&
                       isset($data['tanggal_daftar']);
            }))
            ->andReturn((object)['id' => 1]);

        $controller = new UserController();

        // Act
        $response = $controller->store($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('users', $response->getTargetUrl());
        $this->assertEquals('User berhasil ditambahkan!', session('success'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_berhasil_dengan_data_minimal_tanpa_field_opsional()
    {
        // Arrange
        $requestData = [
            'nama_lengkap' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'securepass',
            'role' => 'admin',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('POST');
        
        Hash::shouldReceive('make')
            ->once()
            ->with('securepass')
            ->andReturn('hashed_securepass');

        $this->userModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['nama_lengkap'] === 'Jane Doe' &&
                       $data['email'] === 'jane@example.com' &&
                       $data['role'] === 'admin' &&
                       $data['status'] === 'aktif';
            }))
            ->andReturn((object)['id' => 2]);

        $controller = new UserController();

        // Act
        $response = $controller->store($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('User berhasil ditambahkan!', session('success'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_melakukan_hash_pada_password_sebelum_menyimpan()
    {
        // Arrange
        $requestData = [
            'nama_lengkap' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'plaintext_password',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('POST');
        
        Hash::shouldReceive('make')
            ->once()
            ->with('plaintext_password')
            ->andReturn('hashed_plaintext_password');

        $this->userModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                // Verifikasi password sudah di-hash
                return $data['password'] === 'hashed_plaintext_password';
            }))
            ->andReturn((object)['id' => 3]);

        $controller = new UserController();

        // Act
        $controller->store($request);

        // Assert - Mockery akan memverifikasi Hash::make dipanggil
        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_menambahkan_tanggal_daftar_secara_otomatis()
    {
        // Arrange
        $requestData = [
            'nama_lengkap' => 'Auto Date User',
            'email' => 'autodate@example.com',
            'password' => 'password123',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('POST');
        
        Hash::shouldReceive('make')
            ->once()
            ->andReturn('hashed_password');

        $expectedDate = now()->toDateString();

        $this->userModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($expectedDate) {
                // Verifikasi tanggal_daftar ditambahkan dengan tanggal hari ini
                return isset($data['tanggal_daftar']) && 
                       $data['tanggal_daftar'] === $expectedDate;
            }))
            ->andReturn((object)['id' => 4]);

        $controller = new UserController();

        // Act
        $controller->store($request);

        // Assert
        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_dapat_menyimpan_user_dengan_role_superadmin()
    {
        // Arrange
        $requestData = [
            'nama_lengkap' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => 'password123',
            'role' => 'superadmin',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('POST');
        
        Hash::shouldReceive('make')->once()->andReturn('hashed_password');

        $this->userModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['role'] === 'superadmin';
            }))
            ->andReturn((object)['id' => 5]);

        $controller = new UserController();

        // Act
        $response = $controller->store($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_dapat_menyimpan_user_dengan_role_admin()
    {
        // Arrange
        $requestData = [
            'nama_lengkap' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'admin',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('POST');
        
        Hash::shouldReceive('make')->once()->andReturn('hashed_password');

        $this->userModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['role'] === 'admin';
            }))
            ->andReturn((object)['id' => 6]);

        $controller = new UserController();

        // Act
        $response = $controller->store($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_dapat_menyimpan_user_dengan_role_mahasiswa()
    {
        // Arrange
        $requestData = [
            'nama_lengkap' => 'Mahasiswa User',
            'email' => 'mahasiswa@example.com',
            'password' => 'password123',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('POST');
        
        Hash::shouldReceive('make')->once()->andReturn('hashed_password');

        $this->userModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['role'] === 'mahasiswa';
            }))
            ->andReturn((object)['id' => 7]);

        $controller = new UserController();

        // Act
        $response = $controller->store($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_dapat_menyimpan_user_dengan_status_aktif()
    {
        // Arrange
        $requestData = [
            'nama_lengkap' => 'Active User',
            'email' => 'active@example.com',
            'password' => 'password123',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('POST');
        
        Hash::shouldReceive('make')->once()->andReturn('hashed_password');

        $this->userModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['status'] === 'aktif';
            }))
            ->andReturn((object)['id' => 8]);

        $controller = new UserController();

        // Act
        $response = $controller->store($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_dapat_menyimpan_user_dengan_status_non_aktif()
    {
        // Arrange
        $requestData = [
            'nama_lengkap' => 'Inactive User',
            'email' => 'inactive@example.com',
            'password' => 'password123',
            'role' => 'mahasiswa',
            'status' => 'non_aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('POST');
        
        Hash::shouldReceive('make')->once()->andReturn('hashed_password');

        $this->userModelMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['status'] === 'non_aktif';
            }))
            ->andReturn((object)['id' => 9]);

        $controller = new UserController();

        // Act
        $response = $controller->store($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    // ==================== EDIT METHOD ====================

    #[\PHPUnit\Framework\Attributes\Test]
    public function edit_mengembalikan_data_user_dalam_format_json()
    {
        // Arrange
        $userId = 1;
        $mockUser = (object)[
            'id' => 1,
            'nama_lengkap' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ];

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $response = $controller->edit($userId);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($mockUser, $response->getData());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function edit_menggunakan_findOrFail_untuk_mencari_user()
    {
        // Arrange
        $userId = 5;
        $mockUser = (object)['id' => 5, 'nama_lengkap' => 'Test User'];

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $controller->edit($userId);

        // Assert - Mockery akan memverifikasi findOrFail dipanggil
        $this->assertTrue(true);
    }

    // ==================== UPDATE METHOD ====================

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_berhasil_memperbarui_user_tanpa_mengubah_password()
    {
        // Arrange
        $userId = 1;
        $requestData = [
            'nama_lengkap' => 'John Doe Updated',
            'email' => 'johnupdated@example.com',
            'role' => 'admin',
            'nim' => '9876543210',
            'no_telp' => '081234567890',
            'status' => 'aktif',
            'dinas_id' => 2,
        ];

        $request = new Request($requestData);
        $request->setMethod('PUT');

        $mockUser = Mockery::mock('stdClass');
        $mockUser->id = $userId;
        $mockUser->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($data) {
                // Verifikasi password TIDAK ada di data update
                return $data['nama_lengkap'] === 'John Doe Updated' &&
                       $data['email'] === 'johnupdated@example.com' &&
                       $data['role'] === 'admin' &&
                       !isset($data['password']);
            }))
            ->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $response = $controller->update($request, $userId);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('User berhasil diperbarui!', session('success'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_berhasil_memperbarui_user_dengan_password_baru()
    {
        // Arrange
        $userId = 1;
        $requestData = [
            'nama_lengkap' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'role' => 'admin',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('PUT');

        Hash::shouldReceive('make')
            ->once()
            ->with('newpassword123')
            ->andReturn('hashed_newpassword123');

        $mockUser = Mockery::mock('stdClass');
        $mockUser->id = $userId;
        $mockUser->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($data) {
                // Verifikasi password DI-HASH dan disimpan
                return $data['nama_lengkap'] === 'John Doe' &&
                       $data['password'] === 'hashed_newpassword123';
            }))
            ->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $response = $controller->update($request, $userId);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('User berhasil diperbarui!', session('success'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_tidak_mengubah_password_jika_field_kosong()
    {
        // Arrange
        $userId = 1;
        $requestData = [
            'nama_lengkap' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '', // Password kosong
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('PUT');

        // Hash::make TIDAK BOLEH dipanggil
        Hash::shouldReceive('make')->never();

        $mockUser = Mockery::mock('stdClass');
        $mockUser->id = $userId;
        $mockUser->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($data) {
                // Verifikasi password TIDAK ada
                return !isset($data['password']);
            }))
            ->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $response = $controller->update($request, $userId);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_tidak_mengubah_password_jika_field_null()
    {
        // Arrange
        $userId = 1;
        $requestData = [
            'nama_lengkap' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'mahasiswa',
            'status' => 'aktif',
            // password tidak ada sama sekali
        ];

        $request = new Request($requestData);
        $request->setMethod('PUT');

        Hash::shouldReceive('make')->never();

        $mockUser = Mockery::mock('stdClass');
        $mockUser->id = $userId;
        $mockUser->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($data) {
                return !isset($data['password']);
            }))
            ->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $response = $controller->update($request, $userId);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_menggunakan_request_except_untuk_mengabaikan_password()
    {
        // Arrange
        $userId = 1;
        $requestData = [
            'nama_lengkap' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('PUT');

        $mockUser = Mockery::mock('stdClass');
        $mockUser->id = $userId;
        $mockUser->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $response = $controller->update($request, $userId);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_dapat_memperbarui_semua_field_sekaligus()
    {
        // Arrange
        $userId = 1;
        $requestData = [
            'nama_lengkap' => 'Complete Update',
            'email' => 'complete@example.com',
            'password' => 'newpassword',
            'role' => 'admin',
            'nim' => '1111111111',
            'no_telp' => '089999999999',
            'status' => 'non_aktif',
            'dinas_id' => 5,
        ];

        $request = new Request($requestData);
        $request->setMethod('PUT');

        Hash::shouldReceive('make')
            ->once()
            ->with('newpassword')
            ->andReturn('hashed_newpassword');

        $mockUser = Mockery::mock('stdClass');
        $mockUser->id = $userId;
        $mockUser->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['nama_lengkap'] === 'Complete Update' &&
                       $data['email'] === 'complete@example.com' &&
                       $data['password'] === 'hashed_newpassword' &&
                       $data['role'] === 'admin' &&
                       $data['nim'] === '1111111111' &&
                       $data['no_telp'] === '089999999999' &&
                       $data['status'] === 'non_aktif' &&
                       $data['dinas_id'] === 5;
            }))
            ->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $response = $controller->update($request, $userId);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('User berhasil diperbarui!', session('success'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_mencari_user_dengan_findOrFail_terlebih_dahulu()
    {
        // Arrange
        $userId = 10;
        $requestData = [
            'nama_lengkap' => 'Test',
            'email' => 'test@example.com',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ];

        $request = new Request($requestData);
        $request->setMethod('PUT');

        $mockUser = Mockery::mock('stdClass');
        $mockUser->id = $userId;
        $mockUser->shouldReceive('update')->once()->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $controller->update($request, $userId);

        // Assert - Mockery akan memverifikasi findOrFail dipanggil dengan userId yang benar
        $this->assertTrue(true);
    }

    // ==================== DESTROY METHOD ====================

    #[\PHPUnit\Framework\Attributes\Test]
    public function destroy_berhasil_menghapus_user()
    {
        // Arrange
        $userId = 1;

        $mockUser = Mockery::mock('stdClass');
        $mockUser->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $response = $controller->destroy($userId);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('users', $response->getTargetUrl());
        $this->assertEquals('User berhasil dihapus!', session('success'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function destroy_memanggil_method_delete_pada_user()
    {
        // Arrange
        $userId = 5;

        $mockUser = Mockery::mock('stdClass');
        $mockUser->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $controller->destroy($userId);

        // Assert - Mockery akan memverifikasi delete() dipanggil
        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function destroy_mencari_user_dengan_findOrFail_sebelum_menghapus()
    {
        // Arrange
        $userId = 99;

        $mockUser = Mockery::mock('stdClass');
        $mockUser->shouldReceive('delete')->once()->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $controller->destroy($userId);

        // Assert
        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function destroy_mengembalikan_redirect_dengan_pesan_sukses()
    {
        // Arrange
        $userId = 1;

        $mockUser = Mockery::mock('stdClass');
        $mockUser->shouldReceive('delete')->once()->andReturn(true);

        $this->userModelMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($userId)
            ->andReturn($mockUser);

        $controller = new UserController();

        // Act
        $response = $controller->destroy($userId);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('User berhasil dihapus!', session('success'));
    }
}