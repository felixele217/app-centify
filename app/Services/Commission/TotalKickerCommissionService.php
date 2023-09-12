<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Models\Agent;
use App\Models\Plan;
use App\Services\QuotaAttainment\PlanQuotaAttainmentService;

class TotalKickerCommissionService extends TimeScopedCommissionService
{
    public function calculate(Agent $agent): int
    {
        return $agent->plans()->active($this->dateInScope)->get()->map(function (Plan $plan) use ($agent) {
            return (new PlanKickerCommissionService($this->timeScope))->calculate($agent, $plan, (new PlanQuotaAttainmentService($agent, $plan, $this->timeScope, $this->dateInScope))->calculate());
        })->sum();
    }
}
