<?php

declare(strict_types=1);

namespace App\Facades;

use App\Integrations\Pipedrive\Mocking\PipedriveClientDummy;
use App\Integrations\Pipedrive\PipedriveTokenIO;
use App\Models\Organization;
use Devio\Pipedrive\Pipedrive;
use Illuminate\Support\Facades\App;

class PipedriveFacade
{
    private Pipedrive|PipedriveClientDummy $pipedriveClient;

    public function __construct(Organization $organization)
    {
        if (App::environment() === 'testing') {
            $this->pipedriveClient = new PipedriveClientDummy($organization);
        } else {
            $this->pipedriveClient = Pipedrive::OAuth([
                'clientId' => env('PIPEDRIVE_CLIENT_ID'),
                'clientSecret' => env('PIPEDRIVE_CLIENT_SECRET'),
                'redirectUrl' => env('PIPEDRIVE_CALLBACK_URL'),
                'storage' => new PipedriveTokenIO($organization),
            ]);
        }
    }

    public function authorize(string $code): void
    {
        $this->pipedriveClient->authorize($code);
    }

    public function deals(): array
    {
        if (App::environment() === 'testing') {
            return $this->pipedriveClient->deals()->toArray();
        }

        return json_decode(json_encode($this->pipedriveClient->deals()->all()->getData()), true);
    }
}