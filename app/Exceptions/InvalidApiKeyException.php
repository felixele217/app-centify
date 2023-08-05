<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InvalidApiKeyException extends Exception
{
    public function __construct()
    {
        $this->message = "Invalid demo_set_by api key! Please check your integration's settings.";
    }
}
