<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Admin;
use App\Models\Integration;
use App\Models\Plan;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RenderPagesTest extends DuskTestCase
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

        $urlsToText = [
            route('dashboard') => 'Total Commission',
            route('deals.index') => 'Users',
            route('agents.index') => 'Dashboard',
            route('integrations.index') => 'pipedrive',
            route('profile.edit') => 'Profile Information',
            route('integrations.custom-fields.index', $admin->organization->integrations->first()) => 'Custom Integration Fields',

            route('plans.index') => 'Plans',
            route('plans.create') => 'Create Straight-Line Commission Plan',
            route('plans.edit', $admin->organization->plans->first()) => 'Update Straight-Line Commission Plan',
        ];

        foreach ($urlsToText as $url => $text) {
            $this->browse(function (Browser $browser) use ($admin, $url, $text) {
                $browser->loginAs($admin)
                    ->visit($url)
                    ->assertUrlIs($url)
                    ->waitForText($text)
                    ->assertSee($text);

                echo 'successfully tested '.$url."\n";
            });
        }
    }

    private function setupDatabase(): Admin
    {
        $admin = Admin::factory()->create();

        Plan::factory(5)->hasAgents(3, [
            'organization_id' => $admin->organization->id,
        ])->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

        Integration::factory()->create([
            'organization_id' => $admin->organization->id,
        ]);

        return $admin;
    }
}
