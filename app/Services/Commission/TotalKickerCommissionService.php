<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use App\Services\PlanQuotaAttainmentService;

class TotalKickerCommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): ?int
    {
        return $agent->plans()->active()->get()->map(function (Plan $plan) use ($agent, $timeScope) {
            return (new PlanKickerCommissionService())->calculate($agent, $plan, $timeScope, (new PlanQuotaAttainmentService($agent, $plan, $timeScope))->calculate());
        })->sum();
    }
}
