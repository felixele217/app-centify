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
            'id' => uniqueIdWith6Digits(),
            'reason' => fake()->text(40),
            'is_permanent' => false,
            'deal_id' => Deal::factory(),
        ];
    }
}
