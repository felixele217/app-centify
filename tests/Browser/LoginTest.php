<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function testUserCanLogin(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make($password = 'asodfno12'),
        ]);

        $this->browse(function (Browser $browser) use ($user, $password) {
            $browser->visitRoute('login')
                ->waitForInput('email')
                ->waitForInput('password')
                ->type('email', $user->email)
                ->type('password', $password)
                ->press('LOG IN')
                ->waitForRoute('dashboard')
                ->assertRouteIs('dashboard');
        });
    }
}
