<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use Carbon\CarbonImmutable;

class TotalCommissionService
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
        $quotaCommission = (new TotalQuotaCommissionService($this->timeScope, $this->dateInScope))->calculate($agent);

        $kickerCommission = (new TotalKickerCommissionService($this->timeScope, $this->dateInScope))->calculate($agent);

        $paidLeaveCommission = (new PaidLeaveCommissionService($this->timeScope, $this->dateInScope))->calculate($agent);

        return $quotaCommission + $kickerCommission + $paidLeaveCommission;
    }
}
