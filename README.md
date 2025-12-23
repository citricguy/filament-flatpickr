# Filament Flatpickr

[![Latest Version on Packagist](https://img.shields.io/packagist/v/citricguy/filament-flatpickr.svg?style=flat-square)](https://packagist.org/packages/citricguy/filament-flatpickr)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/citricguy/filament-flatpickr/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/citricguy/filament-flatpickr/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/citricguy/filament-flatpickr/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/citricguy/filament-flatpickr/actions?query=workflow%3A"Fix+PHP+Code+Styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/citricguy/filament-flatpickr.svg?style=flat-square)](https://packagist.org/packages/citricguy/filament-flatpickr)

A powerful Flatpickr date/time picker integration for Filament v4. Features timezone handling, date ranges, multiple date selection, time slots, business hours, and 9 built-in themes.

## Requirements

- PHP 8.3+
- Laravel 11+
- Filament v4
- Livewire 3

## Installation

Install the package via Composer:

```bash
composer require citricguy/filament-flatpickr
```

Publish the assets (required):

```bash
php artisan filament:assets
```

## Quick Start

```php
use Citricguy\FilamentFlatpickr\Forms\Components\Flatpickr;

Flatpickr::make('published_at')
    ->dateTime()
```

## Available Components

The package provides one main component with three convenience wrappers:

| Component | Description |
|-----------|-------------|
| `Flatpickr` | Full-featured base component |
| `FlatpickrDatePicker` | Pre-configured date-only picker |
| `FlatpickrTimePicker` | Pre-configured time-only picker |
| `FlatpickrDateTimePicker` | Pre-configured date+time picker |

```php
use Citricguy\FilamentFlatpickr\Forms\Components\FlatpickrDatePicker;
use Citricguy\FilamentFlatpickr\Forms\Components\FlatpickrTimePicker;
use Citricguy\FilamentFlatpickr\Forms\Components\FlatpickrDateTimePicker;

FlatpickrDatePicker::make('birth_date')

FlatpickrTimePicker::make('alarm_time')

FlatpickrDateTimePicker::make('appointment_at')
```

## Selection Modes

### Date Only

```php
Flatpickr::make('date')
    ->dateOnly()
```

### Time Only

```php
Flatpickr::make('time')
    ->timeOnly()
```

### Date and Time

```php
Flatpickr::make('datetime')
    ->dateTime()
```

### Date Range

Select a start and end date:

```php
Flatpickr::make('vacation')
    ->range()
```

For storing start/end dates in separate database columns:

```php
Flatpickr::make('vacation')
    ->range()
    ->dualStatePaths('vacation_start', 'vacation_end')
```

### Multiple Dates

Select multiple individual dates:

```php
Flatpickr::make('blocked_dates')
    ->multiple()
```

## Time Configuration

### 24-Hour Format

```php
Flatpickr::make('time')
    ->timeOnly()
    ->time24hr()
```

### Include Seconds

```php
Flatpickr::make('precise_time')
    ->timeOnly()
    ->seconds()
```

### Time Increments

```php
Flatpickr::make('appointment')
    ->dateTime()
    ->hourIncrement(1)
    ->minuteIncrement(15)
```

### Business Hours

Restrict selection to business hours (validation enforced):

```php
Flatpickr::make('meeting')
    ->dateTime()
    ->businessHours('09:00', '17:00')
```

### Time Slots

Enforce fixed time slot intervals:

```php
Flatpickr::make('appointment')
    ->dateTime()
    ->useTimeSlots()
    ->timeSlotDurationMinutes(30)
```

### Default Time

Set default hour and minute when opening the picker:

```php
Flatpickr::make('start_time')
    ->dateTime()
    ->defaultHour(9)
    ->defaultMinute(0)
```

## Date Constraints

### Minimum and Maximum Dates

```php
Flatpickr::make('future_date')
    ->dateOnly()
    ->minDate(now())
    ->maxDate(now()->addYear())
```

### Date Restrictions

```php
use Citricguy\FilamentFlatpickr\Enums\DateRestriction;

// No past dates
Flatpickr::make('future_event')
    ->dateOnly()
    ->dateRestriction(DateRestriction::NoPast)

// No future dates
Flatpickr::make('birth_date')
    ->dateOnly()
    ->dateRestriction(DateRestriction::NoFuture)
```

### Disable Specific Weekdays

```php
// Disable weekends (0 = Sunday, 6 = Saturday)
Flatpickr::make('business_day')
    ->dateOnly()
    ->disabledWeekdays([0, 6])
```

### Exclude Specific Dates

```php
Flatpickr::make('available_date')
    ->dateOnly()
    ->excludedDates(['2025-12-25', '2025-01-01'])
```

## Range Duration Constraints

For range mode, constrain the minimum and maximum duration:

```php
Flatpickr::make('rental_period')
    ->range()
    ->minRangeMinutes(60 * 24)      // Minimum 1 day
    ->maxRangeMinutes(60 * 24 * 14) // Maximum 14 days
```

## Timezone Handling

### Display and Storage Timezones

```php
Flatpickr::make('event_at')
    ->dateTime()
    ->displayTimezone('America/New_York')
    ->storageTimezone('UTC')
```

### Timezone Modes

```php
use Citricguy\FilamentFlatpickr\Enums\TimezoneMode;

// Use app's default timezone
Flatpickr::make('event_at')
    ->timezoneMode(TimezoneMode::LocationDefault)

// Use a fixed timezone
Flatpickr::make('event_at')
    ->timezoneMode(TimezoneMode::Fixed)
    ->displayTimezone('Europe/London')

// Detect user's browser timezone
Flatpickr::make('event_at')
    ->timezoneMode(TimezoneMode::Submitter)
```

## Formats

### Storage Format

The format used when saving to the database:

```php
Flatpickr::make('date')
    ->format('Y-m-d H:i:s')
```

Default formats by mode:
- Date only: `Y-m-d`
- Time only: `H:i:s`
- DateTime: `Y-m-d H:i:s`

### Display Format

The format shown to the user (uses [Flatpickr tokens](https://flatpickr.js.org/formatting/)):

```php
Flatpickr::make('date')
    ->displayFormat('F j, Y')
    ->useAltInput()
```

## Appearance

### Themes

```php
use Citricguy\FilamentFlatpickr\Enums\FlatpickrTheme;

Flatpickr::make('date')
    ->theme(FlatpickrTheme::Dark)
```

Available themes:
- `Default`
- `Airbnb`
- `Confetti`
- `Dark`
- `Light`
- `MaterialBlue`
- `MaterialGreen`
- `MaterialOrange`
- `MaterialRed`

### Position

```php
use Citricguy\FilamentFlatpickr\Enums\FlatpickrPosition;

Flatpickr::make('date')
    ->position(FlatpickrPosition::AboveRight)
```

Available positions: `Auto`, `Above`, `Below`, `AutoLeft`, `AutoCenter`, `AutoRight`, `AboveLeft`, `AboveCenter`, `AboveRight`, `BelowLeft`, `BelowCenter`, `BelowRight`

### Inline Calendar

Display the calendar inline (always visible):

```php
Flatpickr::make('date')
    ->inline()
```

### Show Multiple Months

```php
Flatpickr::make('date_range')
    ->range()
    ->showMonths(2)
```

### Week Numbers

```php
Flatpickr::make('date')
    ->weekNumbers()
```

## UI Options

### Allow Manual Input

```php
Flatpickr::make('date')
    ->allowInput()
```

### Disable Mobile Native Picker

```php
Flatpickr::make('date')
    ->disableMobile()
```

### Disable Click to Open

```php
Flatpickr::make('date')
    ->clickOpens(false)
```

### Close on Select

```php
Flatpickr::make('date')
    ->closeOnSelect(false) // Keep open after selection
```

## Prefixes and Suffixes

Works with Filament's standard affix system:

```php
Flatpickr::make('date')
    ->prefixIcon('heroicon-o-calendar')
    ->suffixIcon('heroicon-o-clock')
```

## Localization

```php
Flatpickr::make('date')
    ->locale('fr')
```

Flatpickr supports [40+ locales](https://flatpickr.js.org/localization/).

## Livewire Event Hooks

Emit Livewire events when the picker state changes:

```php
Flatpickr::make('date')
    ->emitOnChange('dateSelected', ['dateStr', 'selectedDates'])
    ->emitOnOpen('pickerOpened')
    ->emitOnClose('pickerClosed')
```

Listen in your Livewire component:

```php
#[On('dateSelected')]
public function handleDateChange(string $dateStr, array $selectedDates): void
{
    // Handle the event
}
```

## Enums Reference

### FlatpickrMode

```php
use Citricguy\FilamentFlatpickr\Enums\FlatpickrMode;

FlatpickrMode::Single   // Default single date/time
FlatpickrMode::Range    // Date range selection
FlatpickrMode::Multiple // Multiple date selection
```

### FlatpickrTheme

```php
use Citricguy\FilamentFlatpickr\Enums\FlatpickrTheme;

FlatpickrTheme::Default
FlatpickrTheme::Airbnb
FlatpickrTheme::Confetti
FlatpickrTheme::Dark
FlatpickrTheme::Light
FlatpickrTheme::MaterialBlue
FlatpickrTheme::MaterialGreen
FlatpickrTheme::MaterialOrange
FlatpickrTheme::MaterialRed
```

### FlatpickrPosition

```php
use Citricguy\FilamentFlatpickr\Enums\FlatpickrPosition;

FlatpickrPosition::Auto
FlatpickrPosition::Above
FlatpickrPosition::Below
FlatpickrPosition::AutoLeft
FlatpickrPosition::AutoCenter
FlatpickrPosition::AutoRight
FlatpickrPosition::AboveLeft
FlatpickrPosition::AboveCenter
FlatpickrPosition::AboveRight
FlatpickrPosition::BelowLeft
FlatpickrPosition::BelowCenter
FlatpickrPosition::BelowRight
```

### DateRestriction

```php
use Citricguy\FilamentFlatpickr\Enums\DateRestriction;

DateRestriction::None     // No restriction
DateRestriction::NoPast   // Disable past dates
DateRestriction::NoFuture // Disable future dates
```

### TimezoneMode

```php
use Citricguy\FilamentFlatpickr\Enums\TimezoneMode;

TimezoneMode::LocationDefault // Use app timezone
TimezoneMode::Fixed           // Use specified timezone
TimezoneMode::Submitter       // Detect browser timezone
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Josh Maldonado](https://github.com/citricguy)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
