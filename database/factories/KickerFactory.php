<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class KickerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber(),
            'type' => fake()->randomElement(KickerTypeEnum::cases())->value,
            'threshold_in_percent' => 200,
            'payout_in_percent' => 25,
            'salary_type' => fake()->randomElement(SalaryTypeEnum::cases())->value,
            'plan_id' => Plan::factory()->create(),
        ];
    }
}