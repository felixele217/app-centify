<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpsertSplitRequest;
use App\Models\Deal;
use App\Repositories\SplitRepository;
use Illuminate\Http\RedirectResponse;

class SplitController extends Controller
{
    public function store(UpsertSplitRequest $request, Deal $deal): RedirectResponse
    {
        SplitRepository::upsert($deal, $request);

        return back();
    }
}
