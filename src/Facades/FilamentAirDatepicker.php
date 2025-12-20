<?php

namespace Citricguy\FilamentAirDatepicker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Citricguy\FilamentAirDatepicker\FilamentAirDatepicker
 */
class FilamentAirDatepicker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Citricguy\FilamentAirDatepicker\FilamentAirDatepicker::class;
    }
}
