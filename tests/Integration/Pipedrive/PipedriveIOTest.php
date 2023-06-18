<?php

use App\Integrations\Pipedrive\PipedriveTokenIO;
use Carbon\Carbon;
use Devio\Pipedrive\PipedriveToken;

it('tokens are saved and retrieved with encryption so they return the original', function () {
    signIn();

    $io = new PipedriveTokenIO();

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
