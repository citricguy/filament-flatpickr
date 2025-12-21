<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Enums;

enum TimezoneMode: string
{
    case LocationDefault = 'location_default';
    case Fixed = 'fixed';
    case Submitter = 'submitter';
}
