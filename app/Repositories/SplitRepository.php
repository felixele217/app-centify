<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\StoreSplitRequest;
use App\Models\Deal;
use App\Models\Split;

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
}
