<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\Plan;
use Illuminate\Database\Seeder;
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
        ]);

        $pipedriveAgent1 = Agent::factory()->create([
            'name' => 'Pipedrive Agent 1',
            'email' => 'pipedrive1@centify.de',
            'organization_id' => $admin->organization->id,
        ]);

        $pipedriveAgent2 = Agent::factory()->create([
            'name' => 'Pipedrive Agent 2',
            'email' => 'pipedrive2@centify.de',
            'organization_id' => $admin->organization->id,
        ]);

        Agent::factory(5)->create([
            'organization_id' => $admin->organization->id,
        ]);

        Plan::factory(3)->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ])->first();

        Plan::first()->agents()->attach([
            ...$admin->organization->agents->pluck('id'),
            $centifyAgent->id,
            $pipedriveAgent1->id,
            $pipedriveAgent2->id,
        ]);
    }
}
