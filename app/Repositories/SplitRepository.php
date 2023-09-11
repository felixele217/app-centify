<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\TriggerEnum;
use App\Http\Requests\UpsertSplitRequest;
use App\Models\AgentDeal;
use App\Models\Deal;

class SplitRepository
{
    public static function upsert(Deal $deal, UpsertSplitRequest $request): void
    {
        $requestDemoScheduledPartners = [];
        $requestDealWonPartners = [];

        foreach ($request->validated('partners') as $partner) {
            if (isset($partner['demo_scheduled_deal_percentage']) && $partner['demo_scheduled_deal_percentage'] !== 0) {
                AgentDeal::updateOrCreate([
                    'deal_id' => $deal->id,
                    'agent_id' => $partner['id'],
                    'triggered_by' => TriggerEnum::DEMO_SCHEDULED->value,
                ], ['deal_percentage' => $partner['demo_scheduled_deal_percentage']]);

                $requestDemoScheduledPartners[] = $partner['id'];
            }

            if (isset($partner['deal_won_deal_percentage']) && $partner['deal_won_deal_percentage'] !== 0) {
                AgentDeal::updateOrCreate([
                    'deal_id' => $deal->id,
                    'agent_id' => $partner['id'],
                    'triggered_by' => TriggerEnum::DEAL_WON->value,
                ], ['deal_percentage' => $partner['deal_won_deal_percentage']]);

                $requestDealWonPartners[] = $partner['id'];
            }
        }

        foreach ($deal->demoScheduledShareholders as $shareholder) {
            if (! in_array($shareholder->id, $requestDemoScheduledPartners)) {
                $shareholder->pivot->delete();
            }
        }

        foreach ($deal->dealWonShareholders as $shareholder) {
            if (! in_array($shareholder->id, $requestDealWonPartners)) {
                $shareholder->pivot->delete();
            }
        }

        $deal->sdr?->pivot->update(['deal_percentage' => (100 - array_sum(array_map(fn (array $partner) => $partner['demo_scheduled_deal_percentage'], $request->validated('partners'))))]);
        if ($deal->ae) {
            $deal->ae->pivot->update(['deal_percentage' => (100 - array_sum(array_map(fn (array $partner) => $partner['deal_won_deal_percentage'], $request->validated('partners'))))]);
        }
    }
}
