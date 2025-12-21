<?php

declare(strict_types=1);

use Citricguy\FilamentFlatpickr\Enums\FlatpickrMode;
use Citricguy\FilamentFlatpickr\Forms\Components\Flatpickr;

describe('Flatpickr State Configuration', function (): void {
    it('properly configures for single date mode', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateOnly();

        expect($field->getMode())->toBe(FlatpickrMode::Single);
        expect($field->hasDate())->toBeTrue();
        expect($field->hasTime())->toBeFalse();
    });

    it('properly configures for range mode', function (): void {
        $field = Flatpickr::make('date_range')
            ->range();

        expect($field->getMode())->toBe(FlatpickrMode::Range);
    });

    it('properly configures for multiple mode', function (): void {
        $field = Flatpickr::make('dates')
            ->multiple();

        expect($field->getMode())->toBe(FlatpickrMode::Multiple);
    });

    it('generates default format for date-only mode', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateOnly();

        expect($field->getFormat())->toBe('Y-m-d');
    });

    it('generates default format for time-only mode', function (): void {
        $field = Flatpickr::make('test_time')
            ->timeOnly();

        expect($field->getFormat())->toBe('H:i');
    });

    it('generates default format for datetime mode', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime();

        expect($field->getFormat())->toBe('Y-m-d H:i');
    });

    it('generates default format with seconds enabled', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->seconds(true);

        expect($field->getFormat())->toBe('Y-m-d H:i:s');
    });

    it('generates time-only default format with seconds', function (): void {
        $field = Flatpickr::make('test_time')
            ->timeOnly()
            ->seconds(true);

        expect($field->getFormat())->toBe('H:i:s');
    });

    it('allows custom format override', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateOnly()
            ->format('d/m/Y');

        expect($field->getFormat())->toBe('d/m/Y');
    });

    it('allows custom display format', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateOnly()
            ->displayFormat('l, F j, Y');

        $displayFormat = $field->getDisplayFormat();
        expect($displayFormat)->toContain('l');
        expect($displayFormat)->toContain('F');
        expect($displayFormat)->toContain('j');
        expect($displayFormat)->toContain('Y');
    });
});

describe('Flatpickr Dual State Path Mode', function (): void {
    it('enables dual state path mode', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->dualStatePaths('start_date', 'end_date');

        expect($field->isDualStatePathMode())->toBeTrue();
    });

    it('returns start state path', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->dualStatePaths('start_date', 'end_date');

        expect($field->getStartStatePath())->toBe('start_date');
    });

    it('returns end state path', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->dualStatePaths('start_date', 'end_date');

        expect($field->getEndStatePath())->toBe('end_date');
    });

    it('is not in dual state path mode by default', function (): void {
        $field = Flatpickr::make('date_range')
            ->range();

        expect($field->isDualStatePathMode())->toBeFalse();
    });
});

describe('Flatpickr Config Hash', function (): void {
    it('generates consistent config hash for same configuration', function (): void {
        $field1 = Flatpickr::make('test_date')
            ->dateOnly()
            ->format('Y-m-d');

        $field2 = Flatpickr::make('test_date')
            ->dateOnly()
            ->format('Y-m-d');

        $config1 = $field1->getFlatpickrConfig();
        $config2 = $field2->getFlatpickrConfig();

        expect(md5(json_encode($config1)))->toBe(md5(json_encode($config2)));
    });

    it('generates different config hash for different configuration', function (): void {
        $field1 = Flatpickr::make('test_date')
            ->dateOnly()
            ->format('Y-m-d');

        $field2 = Flatpickr::make('test_date')
            ->dateTime()
            ->format('Y-m-d H:i');

        $config1 = $field1->getFlatpickrConfig();
        $config2 = $field2->getFlatpickrConfig();

        expect(md5(json_encode($config1)))->not->toBe(md5(json_encode($config2)));
    });
});
