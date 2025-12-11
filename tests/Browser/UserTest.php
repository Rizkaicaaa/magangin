<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Group;

class UserTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $superadmin;
    protected $userToManage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->userToManage = User::factory()->create(['role' => 'mahasiswa', 'nama_lengkap' => 'Budi Asli']);
    }

    /**
     * Test Superadmin can view, create, edit, and delete a user.
     */
    #[Group('user-management-dusk')]
    public function testSuperadminCanPerformUserCRUD()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superadmin)
                    ->visit(route('users.index'))
                    ->assertSee('Manajemen User')
                    ->assertSee($this->userToManage->nama_lengkap);

            // 1. Create User
            $browser->press('Tambah User') // Assuming this button opens a modal
                    ->whenAvailable('#userModal', function ($modal) {
                        $modal->type('nama_lengkap', 'User Baru')
                              ->type('email', 'userbaru@test.com')
                              ->type('password', 'password123')
                              ->select('role', 'mahasiswa')
                              ->press('Simpan');
                    })
                    ->waitForText('User berhasil ditambahkan!')
                    ->assertSee('User Baru');

            // 2. Edit User
            $userToEdit = User::where('email', 'userbaru@test.com')->first();
            $browser->press('@edit-user-'.$userToEdit->id) // Using a dusk selector for the edit button
                    ->whenAvailable('#userModal', function ($modal) {
                        $modal->assertInputValue('nama_lengkap', 'User Baru')
                              ->type('nama_lengkap', 'User Diedit')
                              ->select('role', 'admin')
                              ->press('Simpan');
                    })
                    ->waitForText('User berhasil diperbarui!')
                    ->assertSee('User Diedit');

            // 3. Delete User
            $userToDelete = User::where('email', 'userbaru@test.com')->first();
            $browser->press('@delete-user-'.$userToDelete->id)
                    ->acceptDialog()
                    ->waitForText('User berhasil dihapus!')
                    ->assertDontSee('User Diedit');
        });
    }

    /**
     * Test non-superadmin users are denied access.
     */
    #[Group('user-management-dusk')]
    public function testNonSuperadminIsDeniedAccess()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        // Test with Admin
        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('users.index'))
                    ->assertPathIsNot(route('users.index')) // Should be redirected
                    ->assertPathIs('/dashboard'); // Assuming redirect to dashboard
        });

        // Test with Mahasiswa
        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->loginAs($mahasiswa)
                    ->visit(route('users.index'))
                    ->assertPathIsNot(route('users.index'))
                    ->assertPathIs('/dashboard');
        });
    }
}
