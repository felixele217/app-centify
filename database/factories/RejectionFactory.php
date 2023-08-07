<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

class RejectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber(),
            'reason' => fake()->text(40),
            'deal_id' => Deal::factory()->create(),
        ];
    }
}
