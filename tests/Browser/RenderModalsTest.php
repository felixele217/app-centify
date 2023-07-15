<?php

namespace Tests\Browser;

use App\Models\Admin;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RenderModalsTest extends DuskTestCase
{
    public function testRender(): void
    {
        $user = $this->setupDatabase();

        $urlsToText = [
            route('agents.index') => [
                'awaitedText' => 'Agents',
                'elementPrefix' => 'slide-over',
                'expectedText' => 'Create a new Agent',
            ],
        ];

        foreach ($urlsToText as $url => $assertData) {
            $this->browse(function (Browser $browser) use ($user, $url, $assertData) {
                $browser->loginAs($user)
                    ->visit($url)
                    ->assertUrlIs($url)
                    ->waitForText($assertData['awaitedText'])
                    ->click('@'.$assertData['elementPrefix'].'-button')
                    ->waitFor('@'.$assertData['elementPrefix'].'-modal')
                    ->assertVisible('@'.$assertData['elementPrefix'].'-modal')

                    ->waitForText($assertData['expectedText'])
                    ->assertSee($assertData['expectedText']);

                echo 'successfully tested '.$url."\n";
            });
        }
    }

    private function setupDatabase(): Admin
    {
        return Admin::factory()->create();
    }
}
