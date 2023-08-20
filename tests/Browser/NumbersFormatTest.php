<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NumbersFormatTest extends DuskTestCase
{
    public function testFormatsEuros(): void
    {
        $admin = $this->setupDatabase();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit(route('dashboard'))
                ->assertUrlIs(route('dashboard'))
                ->waitForText('Total Commission')
                ->assertSee('5.000,00€');
        });
    }

    public function testFormatsPercentage(): void
    {
        $admin = $this->setupDatabase();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit(route('dashboard'))
                ->assertUrlIs(route('dashboard'))
                ->waitForText('Quota Attainment')
                ->assertSee('50%');
        });
    }

    private function setupDatabase(): Admin
    {
        $admin = Admin::factory()->create();

        $plan = Plan::factory()->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
            'target_amount_per_month' => 10_000_00,
        ]);

        $agent = Agent::factory()->create([
            'organization_id' => $admin->organization->id,
            'base_salary' => 50_000_00,
            'on_target_earning' => 170_000_00,
        ]);

        $plan->agents()->attach($agent);

        Deal::factory()->create([
            'value' => 5_000_00,
            'agent_id' => $agent->id,
            'accepted_at' => Carbon::yesterday(),
        ]);

        return $admin;
    }
}
