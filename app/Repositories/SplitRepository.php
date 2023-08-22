<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Http\Requests\UpsertSplitRequest;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class SplitRepository
{
    public static function upsert(Deal $deal, UpsertSplitRequest $request): void
    {
        $requestPartnerIds = [];

        foreach ($request->validated('partners') as $partner) {
            AgentDeal::updateOrCreate([
                'deal_id' => $deal->id,
                'agent_id' => $partner['id'],
            ], [
                'deal_id' => $deal->id,
                'agent_id' => $partner['id'],
                'deal_percentage' => $partner['shared_percentage'],
            ]);

            $requestPartnerIds[] = $partner['id'];
        }

        foreach ($deal->agents()->wherePivotNull('triggered_by')->get() as $agentDeal) {
            if (! in_array($agentDeal->pivot->agent_id, $requestPartnerIds)) {
                $agentDeal->pivot->delete();
            }
        }

        if ($deal->won_time) {
            $deal->ae?->pivot->update(['deal_percentage' => 100 - array_sum(array_map(fn ($partner) => $partner['shared_percentage'], $request->validated('partners')))]);
        } else {
            $deal->sdr?->pivot->update(['deal_percentage' => 100 - array_sum(array_map(fn ($partner) => $partner['shared_percentage'], $request->validated('partners')))]);
        }
    }
}
