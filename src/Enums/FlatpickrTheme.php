<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Enums;

enum FlatpickrTheme: string
{
    case Default = 'default';
    case Airbnb = 'airbnb';
    case Confetti = 'confetti';
    case Dark = 'dark';
    case Light = 'light';
    case MaterialBlue = 'material_blue';
    case MaterialGreen = 'material_green';
    case MaterialOrange = 'material_orange';
    case MaterialRed = 'material_red';

    /**
     * Get the CSS asset URL for this theme.
     */
    public function getAssetUrl(): string
    {
        return asset("vendor/filament-flatpickr/themes/{$this->value}.css");
    }
}
