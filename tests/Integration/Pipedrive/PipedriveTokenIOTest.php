<?php

use App\Integrations\Pipedrive\PipedriveTokenIO;
use App\Models\Integration;
use Carbon\Carbon;
use Devio\Pipedrive\PipedriveToken;

it('tokens are saved and retrieved with encryption so they return the original', function () {
    $admin = signInAdmin();

    $io = new PipedriveTokenIO($admin->organization_id);

    $io->setToken(new PipedriveToken([
        'accessToken' => $accessToken = 'asdfko12ijasdfml',
        'refreshToken' => $refreshToken = 'ojasd0fm129j10293j',
        'expiresAt' => $expiresAt = Carbon::now()->timestamp,
    ]));

    $retrievedToken = $io->getToken();

    expect($retrievedToken->getAccessToken())->toBe($accessToken);
    expect($retrievedToken->getRefreshToken())->toBe($refreshToken);
    expect($retrievedToken->expiresAt())->toBe($expiresAt);
});

it('updates the existing token model instead of creating a new one on setToken', function () {
    $admin = signInAdmin();

    $io = new PipedriveTokenIO($admin->organization_id);

    $io->setToken(new PipedriveToken([
        'accessToken' => 'asdfko12ijasdfml',
        'refreshToken' => 'ojasd0fm129j10293j',
        'expiresAt' => Carbon::now()->timestamp,
    ]));

    $io->setToken(new PipedriveToken([
        'accessToken' => $newAccessToken = 'asdfko12ij123123asdfml',
        'refreshToken' => $newRefreshToken = 'ojasd0fm129j10123293j',
        'expiresAt' => $newExpiredAt = Carbon::tomorrow()->timestamp,
    ]));

    $newToken = $io->getToken();

    expect(Integration::count())->toBe(1);
    expect($newToken->getAccessToken())->toBe($newAccessToken);
    expect($newToken->getRefreshToken())->toBe($newRefreshToken);
    expect($newToken->expiresAt())->toBe($newExpiredAt);
});
