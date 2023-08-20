<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Admin;
use Database\Seeders\TestDataSeeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class StagingTest extends DuskTestCase
{
    public function testStaging(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://staging.centify.de/login')
                ->waitForInput('email')
                ->waitForInput('password')
                ->type('email')
                ->type('password', 'b')
                ->press('Log in')
                ->waitForRoute('dashboard')
                ->assertRouteIs('dashboard');
        });
    }
}
