<?php

namespace Database\Factories;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word().' Plan',
            'start_date' => Carbon::tomorrow(),
            'target_amount_per_month' => 500000,
            'target_variable' => TargetVariableEnum::ARR->value,
            'payout_frequency' => PayoutFrequencyEnum::MONTHLY->value,
            'organization_id' => Auth::user() ? Auth::user()->organization->id : Organization::factory()->create(),
            'creator_id' => Auth::user() ? Auth::user()->id : User::factory()->create(),
        ];
    }

    public function configure()
    {
        Role::firstOrCreate(['name' => 'agent']);

        return $this->afterCreating(function (Plan $plan) {
            $plan->agents()->saveMany(User::role('agent')->take(fake()->numberBetween(1, 5))->get());
        });
    }
}
