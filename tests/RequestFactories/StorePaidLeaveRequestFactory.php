<?php

namespace Tests\RequestFactories;

use App\Enum\AgentStatusEnum;
use Worksome\RequestFactories\RequestFactory;

class StorePaidLeaveRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'reason' => AgentStatusEnum::ACTIVE->value,
            'paid_leave' => [
                'start_date' => null,
                'end_date' => null,
                'continuation_of_pay_time_scope' => null,
                'sum_of_commissions' => null,
            ],
        ];
    }
}
