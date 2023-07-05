<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            'email' => 'alex.dosse@centify.com',
            'password' => Hash::make('centify'),
        ]);

        Agent::factory(1)->create([
            'email' => 'paul.sochiera@gmail.com',
            'organization_id' => $admin->organization->id,
        ]);

        Agent::factory(5)->create([
            'organization_id' => $admin->organization->id,
        ]);

        Plan::factory(3)->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ])->first();
    }
}
