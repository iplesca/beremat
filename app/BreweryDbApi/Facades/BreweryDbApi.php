<?php

namespace App\BreweryDbApi\Facades;

use Illuminate\Support\Facades\Facade;

class BreweryDbApi extends Facade
{
    protected static function getFacadeAccessor()
    { 
        return 'BreweryDbApi'; 
    }
}