<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CliffFactory extends Factory
{
    public function definition(): array
    {
        return [
           'threshold_in_percent' => fake()->numberBetween(0, 1)
        ];
    }
}
