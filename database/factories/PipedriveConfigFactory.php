<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Models\Agent;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PipedriveConfigFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber(),
            'access_token' => 'some token',
            'refresh_token' => 'some token',
            'expires_at' => Carbon::parse('+10 minutes'),
            'subdomain' => null,
            'organization_id' => Organization::factory()->create()->id,
        ];
    }
}
