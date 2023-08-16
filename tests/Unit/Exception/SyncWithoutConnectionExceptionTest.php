<?php

use App\Exceptions\SyncWithoutConnectionException;

it('has the correct message', function () {
    $exception = new SyncWithoutConnectionException();

    expect($exception->getMessage())->toBe('You are not connected to the integration! Please connect before trying to synchronize the deals.');
});
