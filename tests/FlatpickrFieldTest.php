<?php

declare(strict_types=1);

use Citricguy\FilamentFlatpickr\Enums\DateRestriction;
use Citricguy\FilamentFlatpickr\Enums\FlatpickrMode;
use Citricguy\FilamentFlatpickr\Enums\FlatpickrMonthSelectorType;
use Citricguy\FilamentFlatpickr\Enums\FlatpickrPosition;
use Citricguy\FilamentFlatpickr\Enums\FlatpickrTheme;
use Citricguy\FilamentFlatpickr\Enums\TimezoneMode;
use Citricguy\FilamentFlatpickr\Forms\Components\Flatpickr;
use Citricguy\FilamentFlatpickr\Forms\Components\FlatpickrDatePicker;
use Citricguy\FilamentFlatpickr\Forms\Components\FlatpickrDateTimePicker;
use Citricguy\FilamentFlatpickr\Forms\Components\FlatpickrTimePicker;

describe('Flatpickr Field Configuration', function (): void {
    it('creates a field with default configuration', function (): void {
        $field = Flatpickr::make('test_date');

        expect($field->getName())->toBe('test_date');
        expect($field->getMode())->toBe(FlatpickrMode::Single);
        expect($field->hasTime())->toBeFalse();
        expect($field->hasDate())->toBeTrue();
    });

    it('can set date-only mode', function (): void {
        $field = Flatpickr::make('test_date')->dateOnly();

        expect($field->getMode())->toBe(FlatpickrMode::Single);
        expect($field->hasTime())->toBeFalse();
        expect($field->hasDate())->toBeTrue();
    });

    it('can set time-only mode', function (): void {
        $field = Flatpickr::make('test_time')->timeOnly();

        expect($field->hasTime())->toBeTrue();
        expect($field->hasDate())->toBeFalse();
    });

    it('can set datetime mode', function (): void {
        $field = Flatpickr::make('test_datetime')->dateTime();

        expect($field->hasTime())->toBeTrue();
        expect($field->hasDate())->toBeTrue();
    });

    it('can set range mode', function (): void {
        $field = Flatpickr::make('date_range')->range();

        expect($field->getMode())->toBe(FlatpickrMode::Range);
    });

    it('can set multiple mode', function (): void {
        $field = Flatpickr::make('dates')->multiple();

        expect($field->getMode())->toBe(FlatpickrMode::Multiple);
    });
});

describe('Flatpickr Field Format Configuration', function (): void {
    it('can set format for date storage', function (): void {
        $field = Flatpickr::make('test_date')->format('Y-m-d');

        expect($field->getFormat())->toBe('Y-m-d');
    });

    it('can set display format', function (): void {
        $field = Flatpickr::make('test_date')->displayFormat('F j, Y');

        expect($field->getDisplayFormat())->toBe('F j, Y');
    });

    it('can set time format to 12 hour', function (): void {
        $field = Flatpickr::make('test_time')
            ->timeOnly()
            ->time24hr(false);

        expect($field->getTime24hr())->toBeFalse();
    });

    it('can set time format to 24 hour', function (): void {
        $field = Flatpickr::make('test_time')
            ->timeOnly()
            ->time24hr(true);

        expect($field->getTime24hr())->toBeTrue();
    });

    it('can enable seconds', function (): void {
        $field = Flatpickr::make('test_time')
            ->timeOnly()
            ->seconds(true);

        expect($field->hasSeconds())->toBeTrue();
    });
});

describe('Flatpickr Field Timezone Configuration', function (): void {
    it('can set display timezone', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->displayTimezone('America/New_York');

        expect($field->getDisplayTimezone())->toBe('America/New_York');
    });

    it('can set storage timezone', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->storageTimezone('UTC');

        expect($field->getStorageTimezone())->toBe('UTC');
    });

    it('can set timezone mode to fixed', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->timezoneMode(TimezoneMode::Fixed);

        expect($field->getTimezoneMode())->toBe(TimezoneMode::Fixed);
    });

    it('can set timezone mode to submitter', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->timezoneMode(TimezoneMode::Submitter);

        expect($field->getTimezoneMode())->toBe(TimezoneMode::Submitter);
    });
});

describe('Flatpickr Field Date Constraints', function (): void {
    it('can set minimum date', function (): void {
        $field = Flatpickr::make('test_date')->minDate('2024-01-01');

        expect($field->getMinDate())->toBe('2024-01-01');
    });

    it('can set maximum date', function (): void {
        $field = Flatpickr::make('test_date')->maxDate('2024-12-31');

        expect($field->getMaxDate())->toBe('2024-12-31');
    });

    it('can set date restriction to no past', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateRestriction(DateRestriction::NoPast);

        expect($field->getDateRestriction())->toBe(DateRestriction::NoPast);
    });

    it('can set date restriction to no future', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateRestriction(DateRestriction::NoFuture);

        expect($field->getDateRestriction())->toBe(DateRestriction::NoFuture);
    });

    it('can set disabled weekdays', function (): void {
        $field = Flatpickr::make('test_date')
            ->disabledWeekdays([0, 6]); // Sunday, Saturday

        expect($field->getDisabledWeekdays())->toBe([0, 6]);
    });

    it('can set excluded dates', function (): void {
        $excludedDates = ['2024-12-25', '2024-01-01'];
        $field = Flatpickr::make('test_date')
            ->excludedDates($excludedDates);

        expect($field->getExcludedDates())->toBe($excludedDates);
    });
});

describe('Flatpickr Field Time Constraints', function (): void {
    it('can set business hours', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->businessHours('09:00', '17:00');

        expect($field->getBusinessHoursStart())->toBe('09:00');
        expect($field->getBusinessHoursEnd())->toBe('17:00');
    });

    it('can enable time slots', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->useTimeSlots()
            ->timeSlotDurationMinutes(30);

        expect($field->getUseTimeSlots())->toBeTrue();
        expect($field->getTimeSlotDurationMinutes())->toBe(30);
    });

    it('can set minute increment', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->minuteIncrement(15);

        expect($field->getMinuteIncrement())->toBe(15);
    });

    it('can set hour increment', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->hourIncrement(2);

        expect($field->getHourIncrement())->toBe(2);
    });

    it('can set default hour', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->defaultHour(14);

        expect($field->getDefaultHour())->toBe(14);
    });

    it('can set default minute', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->defaultMinute(30);

        expect($field->getDefaultMinute())->toBe(30);
    });
});

describe('Flatpickr Field Range Constraints', function (): void {
    it('can set minimum range in minutes', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->minRangeMinutes(60);

        expect($field->getMinRangeMinutes())->toBe(60);
    });

    it('can set maximum range in minutes', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->maxRangeMinutes(1440); // 1 day

        expect($field->getMaxRangeMinutes())->toBe(1440);
    });

    it('can set dual state paths for range mode', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->dualStatePaths('start_date', 'end_date');

        expect($field->isDualStatePathMode())->toBeTrue();
        expect($field->getStartStatePath())->toBe('start_date');
        expect($field->getEndStatePath())->toBe('end_date');
    });
});

describe('Flatpickr Field UI Configuration', function (): void {
    it('can set inline mode', function (): void {
        $field = Flatpickr::make('test_date')->inline(true);

        expect($field->getInline())->toBeTrue();
    });

    it('can set week numbers', function (): void {
        $field = Flatpickr::make('test_date')->weekNumbers(true);

        expect($field->getWeekNumbers())->toBeTrue();
    });

    it('can set multiple months', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->showMonths(2);

        expect($field->getShowMonths())->toBe(2);
    });

    it('can set position', function (): void {
        $field = Flatpickr::make('test_date')
            ->position(FlatpickrPosition::Above);

        expect($field->getPosition())->toBe(FlatpickrPosition::Above);
    });

    it('can allow keyboard input', function (): void {
        $field = Flatpickr::make('test_date')->allowInput(true);

        expect($field->getAllowInput())->toBeTrue();
    });

    it('can disable mobile mode', function (): void {
        $field = Flatpickr::make('test_date')->disableMobile(true);

        expect($field->getDisableMobile())->toBeTrue();
    });
});

describe('Flatpickr Field Events', function (): void {
    it('can emit on change', function (): void {
        $field = Flatpickr::make('test_date')->emitOnChange('date-changed');

        $hooks = $field->getEventHooks();
        expect($hooks)->toBeArray();
        expect($hooks['onChange']['event'])->toBe('date-changed');
    });

    it('can emit on open', function (): void {
        $field = Flatpickr::make('test_date')->emitOnOpen('calendar-opened');

        $hooks = $field->getEventHooks();
        expect($hooks)->toBeArray();
        expect($hooks['onOpen']['event'])->toBe('calendar-opened');
    });

    it('can emit on close', function (): void {
        $field = Flatpickr::make('test_date')->emitOnClose('calendar-closed');

        $hooks = $field->getEventHooks();
        expect($hooks)->toBeArray();
        expect($hooks['onClose']['event'])->toBe('calendar-closed');
    });
});

describe('Flatpickr Config Generation', function (): void {
    it('generates valid flatpickr config for date-only mode', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateOnly()
            ->format('Y-m-d')
            ->displayFormat('F j, Y');

        $config = $field->getFlatpickrConfig();

        expect($config)->toBeArray();
        expect($config['mode'])->toBe('single');
        expect($config['enableTime'])->toBeFalse();
        expect($config['noCalendar'])->toBeFalse();
        expect($config['dateFormat'])->toBe('Y-m-d');
        expect($config['altFormat'])->toBe('F j, Y');
    });

    it('generates valid flatpickr config for time-only mode', function (): void {
        $field = Flatpickr::make('test_time')
            ->timeOnly()
            ->time24hr(true)
            ->minuteIncrement(15);

        $config = $field->getFlatpickrConfig();

        expect($config)->toBeArray();
        expect($config['enableTime'])->toBeTrue();
        expect($config['noCalendar'])->toBeTrue();
        expect($config['time_24hr'])->toBeTrue();
        expect($config['minuteIncrement'])->toBe(15);
    });

    it('generates valid flatpickr config for datetime mode', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->displayTimezone('America/New_York')
            ->storageTimezone('UTC');

        $config = $field->getFlatpickrConfig();

        expect($config)->toBeArray();
        expect($config['enableTime'])->toBeTrue();
        expect($config['noCalendar'])->toBeFalse();
        expect($config['displayTimezone'])->toBe('America/New_York');
        expect($config['storageTimezone'])->toBe('UTC');
    });

    it('generates valid flatpickr config for range mode', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->showMonths(2);

        $config = $field->getFlatpickrConfig();

        expect($config)->toBeArray();
        expect($config['mode'])->toBe('range');
        expect($config['showMonths'])->toBe(2);
    });

    it('includes disabled weekdays in config', function (): void {
        $field = Flatpickr::make('test_date')
            ->disabledWeekdays([0, 6]);

        $config = $field->getFlatpickrConfig();

        expect($config['disabledWeekdays'])->toBe([0, 6]);
    });

    it('includes excluded dates in config', function (): void {
        $excludedDates = ['2024-12-25', '2024-01-01'];
        $field = Flatpickr::make('test_date')
            ->excludedDates($excludedDates);

        $config = $field->getFlatpickrConfig();

        expect($config['disable'])->toBe($excludedDates);
    });

    it('includes business hours in config', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->businessHours('09:00', '17:00');

        $config = $field->getFlatpickrConfig();

        expect($config['businessHours'])->toBeArray();
        expect($config['businessHours']['start'])->toBe('09:00');
        expect($config['businessHours']['end'])->toBe('17:00');
    });
});

describe('Flatpickr Preset Classes', function (): void {
    it('FlatpickrDatePicker is configured correctly', function (): void {
        $field = FlatpickrDatePicker::make('date');

        expect($field->getMode())->toBe(FlatpickrMode::Single);
        expect($field->hasTime())->toBeFalse();
        expect($field->hasDate())->toBeTrue();
    });

    it('FlatpickrDateTimePicker is configured correctly', function (): void {
        $field = FlatpickrDateTimePicker::make('datetime');

        expect($field->hasTime())->toBeTrue();
        expect($field->hasDate())->toBeTrue();
    });

    it('FlatpickrTimePicker is configured correctly', function (): void {
        $field = FlatpickrTimePicker::make('time');

        expect($field->hasTime())->toBeTrue();
        expect($field->hasDate())->toBeFalse();
    });
});

describe('Flatpickr Enums', function (): void {
    it('FlatpickrMode enum has expected values', function (): void {
        expect(FlatpickrMode::Single->value)->toBe('single');
        expect(FlatpickrMode::Multiple->value)->toBe('multiple');
        expect(FlatpickrMode::Range->value)->toBe('range');
    });

    it('FlatpickrPosition enum has expected values', function (): void {
        expect(FlatpickrPosition::Auto->value)->toBe('auto');
        expect(FlatpickrPosition::Above->value)->toBe('above');
        expect(FlatpickrPosition::Below->value)->toBe('below');
    });

    it('DateRestriction enum has expected values', function (): void {
        expect(DateRestriction::None->value)->toBe('none');
        expect(DateRestriction::NoPast->value)->toBe('no_past');
        expect(DateRestriction::NoFuture->value)->toBe('no_future');
    });

    it('TimezoneMode enum has expected values', function (): void {
        expect(TimezoneMode::LocationDefault->value)->toBe('location_default');
        expect(TimezoneMode::Fixed->value)->toBe('fixed');
        expect(TimezoneMode::Submitter->value)->toBe('submitter');
    });

    it('FlatpickrMonthSelectorType enum has expected values', function (): void {
        expect(FlatpickrMonthSelectorType::Dropdown->value)->toBe('dropdown');
        expect(FlatpickrMonthSelectorType::Static->value)->toBe('static');
    });

    it('FlatpickrTheme enum has expected values', function (): void {
        expect(FlatpickrTheme::Default->value)->toBe('default');
        expect(FlatpickrTheme::Dark->value)->toBe('dark');
        expect(FlatpickrTheme::Light->value)->toBe('light');
    });
});
