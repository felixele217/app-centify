<?php

namespace App\Providers;

use App\Integrations\Pipedrive\PipedriveTokenIO;
use Devio\Pipedrive\Pipedrive;
use Illuminate\Support\ServiceProvider;

class PipedriveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('pipedrive', function () {
            return Pipedrive::OAuth([
                'clientId' => env('PIPEDRIVE_CLIENT_ID'),
                'clientSecret' => env('PIPEDRIVE_CLIENT_SECRET'),
                'redirectUrl' => env('PIPEDRIVE_CALLBACK_URL'),
                'storage' => new PipedriveTokenIO(),
            ]);
        });
    }
}
