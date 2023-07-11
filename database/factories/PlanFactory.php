<?php

namespace Database\Factories;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word().' Plan',
            'start_date' => Carbon::parse('-1 week'),
            'target_amount_per_month' => 500000,
            'target_variable' => TargetVariableEnum::ARR->value,
            'payout_frequency' => PayoutFrequencyEnum::MONTHLY->value,
            'organization_id' => Organization::factory()->create(),
            'creator_id' => Admin::factory()->create(),
        ];
    }
}
