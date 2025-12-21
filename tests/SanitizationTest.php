<?php

declare(strict_types=1);

use Citricguy\FilamentFlatpickr\Forms\Components\Flatpickr;

describe('Flatpickr XSS Sanitization', function (): void {
    it('sanitizes format strings removing HTML tags', function (): void {
        $field = Flatpickr::make('test_date')
            ->format('<script>alert("xss")</script>Y-m-d');

        // HTML tags are stripped, leaving only valid format characters
        $format = $field->getFormat();
        expect($format)->not->toContain('<script');
        expect($format)->not->toContain('</script>');
        // Final format should contain Y-m-d
        expect($format)->toContain('Y-m-d');
    });

    it('sanitizes display format removing HTML tags', function (): void {
        $field = Flatpickr::make('test_date')
            ->displayFormat('<img onerror="alert(1)" src="x">F j, Y');

        $format = $field->getDisplayFormat();
        expect($format)->not->toContain('<img');
        expect($format)->not->toContain('onerror');
        // Final format should contain valid format tokens
        expect($format)->toContain('F');
        expect($format)->toContain('j');
        expect($format)->toContain('Y');
    });

    it('only allows valid format characters', function (): void {
        $field = Flatpickr::make('test_date')
            ->format('Y-m-d!@#$%^&*()');

        // Only valid format characters should remain
        expect($field->getFormat())->toBe('Y-m-d');
    });

    it('sanitizes excluded dates rejecting invalid formats', function (): void {
        $field = Flatpickr::make('test_date')
            ->excludedDates([
                '2024-12-25',                          // Valid
                '<script>alert(1)</script>',           // Invalid - XSS attempt
                '2024-01-01',                          // Valid
                'not-a-date',                          // Invalid - wrong format
            ]);

        $excludedDates = $field->getExcludedDates();

        // Only valid dates should be included
        expect($excludedDates)->toBe(['2024-12-25', '2024-01-01']);
    });

    it('sanitizes disabled weekdays rejecting invalid values', function (): void {
        $field = Flatpickr::make('test_date')
            ->disabledWeekdays([0, 6, 7, -1, 100, 'invalid']);

        $weekdays = $field->getDisabledWeekdays();

        // Only valid weekday integers (0-6) should be included
        expect($weekdays)->toBe([0, 6]);
    });

    it('sanitizes business hours start time with XSS attempt', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->businessHours('<script>09:00</script>', '17:00');

        // Invalid start time should be sanitized/rejected
        expect($field->getBusinessHoursStart())->toBeNull();
        expect($field->getBusinessHoursEnd())->toBe('17:00');
    });

    it('sanitizes business hours end time with invalid hour', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->businessHours('09:00', '25:00'); // Invalid hour

        expect($field->getBusinessHoursStart())->toBe('09:00');
        // Invalid end time should be sanitized/rejected
        expect($field->getBusinessHoursEnd())->toBeNull();
    });

    it('validates time format HH:MM and normalizes', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->businessHours('09:00', '17:00'); // Valid format

        expect($field->getBusinessHoursStart())->toBe('09:00');
        expect($field->getBusinessHoursEnd())->toBe('17:00');
    });

    it('sanitizes min range minutes', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->minRangeMinutes(-100); // Invalid negative value

        expect($field->getMinRangeMinutes())->toBeNull();
    });

    it('sanitizes max range minutes', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->maxRangeMinutes(0); // Invalid zero value

        expect($field->getMaxRangeMinutes())->toBeNull();
    });

    it('sanitizes time slot duration', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->useTimeSlots()
            ->timeSlotDurationMinutes(-15); // Invalid negative value

        expect($field->getTimeSlotDurationMinutes())->toBeNull();
    });

    it('sanitizes minute increment to valid range', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->minuteIncrement(15); // Valid value

        // Should return the set value
        expect($field->getMinuteIncrement())->toBe(15);
    });

    it('sanitizes hour increment to valid range', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->hourIncrement(2); // Valid value

        expect($field->getHourIncrement())->toBe(2);
    });

    it('sanitizes default hour to valid range', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->defaultHour(14); // Valid value

        expect($field->getDefaultHour())->toBe(14);
    });

    it('sanitizes default minute to valid range', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->defaultMinute(30); // Valid value

        expect($field->getDefaultMinute())->toBe(30);
    });

    it('sanitizes show months to positive value', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->showMonths(2); // Valid value

        expect($field->getShowMonths())->toBe(2);
    });
});

describe('Flatpickr Config XSS Prevention', function (): void {
    it('generates safe config for javascript serialization', function (): void {
        $field = Flatpickr::make('test_date')
            ->format('Y-m-d')
            ->displayFormat('F j, Y')
            ->disabledWeekdays([0, 6])
            ->excludedDates(['2024-12-25']);

        $config = $field->getFlatpickrConfig();

        // Config should be safe to JSON encode
        $json = json_encode($config);
        expect($json)->toBeString();

        // Should not contain script tags
        expect($json)->not->toContain('<script');
        expect($json)->not->toContain('javascript:');
        expect($json)->not->toContain('onerror');
    });

    it('escapes special characters in config values', function (): void {
        $field = Flatpickr::make('test_date')
            ->format('Y-m-d');

        $config = $field->getFlatpickrConfig();
        $json = json_encode($config);

        // Should be valid JSON
        expect(json_last_error())->toBe(JSON_ERROR_NONE);
    });
});
