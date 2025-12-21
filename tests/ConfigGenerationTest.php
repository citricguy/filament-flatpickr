<?php

declare(strict_types=1);

use Citricguy\FilamentFlatpickr\Enums\DateRestriction;
use Citricguy\FilamentFlatpickr\Forms\Components\Flatpickr;

describe('Flatpickr Date Constraints in Config', function (): void {
    it('includes minDate in config when set', function (): void {
        $field = Flatpickr::make('test_date')
            ->minDate('2024-01-01');

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('minDate');
        expect($config['minDate'])->toBe('2024-01-01');
    });

    it('includes maxDate in config when set', function (): void {
        $field = Flatpickr::make('test_date')
            ->maxDate('2024-12-31');

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('maxDate');
        expect($config['maxDate'])->toBe('2024-12-31');
    });

    it('correctly applies date restriction setting', function (): void {
        $field = Flatpickr::make('test_date')
            ->dateRestriction(DateRestriction::NoPast);

        // The date restriction is stored on the field
        expect($field->getDateRestriction())->toBe(DateRestriction::NoPast);
    });
});

describe('Flatpickr Time Constraints in Config', function (): void {
    it('includes business hours in config when set', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->businessHours('09:00', '17:00');

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('businessHours');
        expect($config['businessHours']['start'])->toBe('09:00');
        expect($config['businessHours']['end'])->toBe('17:00');
    });

    it('includes hour increment in config', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->hourIncrement(2);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('hourIncrement');
        expect($config['hourIncrement'])->toBe(2);
    });

    it('includes minute increment in config', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->minuteIncrement(15);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('minuteIncrement');
        expect($config['minuteIncrement'])->toBe(15);
    });

    it('includes time24hr setting in config', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->time24hr(false);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('time_24hr');
        expect($config['time_24hr'])->toBeFalse();
    });

    it('includes enableSeconds in config when enabled', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->seconds(true);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('enableSeconds');
        expect($config['enableSeconds'])->toBeTrue();
    });

    it('stores time slot settings on the field', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->useTimeSlots()
            ->timeSlotDurationMinutes(30);

        expect($field->getUseTimeSlots())->toBeTrue();
        expect($field->getTimeSlotDurationMinutes())->toBe(30);
    });
});

describe('Flatpickr Range Constraints in Config', function (): void {
    it('includes minRangeMinutes in config when set', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->minRangeMinutes(60);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('minRangeMinutes');
        expect($config['minRangeMinutes'])->toBe(60);
    });

    it('includes maxRangeMinutes in config when set', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->maxRangeMinutes(1440);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('maxRangeMinutes');
        expect($config['maxRangeMinutes'])->toBe(1440);
    });
});

describe('Flatpickr UI Config', function (): void {
    it('includes inline mode in config', function (): void {
        $field = Flatpickr::make('test_date')
            ->inline(true);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('inline');
        expect($config['inline'])->toBeTrue();
    });

    it('includes weekNumbers in config', function (): void {
        $field = Flatpickr::make('test_date')
            ->weekNumbers(true);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('weekNumbers');
        expect($config['weekNumbers'])->toBeTrue();
    });

    it('includes showMonths in config', function (): void {
        $field = Flatpickr::make('date_range')
            ->range()
            ->showMonths(2);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('showMonths');
        expect($config['showMonths'])->toBe(2);
    });

    it('includes position in config', function (): void {
        $field = Flatpickr::make('test_date');

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('position');
        expect($config['position'])->toBe('auto');
    });

    it('includes allowInput in config', function (): void {
        $field = Flatpickr::make('test_date')
            ->allowInput(true);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('allowInput');
        expect($config['allowInput'])->toBeTrue();
    });

    it('includes disableMobile in config', function (): void {
        $field = Flatpickr::make('test_date')
            ->disableMobile(true);

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('disableMobile');
        expect($config['disableMobile'])->toBeTrue();
    });
});

describe('Flatpickr Timezone Config', function (): void {
    it('includes display timezone in config when set', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->displayTimezone('America/New_York');

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('displayTimezone');
        expect($config['displayTimezone'])->toBe('America/New_York');
    });

    it('includes storage timezone in config', function (): void {
        $field = Flatpickr::make('test_datetime')
            ->dateTime()
            ->storageTimezone('UTC');

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('storageTimezone');
        expect($config['storageTimezone'])->toBe('UTC');
    });
});

describe('Flatpickr Event Hooks Config', function (): void {
    it('includes event hooks in config when set', function (): void {
        $field = Flatpickr::make('test_date')
            ->emitOnChange('date-changed')
            ->emitOnOpen('calendar-opened')
            ->emitOnClose('calendar-closed');

        $config = $field->getFlatpickrConfig();

        expect($config)->toHaveKey('eventHooks');
        expect($config['eventHooks']['onChange']['event'])->toBe('date-changed');
        expect($config['eventHooks']['onOpen']['event'])->toBe('calendar-opened');
        expect($config['eventHooks']['onClose']['event'])->toBe('calendar-closed');
    });
});
