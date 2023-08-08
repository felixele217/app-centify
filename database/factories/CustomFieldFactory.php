<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\CustomFieldEnum;
use App\Models\Integration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber(),
            'name' => fake()->randomElement(CustomFieldEnum::cases())->value,
            'api_key' => Str::random(40),
            'integration_id' => Integration::factory()->create()->id,
        ];
    }
}
