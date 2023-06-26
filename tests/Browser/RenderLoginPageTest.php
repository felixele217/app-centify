<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RenderLoginPageTest extends DuskTestCase
{
    public function testRender(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('login')
                ->assertRouteIs('login')
                ->waitForText('Remember me')
                ->assertSee('Remember me');
        });
    }
}
