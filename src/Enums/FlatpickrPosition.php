<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Enums;

enum FlatpickrPosition: string
{
    case Auto = 'auto';
    case Above = 'above';
    case Below = 'below';
    case AutoLeft = 'auto left';
    case AutoCenter = 'auto center';
    case AutoRight = 'auto right';
    case AboveLeft = 'above left';
    case AboveCenter = 'above center';
    case AboveRight = 'above right';
    case BelowLeft = 'below left';
    case BelowCenter = 'below center';
    case BelowRight = 'below right';
}
