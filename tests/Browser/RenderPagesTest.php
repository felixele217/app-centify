<?php

namespace Tests\Browser;

use App\Models\Admin;
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
        $user = $this->setupDatabase();

        $urlsToText = [
            route('dashboard') => 'Total Payout',
            route('todos.index') => 'Users',
            route('plans.index') => 'Plans',
            route('agents.index') => 'Dashboard',
            route('integrations') => 'pipedrive',
            route('profile.edit') => 'Profile Information',
        ];

        foreach ($urlsToText as $url => $text) {
            $this->browse(function (Browser $browser) use ($user, $url, $text) {
                $browser->loginAs($user)
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
        $user = Admin::factory()->create();

        Plan::factory(5)->hasAgents(3, [
            'organization_id' => $user->organization->id,
        ])->create([
            'organization_id' => $user->organization->id,
            'creator_id' => $user->id,
        ]);

        return $user;
    }
}
