<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreDealRejectionRequest;
use App\Models\Deal;
use Illuminate\Http\RedirectResponse;

class DealRejectionController extends Controller
{
    public function store(StoreDealRejectionRequest $request, Deal $deal): RedirectResponse
    {
        $deal->rejections()->create([
            'reason' => $request->validated('rejection_reason'),
            'is_permanent' => $request->validated('is_permanent'),
        ]);

        return back();
    }
}
