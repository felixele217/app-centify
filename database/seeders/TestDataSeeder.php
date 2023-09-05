<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\PlanCycleEnum;
use App\Enum\TargetVariableEnum;
use App\Enum\TriggerEnum;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\AgentPlan;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    const ADMIN_EMAIL = 'product@centify.de';

    const ADMIN_PASSWORD = 'centify';

    public function run(): void
    {
        $admin = Admin::firstOrCreate([
            'email' => self::ADMIN_EMAIL,
        ], [
            'name' => 'Alex Dosse',
            'email' => self::ADMIN_EMAIL,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'password' => Hash::make(self::ADMIN_PASSWORD),
            'organization_id' => Organization::firstOrCreate([
                'name' => 'Centify GmbH',
            ])->id,
        ]);

        $centifyAgent = Agent::create([
            'name' => 'Centify Agent',
            'email' => 'tech@centify.de',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'organization_id' => $admin->organization_id,
            'on_target_earning' => 200_000_00,
            'base_salary' => 100_000_00,
        ]);

        $pipedriveAgent1 = Agent::create([
            'name' => 'Pipedrive Agent 1',
            'email' => 'pipedrive1@centify.de',
            'organization_id' => $admin->organization_id,
            'on_target_earning' => 200_000_00,
            'base_salary' => 100_000_00,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);

        $pipedriveAgent2 = Agent::create([
            'name' => 'Pipedrive Agent 2',
            'email' => 'pipedrive2@centify.de',
            'organization_id' => $admin->organization_id,
            'on_target_earning' => 200_000_00,
            'base_salary' => 100_000_00,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);

        $demoScheduledPlan = Plan::create([
            'id' => 1,
            'organization_id' => $admin->organization_id,
            'creator_id' => $admin->id,
            'name' => 'Demo Scheduled Plan',
            'start_date' => Carbon::parse('2023-05-01'),
            'target_amount_per_month' => 5_000_00,
            'target_variable' => TargetVariableEnum::DEAL_VALUE->value,
            'plan_cycle' => PlanCycleEnum::MONTHLY->value,
            'target_amount_per_month' => 35_000_00,
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

        $dealWonPlan = Plan::create([
            'organization_id' => $admin->organization_id,
            'creator_id' => $admin->id,
            'name' => 'Deal Won Plan',
            'target_amount_per_month' => 50_000_00,
            'start_date' => Carbon::parse('2023-05-01'),
            'target_amount_per_month' => 5_000_00,
            'target_variable' => TargetVariableEnum::DEAL_VALUE->value,
            'plan_cycle' => PlanCycleEnum::MONTHLY->value,
            'trigger' => TriggerEnum::DEAL_WON->value,
        ]);

        AgentPlan::factory()->create([
            'plan_id' => $demoScheduledPlan->id,
            'agent_id' => $centifyAgent->id,
            'share_of_variable_pay' => 30,
        ]);

        AgentPlan::factory()->create([
            'plan_id' => $dealWonPlan->id,
            'agent_id' => $centifyAgent->id,
            'share_of_variable_pay' => 70,
        ]);

        Deal::factory(3)
            ->withAgentDeal($centifyAgent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
            ->create();

        Deal::factory(3)
            ->withAgentDeal($centifyAgent->id, TriggerEnum::DEAL_WON, Carbon::now())
            ->won(Carbon::now())
            ->create();

        AgentPlan::factory()->create([
            'plan_id' => $demoScheduledPlan->id,
            'agent_id' => $pipedriveAgent1->id,
        ]);

        AgentPlan::factory()->create([
            'plan_id' => $demoScheduledPlan->id,
            'agent_id' => $pipedriveAgent2->id,
        ]);
    }
}
