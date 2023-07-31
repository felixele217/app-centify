<?php

namespace Tests\RequestFactories;

use App\Enum\AgentStatusEnum;
use Illuminate\Support\Facades\Auth;
use Worksome\RequestFactories\RequestFactory;

class StoreAgentRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'base_salary' => fake()->randomElement([5000000, 10000000]),
            'on_target_earning' => fake()->randomElement([10000000, 20000000]),
            'organization_id' => Auth::user()->organization->id,
        ];
    }
}
