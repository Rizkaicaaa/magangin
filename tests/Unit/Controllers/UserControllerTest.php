<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function test_index_returns_view_with_users()
    {
        // Mock Eloquent Query
        $mock = Mockery::mock('alias:App\Models\User');
        $mock->shouldReceive('orderBy->get')
            ->once()
            ->andReturn(collect(['user1', 'user2']));

        $controller = new UserController();

        $response = $controller->index();

        $this->assertEquals('user.index', $response->getName());
        $this->assertArrayHasKey('users', $response->getData());
    }

    /** @test */
    public function test_store_creates_user_and_redirects()
    {
        // mock create
        $mock = Mockery::mock('alias:App\Models\User');
        $mock->shouldReceive('create')->once()->andReturn(new User);

        $data = [
            'nama_lengkap' => 'Test User',
            'email' => 'test@mail.com',
            'password' => 'password123',
            'role' => 'admin',
            'nim' => null,
            'no_telp' => null,
            'status' => 'aktif',
            'dinas_id' => null,
        ];

        $request = Request::create('/users', 'POST', $data);

        $controller = new UserController();
        $response = $controller->store($request);

        $this->assertEquals(302, $response->status());
    }

    /** @test */
    public function test_edit_returns_json()
    {
        $mockUser = new User(['id' => 1, 'nama_lengkap' => 'Test']);

        // Mock findOrFail
        $mock = Mockery::mock('alias:App\Models\User');
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($mockUser);

        $controller = new UserController();
        $response = $controller->edit(1);

        $this->assertEquals(200, $response->status());
    }

    /** @test */
    public function test_update_updates_user()
    {
        $existingUser = Mockery::mock(User::class)->makePartial();

        $mock = Mockery::mock('alias:App\Models\User');
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($existingUser);

        $existingUser->shouldReceive('update')->once()->andReturn(true);

        $request = Request::create('/users/1', 'PUT', [
            'nama_lengkap' => 'Updated Name',
            'email' => 'updated@mail.com',
            'role' => 'admin',
            'nim' => null,
            'no_telp' => null,
            'status' => 'aktif',
            'dinas_id' => null,
        ]);

        $controller = new UserController();
        $response = $controller->update($request, 1);

        $this->assertEquals(302, $response->status());
    }

    /** @test */
    public function test_destroy_deletes_user()
    {
        $existingUser = Mockery::mock(User::class)->makePartial();
        $existingUser->shouldReceive('delete')->once();

        $mock = Mockery::mock('alias:App\Models\User');
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($existingUser);

        $controller = new UserController();
        $response = $controller->destroy(1);

        $this->assertEquals(302, $response->status());
    }
}