<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\TimeScopeEnum;
use App\Http\Requests\StoreSplitRequest;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Split;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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

        [$firstDateInScope, $lastDateInScope] = match ($timeScope) {
            TimeScopeEnum::MONTHLY => [$dateInScope->firstOfMonth(), $dateInScope->lastOfMonth()],
            TimeScopeEnum::QUARTERLY => [$dateInScope->firstOfQuarter(), $dateInScope->lastOfQuarter()],
            TimeScopeEnum::ANNUALY => [$dateInScope->firstOfYear(), $dateInScope->lastOfYear()],
        };

        return $agent->splits()->acceptedDeals($dateInScope)->whereHas('deal', function (Builder $query) use ($firstDateInScope, $lastDateInScope) {
            $query->whereBetween('add_time', [$firstDateInScope, $lastDateInScope]);
        })->get();
    }
}
