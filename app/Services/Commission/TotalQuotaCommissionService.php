<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;

class TotalQuotaCommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): ?int
    {
        return $agent->plans()->active()->get()
            ->map(fn (Plan $plan) => (new PlanQuotaCommissionService())->calculate($agent, $plan, $timeScope))
            ->sum();
    }
}
