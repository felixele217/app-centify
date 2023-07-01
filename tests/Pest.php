<?php

use App\Enum\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in(
    'Feature',
    'Unit',
    'Integration',
);

function signIn($role = RoleEnum::ADMIN->value, $permission = '', $user = null): User
{
    $user = $user ?: User::factory()
        ->create()
        ->assignRole(Role::firstOrCreate(['name' => $role]))
        ->givePermissionTo(Permission::firstOrCreate(['name' => $permission]));

    test()->actingAs($user, null);

    return $user;
}
