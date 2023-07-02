<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
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
            route('integrations') => 'pipedrive',
            route('agents.index') => 'Dashboard',
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

    private function setupDatabase(): User
    {
        Artisan::call('db:seed --class=PermissionSeeder');

        $user = User::factory()
            ->create();

        return $user;
    }
}
