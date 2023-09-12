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
            'id' => uniqueIdWith6Digits(),
            'name' => fake()->randomElement(CustomFieldEnum::cases())->value,
            'api_key' => Str::random(40),
            'integration_id' => Integration::factory()->create()->id,
        ];
    }

    public function ofIntegration(int $integrationId): static
    {
        return $this->state(fn (array $attributes) => [
            'integration_id' => $integrationId,
        ]);
    }
}
