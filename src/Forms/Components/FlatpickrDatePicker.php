<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Forms\Components;

/**
 * Date-only picker (no time selection).
 *
 * This is a convenience wrapper around Flatpickr configured for date-only selection.
 */
class FlatpickrDatePicker extends Flatpickr
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->dateOnly();
        $this->prefixIcon('heroicon-o-calendar');
    }
}
