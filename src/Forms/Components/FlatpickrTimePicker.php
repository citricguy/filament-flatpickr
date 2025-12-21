<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Forms\Components;

/**
 * Time-only picker (no date selection).
 *
 * This is a convenience wrapper around Flatpickr configured for time-only selection.
 */
class FlatpickrTimePicker extends Flatpickr
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->timeOnly();
        $this->prefixIcon('heroicon-o-clock');
    }
}
