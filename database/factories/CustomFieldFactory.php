<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Organization;
use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber(),
            'name' => fake()->randomElement(CustomFieldEnum::cases())->value,
            'api_key' => Str::random(40),
            'integration_type' => fake()->randomElement(IntegrationTypeEnum::cases())->value,
            'organization_id' => Organization::factory()->create(),
        ];
    }
}
