<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Enum\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Alex Dosse',
            'email' => 'alex.dosse@centify.com',
            'password' => Hash::make('centify'),
        ])->assignRole(RoleEnum::ADMIN->value);


        User::factory(5)->agent()->create([
            'organization_id' => $admin->organization->id
        ]);
    }
}
