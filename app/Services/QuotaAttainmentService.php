<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use Carbon\Carbon;

class QuotaAttainmentService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): float
    {
        $latestActivePlan = $agent->load('plans')->plans()->active()->first();

        if (! $latestActivePlan?->target_amount_per_month) {
            return 0;
        }

        return match ($timeScope) {
            TimeScopeEnum::MONTHLY => array_sum($agent->deals()->whereMonth('accepted_at', Carbon::now()->month)->get()->map(fn (Deal $deal) => $this->cappedValue($deal, $latestActivePlan?->cap?->value))->toArray()) / $latestActivePlan?->target_amount_per_month,
            TimeScopeEnum::QUARTERLY => $agent->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfQuarter(), Carbon::now()->endOfQuarter()])->sum('value') / ($latestActivePlan?->target_amount_per_month * 3),
            TimeScopeEnum::ANNUALY => $agent->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()])->sum('value') / ($latestActivePlan?->target_amount_per_month * 12),
            default => $agent->deals()->whereMonth('accepted_at', Carbon::now()->month)->sum('value') / $latestActivePlan?->target_amount_per_month,
        };
    }

    private function cappedValue(Deal $deal, ?int $cap): int
    {
        if (!!$cap && $deal->value >= $cap) {
            return $cap;
        }

        return $deal->value;
    }
}
