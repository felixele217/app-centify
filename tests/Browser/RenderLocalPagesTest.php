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
    const DASHBOARD_TEXT = 'Total Commission';

    const DEALS_INDEX_TEXT = 'Deals';

    const AGENTS_INDEX_TEXT = 'Agents';

    const INTEGRATIONS_INDEX_TEXT = 'pipedrive';

    const PROFILE_EDIT_TEXT = 'Profile Information';

    const INTEGRATIONS_CUSTOM_FIELDS_INDEX_TEXT = 'Custom Integration Fields';

    const PLANS_INDEX_TEXT = 'Plans';

    const PLANS_CREATE_TEXT = 'Create Quota Attainment Plan';

    const PLANS_EDIT_TEXT = 'Update Quota Attainment Plan';

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
            route('dashboard') => self::DASHBOARD_TEXT,
            route('deals.index') => self::DEALS_INDEX_TEXT,
            route('agents.index') => self::AGENTS_INDEX_TEXT,
            route('integrations.index') => self::INTEGRATIONS_INDEX_TEXT,
            route('profile.edit') => self::PROFILE_EDIT_TEXT,
            route('integrations.custom-fields.index', $admin->organization->integrations->first()) => self::INTEGRATIONS_CUSTOM_FIELDS_INDEX_TEXT,

            route('plans.index') => self::PLANS_INDEX_TEXT,
            route('plans.create') => self::PLANS_CREATE_TEXT,
            route('plans.edit', $admin->organization->plans->first()) => self::PLANS_EDIT_TEXT,
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
