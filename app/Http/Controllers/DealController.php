<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDealRequest;
use App\Models\Deal;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class DealController extends Controller
{
    public function update(UpdateDealRequest $request, Deal $deal): RedirectResponse
    {
        if ($request->validated('has_accepted_deal')) {
            $deal->update([
                'accepted_at' => Carbon::now(),
            ]);
        }

        return back();
    }
}
