<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Deal;
use App\Models\Agent;
use App\Models\Split;
use App\Enum\TimeScopeEnum;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreSplitRequest;

class SplitRepository
{
    public static function upsert(Deal $deal, StoreSplitRequest $request): void
    {
        foreach ($request->validated('partners') as $partner) {
            Split::updateOrCreate([
                'deal_id' => $deal->id,
                'agent_id' => $partner['id'],
            ], [
                'agent_id' => $partner['id'],
                'shared_percentage' => $partner['shared_percentage'],
                'deal_id' => $deal->id,
            ]);
        }
    }

    public static function splitsForAgent(Agent $agent, TimeScopeEnum $timeScope, CarbonImmutable $dateInScope = null): Collection
    {
        $dateInScope = $dateInScope ?? CarbonImmutable::now();

        $baseQuery = $agent->deals()->whereNotNull('accepted_at')->doesntHave('rejections');

        return match ($timeScope) {
            TimeScopeEnum::MONTHLY => $monthlyDealsQuery = $baseQuery->whereMonth('add_time', $dateInScope->month)->get(),
            TimeScopeEnum::QUARTERLY => $baseQuery->whereBetween('add_time', [$dateInScope->firstOfQuarter(), $dateInScope->endOfQuarter()])->get(),
            TimeScopeEnum::ANNUALY => $baseQuery->whereBetween('add_time', [$dateInScope->firstOfYear(), $dateInScope->lastOfYear()])->get(),
            default => $monthlyDealsQuery
        };
    }
}
