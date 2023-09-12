<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\TimeScopeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class CliffFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => uniqueIdWith6Digits(),
            'threshold_in_percent' => fake()->numberBetween(0, 1),
            'time_scope' => fake()->randomElement(TimeScopeEnum::cases())->value,
        ];
    }
}
