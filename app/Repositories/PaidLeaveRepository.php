<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\TimeScopeEnum;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\StorePaidLeaveRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PaidLeaveRepository
{
    public static function create(Agent $agent, StorePaidLeaveRequest $request): PaidLeave
    {
        return PaidLeave::create([
            'agent_id' => $agent->id,
            ...$request->validated(),
        ]);
    }

    public static function update(Agent $agent, StoreAgentRequest|UpdateAgentRequest $request): void
    {
        if ($agent->activePaidLeave) {
            $agent->activePaidLeave->update([
                ...$request->validated('paid_leave'),
            ]);
        } else {
            PaidLeave::create([
                'agent_id' => $agent->id,
                ...$request->validated('paid_leave'),
                'reason' => $request->validated('status'),
            ]);
        }
    }

    public static function get(Agent $agent, TimeScopeEnum $timeScope, CarbonImmutable $dateInScope = null): Collection
    {
        $dateInScope = $dateInScope ?? new CarbonImmutable();

        $paidLeavesWithEndDates = $agent->paidLeaves()->whereNotNull('end_date');

        $advancedQuery = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $paidLeavesWithEndDates
                ->where(function (Builder $query) use ($dateInScope) {
                    $query->whereMonth('end_date', $dateInScope->month)
                        ->orWhereMonth('start_date', $dateInScope->month);
                }),
            TimeScopeEnum::QUARTERLY => $paidLeavesWithEndDates
                ->where(function (Builder $query) use ($dateInScope) {
                    $query->whereBetween('end_date', [$dateInScope->firstOfQuarter(), $dateInScope->lastOfQuarter()])
                        ->orWhereBetween('start_date', [$dateInScope->firstOfQuarter(), $dateInScope->lastOfQuarter()]);
                }),
            TimeScopeEnum::ANNUALY => $paidLeavesWithEndDates
                ->where(function (Builder $query) use ($dateInScope) {
                    $query->whereBetween('end_date', [$dateInScope->firstOfYear(), $dateInScope->lastOfYear()])
                        ->orWhereBetween('start_date', [$dateInScope->firstOfYear(), $dateInScope->lastOfYear()]);
                }),
        };

        return $advancedQuery->get();
    }
}
