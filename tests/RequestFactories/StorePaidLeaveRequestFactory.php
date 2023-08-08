<?php

namespace Tests\RequestFactories;

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use Carbon\Carbon;
use Worksome\RequestFactories\RequestFactory;

class StorePaidLeaveRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => Carbon::now(),
            'end_date' => null,
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
            'employed_28_or_more_days' => true,
        ];
    }
}
