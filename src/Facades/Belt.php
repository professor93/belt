<?php

namespace Uzbek\Belt\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Uzbek\Belt\Belt
 */
class Belt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Uzbek\Belt\Belt::class;
    }
}
