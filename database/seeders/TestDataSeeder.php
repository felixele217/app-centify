<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Organization;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::firstOrCreate([
            'email' => 'product@centify.de',
        ], [
            'name' => 'Alex Dosse',
            'email' => 'product@centify.de',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'password' => Hash::make('centify'),
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

        Plan::create([
            'organization_id' => $admin->organization_id,
            'creator_id' => $admin->id,
            'name' => 'Jr. Account Executive Plan',
            'start_date' => Carbon::parse('-1 week'),
            'target_amount_per_month' => 5_000_00,
            'target_variable' => TargetVariableEnum::DEAL_VALUE->value,
            'payout_frequency' => PayoutFrequencyEnum::MONTHLY->value,
            'target_amount_per_month' => 35_000_00,
        ])->first();

        Plan::create([
            'organization_id' => $admin->organization_id,
            'creator_id' => $admin->id,
            'name' => 'Sr. Account Executive Plan',
            'target_amount_per_month' => 50_000_00,
            'start_date' => Carbon::parse('-1 week'),
            'target_amount_per_month' => 5_000_00,
            'target_variable' => TargetVariableEnum::DEAL_VALUE->value,
            'payout_frequency' => PayoutFrequencyEnum::MONTHLY->value,
        ])->first();

        Plan::create([
            'organization_id' => $admin->organization_id,
            'creator_id' => $admin->id,
            'name' => 'SDR Plan',
            'start_date' => Carbon::parse('-1 week'),
            'target_amount_per_month' => 5_000_00,
            'target_variable' => TargetVariableEnum::DEAL_VALUE->value,
            'payout_frequency' => PayoutFrequencyEnum::MONTHLY->value,
            'target_amount_per_month' => 15_000_00,
        ])->first();

        Plan::first()->agents()->attach([
            ...$admin->organization->agents->pluck('id'),
        ]);
    }
}