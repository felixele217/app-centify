<?php

namespace Tests\Browser;

use App\Models\User;
use App\Enum\RoleEnum;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;

class RenderModalsTest extends DuskTestCase
{
    public function testRender(): void
    {
        $user = $this->setupDatabase();

        $urlsToText = [
            route('agents.index') => [
                'awaitedText' => 'Agents',
                'elementPrefix' => 'slide-over',
                'expectedText' => 'Create Agent',
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

    private function setupDatabase(): User
    {
        Artisan::call('db:seed --class=PermissionSeeder');

        return User::factory()->create()->assignRole(RoleEnum::ADMIN->value);
    }
}
