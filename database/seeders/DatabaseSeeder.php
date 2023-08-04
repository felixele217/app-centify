<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\CustomField;
use App\Models\Integration;
use App\Enum\CustomFieldEnum;
use Illuminate\Database\Seeder;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::factory()->create([
            'name' => 'Alex Dosse',
            'email' => 'product@centify.de',
            'password' => Hash::make('centify'),
        ]);

        $centifyAgent = Agent::factory()->create([
            'name' => 'Centify Agent',
            'email' => 'tech@centify.de',
            'organization_id' => $admin->organization->id,
            'on_target_earning' => 200_000_00,
            'base_salary' => 100_000_00,
        ]);

        $pipedriveAgent1 = Agent::factory()->create([
            'name' => 'Pipedrive Agent 1',
            'email' => 'pipedrive1@centify.de',
            'organization_id' => $admin->organization->id,
            'on_target_earning' => 200_000_00,
            'base_salary' => 100_000_00,
        ]);

        $pipedriveAgent2 = Agent::factory()->create([
            'name' => 'Pipedrive Agent 2',
            'email' => 'pipedrive2@centify.de',
            'organization_id' => $admin->organization->id,
            'on_target_earning' => 200_000_00,
            'base_salary' => 100_000_00,
        ]);

        Plan::factory()->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
            'name' => 'Jr. Account Executive Plan',
            'target_amount_per_month' => 35_000_00,
        ])->first();

        Plan::factory()->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
            'name' => 'Sr. Account Executive Plan',
            'target_amount_per_month' => 50_000_00,
        ])->first();

        Plan::factory()->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
            'name' => 'SDR Plan',
            'target_amount_per_month' => 15_000_00,
        ])->first();

        Plan::first()->agents()->attach([
            ...$admin->organization->agents->pluck('id'),
        ]);

        CustomField::create([
            'integration_id' => Integration::factory()->create([
                'name' => IntegrationTypeEnum::PIPEDRIVE->value,
                'organization_id' => $admin->organization->id,
            ]),
            'name' => CustomFieldEnum::DEMO_SET_BY->value,
            'api_key' => env('PIPEDRIVE_DEMO_SET_BY', 'invalid key'),
        ]);
    }
}
