<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\StorePaidLeaveRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
        $paidLeavesWithEndDates = $agent->paidLeaves()->whereNotNull('end_date');

        [$firstDateInScope, $lastDateInScope] = DateHelper::firstAndLastDateInScope($dateInScope ?? CarbonImmutable::now(), $timeScope);

        return $paidLeavesWithEndDates->where(function (Builder $query) use ($firstDateInScope, $lastDateInScope) {
            $query->whereBetween('end_date', [$firstDateInScope, $lastDateInScope])
                ->orWhereBetween('start_date', [$firstDateInScope, $lastDateInScope]);
        })->get();
    }
}
