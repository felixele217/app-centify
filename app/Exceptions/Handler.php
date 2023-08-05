<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (InvalidApiKeyException $exception) {
            return back()->withErrors([
                'invalid_api_key' => $exception->getMessage(),
            ]);
        });
    }
}
