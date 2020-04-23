<?php

namespace Alexmg86\LaravelSubQuery\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelSubQuery extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-sub-query';
    }
}
