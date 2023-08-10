<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePaidLeaveRequest;
use App\Models\Agent;
use App\Models\PaidLeave;
use App\Repositories\PaidLeaveRepository;
use Illuminate\Http\RedirectResponse;

class PaidLeaveController extends Controller
{
    public function store(StorePaidLeaveRequest $request, Agent $agent): RedirectResponse
    {
        PaidLeaveRepository::create($agent, $request);

        return back();
    }

    public function destroy(Agent $agent, PaidLeave $paidLeave): RedirectResponse
    {
        $paidLeave->delete();

        return back();
    }
}
