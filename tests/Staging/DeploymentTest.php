<?php

use Illuminate\Support\Facades\Http;

it('deployed the latest version successfully - without any errors', function () {
    $url = 'https://forge.laravel.com/api/v1/servers/'.env('FORGE_SERVER_ID').'/sites/'.env('FORGE_STAGING_SITE_ID').'/deployment-history';

    $response = Http::withHeaders([
        'Authorization' => 'Bearer '.env('FORGE_AUTH_TOKEN'),
    ])->get($url);

    $lastDeploymentStatus = $response->json('deployments')[0]['status'];

    expect($lastDeploymentStatus)->toBe('finished');
});
