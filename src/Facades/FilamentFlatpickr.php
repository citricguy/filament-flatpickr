<?php

namespace Citricguy\FilamentFlatpickr\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Citricguy\FilamentFlatpickr\FilamentFlatpickr
 */
class FilamentFlatpickr extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Citricguy\FilamentFlatpickr\FilamentFlatpickr::class;
    }
}
