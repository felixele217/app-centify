<?php

use App\Exceptions\InvalidApiKeyException;

it('has the correct message', function () {
    $exception = new InvalidApiKeyException();

    expect($exception->getMessage())->toBe("Invalid demo_set_by api key! Please check your integration's settings.");
});
