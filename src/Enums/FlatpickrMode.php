<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Enums;

enum FlatpickrMode: string
{
    case Single = 'single';
    case Range = 'range';
    case Multiple = 'multiple';
}
