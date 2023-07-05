<?php

use App\Models\Admin;
use App\Models\Agent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in(
    'Feature',
    'Unit',
    'Integration',
);

function signInAdmin($admin = null): Admin
{
    $admin = $admin ?: Admin::factory()->create();

    test()->actingAs($admin, 'admin');

    return $admin;
}

function signInAgent($agent = null): Agent
{
    $agent = $agent ?: Agent::factory()->create();

    test()->actingAs($agent, 'agent');

    return $agent;
}
