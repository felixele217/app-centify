<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use Carbon\CarbonImmutable;

abstract class TimeScopedCommissionService
{
    protected CarbonImmutable $dateInScope;

    public function __construct(
        protected TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    abstract public function calculate(Agent $agent): ?int;
}
