<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enum\TimeScopeEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppendTimeScopeQuery
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! count($request->query())) {
            $request->query->set('time_scope', TimeScopeEnum::MONTHLY->value);
        }

        return $next($request);
    }
}
