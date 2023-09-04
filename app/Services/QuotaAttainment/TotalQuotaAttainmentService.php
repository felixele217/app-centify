<?php

declare(strict_types=1);

namespace App\Services\QuotaAttainment;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use Carbon\CarbonImmutable;

class TotalQuotaAttainmentService
{
    private CarbonImmutable $dateInScope;

    public function __construct(
        private Agent $agent,
        private TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function calculate(): float
    {
        return $this->agent->load('plans')->plans()->active($this->dateInScope)->get()
            ->map(fn (Plan $activePlan) => (new PlanQuotaAttainmentService($this->agent, $activePlan, $this->timeScope, $this->dateInScope))->calculate())
            ->sum();
    }
}
