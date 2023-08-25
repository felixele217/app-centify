<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Illuminate\Support\Facades\Auth;
use Worksome\RequestFactories\RequestFactory;

class UpdateAgentRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'base_salary' => fake()->randomElement([50_000_00, 100_000_00]),
            'on_target_earning' => fake()->randomElement([120_000_00, 200_000_00]),
            'organization_id' => Auth::user()->organization->id,
        ];
    }
}
