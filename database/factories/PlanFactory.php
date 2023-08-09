<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Models\Admin;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber() + 1,
            'name' => fake()->word().' Plan',
            'start_date' => Carbon::parse('-1 week'),
            'target_amount_per_month' => 5_000_00,
            'target_variable' => TargetVariableEnum::DEAL_VALUE->value,
            'payout_frequency' => PayoutFrequencyEnum::MONTHLY->value,
            'organization_id' => Organization::factory()->create(),
            'creator_id' => Admin::factory()->create(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => Carbon::now()->firstOfYear()->subDays(10)->firstOfYear(),
            'end_date' => Carbon::now()->lastOfYear()->addDays(10)->lastOfYear(),
        ]);
    }
}
