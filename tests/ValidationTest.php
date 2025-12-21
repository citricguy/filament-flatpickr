<?php

declare(strict_types=1);

use Citricguy\FilamentFlatpickr\Enums\DateRestriction;
use Citricguy\FilamentFlatpickr\Enums\FlatpickrMode;
use Citricguy\FilamentFlatpickr\Forms\Components\Flatpickr;

describe('Flatpickr Validation Rules', function (): void {
    it('generates date validation rule for date-only mode', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateOnly()
            ->format('Y-m-d');

        $rules = $field->getValidationRules();

        expect($rules)->toContain('date');
    });

    it('generates after_or_equal validation rule for min date', function (): void {
        $field = Flatpickr::make('test_date')
            ->minDate('2024-01-01');

        $rules = $field->getValidationRules();

        expect($rules)->toContain('after_or_equal:2024-01-01');
    });

    it('generates before_or_equal validation rule for max date', function (): void {
        $field = Flatpickr::make('test_date')
            ->maxDate('2024-12-31');

        $rules = $field->getValidationRules();

        expect($rules)->toContain('before_or_equal:2024-12-31');
    });

    it('can set no past date restriction', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateRestriction(DateRestriction::NoPast);

        expect($field->getDateRestriction())->toBe(DateRestriction::NoPast);
    });

    it('can set no future date restriction', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateRestriction(DateRestriction::NoFuture);

        expect($field->getDateRestriction())->toBe(DateRestriction::NoFuture);
    });
});

describe('Flatpickr Weekday Validation', function (): void {
    it('can be tested with disabled weekdays validation', function (): void {
        $field = Flatpickr::make('test_date')
            ->disabledWeekdays([0, 6]); // Disable Sunday and Saturday

        $weekdays = $field->getDisabledWeekdays();

        expect($weekdays)->toContain(0); // Sunday
        expect($weekdays)->toContain(6); // Saturday
        expect($weekdays)->not->toContain(1); // Monday is allowed
    });
});

describe('Flatpickr Time Configuration for Validation', function (): void {
    it('sets up time-only mode correctly', function (): void {
        $field = Flatpickr::make('test_time')
            ->timeOnly()
            ->format('H:i');

        expect($field->hasTime())->toBeTrue();
        expect($field->hasDate())->toBeFalse();
        expect($field->getFormat())->toBe('H:i');
    });

    it('sets up datetime mode correctly', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->format('Y-m-d H:i:s');

        expect($field->hasTime())->toBeTrue();
        expect($field->hasDate())->toBeTrue();
        expect($field->getFormat())->toBe('Y-m-d H:i:s');
    });
});

describe('Flatpickr Range Validation', function (): void {
    it('applies range mode without additional format restrictions', function (): void {
        $field = Flatpickr::make('date_range')
            ->range();

        // Range mode should work with default configuration
        expect($field->getMode())->toBe(FlatpickrMode::Range);
    });
});

describe('Flatpickr Required Validation', function (): void {
    it('includes required rule when field is required', function (): void {
        $field = Flatpickr::make('test_date')
            ->required();

        $rules = $field->getValidationRules();

        expect($rules)->toContain('required');
    });

    it('can be nullable', function (): void {
        $field = Flatpickr::make('test_date')
            ->nullable();

        // Field should accept null values
        expect($field)->toBeInstanceOf(Flatpickr::class);
    });
});
