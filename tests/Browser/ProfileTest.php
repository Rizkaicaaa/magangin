<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Group;

class ProfileTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test user can successfully update their profile information.
     */
    #[Group('profile-dusk')]
    public function testUserCanUpdateProfileInformation()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit(route('profile.edit'))
                    ->assertInputValue('nama_lengkap', $user->nama_lengkap)
                    ->assertInputValue('email', $user->email);

            $browser->type('nama_lengkap', 'Nama Baru')
                    ->type('email', 'emailbaru@example.com')
                    ->press('Save') // Assuming the save button has text 'Save'
                    ->assertPathIs(route('profile.edit'))
                    ->assertSee('Profile has been updated.') // Assuming this success message
                    ->assertInputValue('nama_lengkap', 'Nama Baru')
                    ->assertInputValue('email', 'emailbaru@example.com');
        });
    }

    /**
     * Test user can successfully update their password.
     */
    #[Group('profile-dusk')]
    public function testUserCanUpdatePassword()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit(route('profile.password.edit'))
                    ->type('current_password', 'password_lama')
                    ->type('password', 'password_baru')
                    ->type('password_confirmation', 'password_baru')
                    ->press('Save') // Assuming the save button has text 'Save'
                    ->assertPathIs(route('profile.password.edit'))
                    ->assertSee('Password has been updated.'); // Assuming this success message
        });
    }

    /**
     * Test password update fails if current password is incorrect.
     */
    #[Group('profile-dusk')]
    public function testPasswordUpdateFailsWithIncorrectCurrentPassword()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_asli'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit(route('profile.password.edit'))
                    ->type('current_password', 'password_salah')
                    ->type('password', 'password_baru')
                    ->type('password_confirmation', 'password_baru')
                    ->press('Save')
                    ->assertPathIs(route('profile.password.edit'))
                    ->assertSee('The current password field must be correct.'); // Example validation message
        });
    }
}
