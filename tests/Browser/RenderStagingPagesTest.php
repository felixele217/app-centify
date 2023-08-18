<?php

declare(strict_types=1);

namespace Tests\Browser;

use Database\Seeders\TestDataSeeder;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RenderStagingPagesTest extends DuskTestCase
{
    /**
     * @group staging
     */
    public function testUserCanLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://staging.centify.de/login')
                ->waitForInput('email')
                ->waitForInput('password')
                ->type('email', TestDataSeeder::ADMIN_EMAIL)
                ->type('password', TestDataSeeder::ADMIN_PASSWORD)
                ->press('Log in')
                ->waitForRoute('dashboard')
                ->assertRouteIs('dashboard');

            $browser->visit('https://staging.centify.de/integrations')->assertSee('pipedrive');
        });
    }
}
