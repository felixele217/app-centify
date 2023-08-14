<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SplitFactory extends Factory
{
    public function definition(): array
    {
        return [
             'id' => fake()->unique()->randomNumber() + 1,
        ];
    }
}
