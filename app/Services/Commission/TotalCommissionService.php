<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Models\Agent;

class TotalCommissionService extends TimeScopedCommissionService
{
    public function calculate(Agent $agent): ?int
    {
        $quotaCommission = (new TotalQuotaCommissionService($this->timeScope, $this->dateInScope))->calculate($agent);

        $kickerCommission = (new TotalKickerCommissionService($this->timeScope, $this->dateInScope))->calculate($agent);
        // dd($kickerCommission);

        $paidLeaveCommission = (new PaidLeaveCommissionService($this->timeScope, $this->dateInScope))->calculate($agent);

        return $quotaCommission + $kickerCommission + $paidLeaveCommission;
    }
}
