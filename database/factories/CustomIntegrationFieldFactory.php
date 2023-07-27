<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\CustomIntegrationFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomIntegrationFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber(),
            'name' => fake()->randomElement(CustomIntegrationFieldEnum::cases())->value,
            'api_key' => Str::random(40),
            'integration_type' => fake()->randomElement(IntegrationTypeEnum::cases())->value,
            'organization_id' => Organization::factory()->create(),
        ];
    }
}
