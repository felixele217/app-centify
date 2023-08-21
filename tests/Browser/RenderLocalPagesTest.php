<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Admin;
use App\Models\Integration;
use App\Models\Plan;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RenderLocalPagesTest extends DuskTestCase
{
    public function testRenderLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('login')
                ->assertRouteIs('login')
                ->waitForText('Log in')
                ->assertSee('Log in');
        });
    }

    public function testRender(): void
    {
        $admin = $this->setupDatabase();

        foreach (PagesTestCases::testCases() as $routeConfig) {
            $this->browse(function (Browser $browser) use ($admin, $routeConfig) {
                $browser->loginAs($admin)
                    ->visit(env('APP_URL').$routeConfig['slug'])
                    ->assertUrlIs(env('APP_URL').$routeConfig['slug'])
                    ->waitForText($routeConfig['expected_text'])
                    ->assertSee($routeConfig['expected_text']);

                echo 'successfully tested '.$routeConfig['slug']."\n";
            });
        }
    }

    private function setupDatabase(): Admin
    {
        $admin = Admin::factory()->create();

        Plan::factory()->hasAgents(3, [
            'organization_id' => $admin->organization->id,
        ])->create([
            'id' => 1,
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

        Integration::factory()->create([
            'id' => 1,
            'organization_id' => $admin->organization->id,
        ]);

        return $admin;
    }
}
