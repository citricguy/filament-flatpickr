<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Forms\Components;

/**
 * Date and time picker.
 *
 * This is a convenience wrapper around Flatpickr configured for date and time selection.
 */
class FlatpickrDateTimePicker extends Flatpickr
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->dateTime();
        $this->prefixIcon('heroicon-o-calendar-days');
    }
}
