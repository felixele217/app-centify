<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => uniqueIdWith6Digits(),
            'name' => fake()->word().' Organization',
        ];
    }
}
