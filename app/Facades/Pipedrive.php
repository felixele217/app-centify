<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Pipedrive extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pipedrive';
    }
}
