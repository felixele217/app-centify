<?php

namespace Database\Seeders;

use App\Enum\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => RoleEnum::ADMIN->value]);
        Role::firstOrCreate(['name' => RoleEnum::AGENT->value]);
    }
}
