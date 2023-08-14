<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\PayoutsExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PayoutsExportController extends Controller
{
    public function __invoke()
    {
        return Excel::download(new PayoutsExport(Auth::user()->organization), 'payouts.xlsx');
    }
}
