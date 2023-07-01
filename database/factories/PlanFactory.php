<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Enum\TargetVariableEnum;
use App\Enum\PayoutFrequencyEnum;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word() . ' Plan',
            'start_date' => Carbon::tomorrow(),
            'target_amount_per_month' => 500000,
            'target_variable' => TargetVariableEnum::ARR->value,
            'payout_frequency' => PayoutFrequencyEnum::MONTHLY->value,
            'organization_id' => Organization::factory()->create(),
        ];
    }
}
