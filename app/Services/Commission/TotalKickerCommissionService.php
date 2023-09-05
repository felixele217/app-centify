<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Models\Plan;
use App\Models\Agent;
use App\Enum\TimeScopeEnum;
use Carbon\CarbonImmutable;
use App\Services\QuotaAttainment\PlanQuotaAttainmentService;

class TotalKickerCommissionService
{
    private CarbonImmutable $dateInScope;

    public function __construct(
        private TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null,
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function calculate(Agent $agent): ?int
    {
        return $agent->plans()->active($this->dateInScope)->get()->map(function (Plan $plan) use ($agent) {
            return (new PlanKickerCommissionService($this->timeScope))->calculate($agent, $plan, (new PlanQuotaAttainmentService($agent, $plan, $this->timeScope, $this->dateInScope))->calculate());
        })->sum();
    }
}
