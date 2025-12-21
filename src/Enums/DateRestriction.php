<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Enums;

enum DateRestriction: string
{
    case None = 'none';
    case NoPast = 'no_past';
    case NoFuture = 'no_future';
}
