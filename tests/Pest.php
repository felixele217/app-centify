<?php

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

function signIn($role = 'organization admin', $permission = '', $user = null): User
{
    $user = $user ?: User::factory()
        ->create()
        ->assignRole(Role::create(['name' => $role]))
        ->givePermissionTo(Permission::create(['name' => $permission]));

    test()->actingAs($user, null);

    return $user;
}
