<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\AgentStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Organization;
use App\Models\Payout;
use App\Services\Commission\CommissionFromQuotaService;
use App\Services\Commission\PaidLeaveCommissionService;
use App\Services\Commission\TotalKickerCommissionService;
use Carbon\CarbonImmutable;

class FreezePayoutsService
{
    private CarbonImmutable $dateInScope;

    public function __construct(
        private Organization $organization,
        private TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function freeze(): void
    {
        foreach ($this->organization->agents as $agent) {
            Payout::create([
                'time_scope' => $this->timeScope,
                'date_in_scope' => $this->dateInScope,
                'agent_id' => $agent->id,
                'sick_days' => count((new PaidLeaveDaysService())->paidLeaveDays($agent, $this->timeScope, AgentStatusEnum::SICK)),
                'vacation_days' => count((new PaidLeaveDaysService())->paidLeaveDays($agent, $this->timeScope, AgentStatusEnum::VACATION)),
                'quota_attainment_percentage' => $quotaAttainment = (new TotalQuotaAttainmentService($agent, $this->timeScope, $this->dateInScope))->calculate(),
                'kicker_commission' => (new TotalKickerCommissionService())->calculate($agent, $this->timeScope) ?? 0,
                'absence_commission' => (new PaidLeaveCommissionService())->calculate($agent, $this->timeScope),
                'commission_from_quota' => (new CommissionFromQuotaService())->calculate($agent, $this->timeScope, $quotaAttainment),
            ]);
        }
    }
}
