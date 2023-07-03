<?php

namespace App\Providers;

use App\Integrations\Pipedrive\PipedriveClientDummy;
use App\Integrations\Pipedrive\PipedriveTokenIO;
use Devio\Pipedrive\Pipedrive;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class PipedriveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('pipedrive', function () {
            if (App::environment() === 'testing') {
                return new PipedriveClientDummy();
            }

            return Pipedrive::OAuth([
                'clientId' => env('PIPEDRIVE_CLIENT_ID'),
                'clientSecret' => env('PIPEDRIVE_CLIENT_SECRET'),
                'redirectUrl' => env('PIPEDRIVE_CALLBACK_URL'),
                'storage' => new PipedriveTokenIO(),
            ]);
        });
    }
}
