<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Admin;
use App\Models\Agent;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RenderSlideOversTest extends DuskTestCase
{
    public function testRender(): void
    {
        $user = $this->setupDatabase();

        $urlsToText = [
            route('agents.index') => [
                'awaitedText' => 'Agents',
                'elementPrefix' => 'upsert-agent-slide-over',
                'expectedText' => 'Create a new Agent',
            ],
            route('agents.index') => [
                'awaitedText' => '+ Add Plan',
                'elementPrefix' => 'manage-agent-plans-slide-over',
                'expectedText' => 'Manage Plans',
            ],
        ];

        foreach ($urlsToText as $url => $assertData) {
            $this->browse(function (Browser $browser) use ($user, $url, $assertData) {
                $browser->loginAs($user)
                    ->visit($url)
                    ->assertUrlIs($url)
                    ->waitForText($assertData['awaitedText'])
                    ->click('@'.$assertData['elementPrefix'].'-button')
                    ->waitFor('@'.$assertData['elementPrefix'])
                    ->assertVisible('@'.$assertData['elementPrefix'])
                    ->waitForText($assertData['expectedText'])
                    ->assertSee($assertData['expectedText']);

                echo 'successfully tested '.$url."\n";
            });
        }
    }

    private function setupDatabase(): Admin
    {
        $admin = Admin::factory()->create();

        Agent::factory()->ofOrganization($admin->organization_id)->create();

        return $admin;
    }
}
