<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class SyncWithoutConnectionException extends Exception
{
    public function __construct()
    {
        $this->message = 'You are not connected to the integration! Please connect before trying to synchronize the deals.';
    }
}
