<?php

declare(strict_types=1);

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\PagesTestCases;
use Database\Seeders\TestDataSeeder;

class RenderStagingPagesTest extends DuskTestCase
{
    const STAGING_BASE_URL = 'https://staging.centify.de';

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

            foreach (PagesTestCases::testCases() as $testCase) {
                $browser->visit(self::STAGING_BASE_URL.$testCase['slug'])->assertSee($testCase['expected_text']);
            }
        });
    }
}
