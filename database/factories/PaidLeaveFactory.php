<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaidLeaveFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reason' => $reason = fake()->randomElement([AgentStatusEnum::SICK, AgentStatusEnum::VACATION])->value,
            'start_date' => Carbon::yesterday(),
            'end_date' => $reason === AgentStatusEnum::SICK->value ? null : Carbon::parse('+1 week'),
            'continuation_of_pay_time_scope' => fake()->randomElement(ContinuationOfPayTimeScopeEnum::cases())->value,
            'sum_of_commissions' => 10_000_00,
        ];
    }
}
