<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSplitRequest;
use App\Models\Deal;
use App\Repositories\SplitRepository;
use Illuminate\Http\RedirectResponse;

class SplitController extends Controller
{
    public function store(StoreSplitRequest $request, Deal $deal): RedirectResponse
    {
        SplitRepository::upsert($deal, $request);

        return back();
    }
}
