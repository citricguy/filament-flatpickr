<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Forms\Components;

use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Citricguy\FilamentFlatpickr\Concerns\SanitizesConfiguration;
use Citricguy\FilamentFlatpickr\Enums\DateRestriction;
use Citricguy\FilamentFlatpickr\Enums\FlatpickrMode;
use Citricguy\FilamentFlatpickr\Enums\FlatpickrMonthSelectorType;
use Citricguy\FilamentFlatpickr\Enums\FlatpickrPosition;
use Citricguy\FilamentFlatpickr\Enums\FlatpickrTheme;
use Citricguy\FilamentFlatpickr\Enums\TimezoneMode;
use Closure;
use Filament\Forms\Components\Concerns\HasAffixes;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Filament\Forms\Components\Field;
use Illuminate\Support\Carbon;
use Illuminate\View\ComponentAttributeBag;

class Flatpickr extends Field
{
    use HasAffixes;
    use HasPlaceholder;
    use SanitizesConfiguration;

    protected string $view = 'filament-flatpickr::forms.components.flatpickr';

    // Core configuration
    protected bool | Closure $hasTime = false;

    protected bool | Closure $hasDate = true;

    protected bool | Closure $hasSeconds = false;

    protected string | Closure | null $displayFormat = null;

    protected string | Closure | null $format = null;

    protected FlatpickrMode | Closure $mode = FlatpickrMode::Single;

    // Timezone configuration
    protected string | Closure | null $displayTimezone = null;

    protected string | Closure $storageTimezone = 'UTC';

    protected TimezoneMode | Closure $timezoneMode = TimezoneMode::LocationDefault;

    // Date constraints
    protected CarbonInterface | string | Closure | null $minDate = null;

    protected CarbonInterface | string | Closure | null $maxDate = null;

    /** @var array<int>|Closure|null */
    protected array | Closure | null $disabledWeekdays = null;

    /** @var array<string>|Closure|null */
    protected array | Closure | null $excludedDates = null;

    protected DateRestriction | Closure $dateRestriction = DateRestriction::None;

    // Time constraints
    protected string | Closure | null $businessHoursStart = null;

    protected string | Closure | null $businessHoursEnd = null;

    protected bool | Closure $useTimeSlots = false;

    protected int | Closure | null $timeSlotDurationMinutes = null;

    // Range constraints
    protected int | Closure | null $minRangeMinutes = null;

    protected int | Closure | null $maxRangeMinutes = null;

    // Dual-state range support
    protected string | Closure | null $startStatePath = null;

    protected string | Closure | null $endStatePath = null;

    // Flatpickr-specific options
    protected bool | Closure $altInput = true;

    protected string | Closure | null $altInputClass = null;

    protected bool | Closure $allowInput = false;

    protected bool | Closure $clickOpens = true;

    protected bool | Closure $disableMobile = true;

    protected bool | Closure $inline = false;

    protected bool | Closure $closeOnSelect = true;

    protected bool | Closure $time24hr = true;

    protected int | Closure $hourIncrement = 1;

    protected int | Closure $minuteIncrement = 5;

    protected int | Closure $showMonths = 1;

    protected bool | Closure $weekNumbers = false;

    protected FlatpickrPosition | Closure $position = FlatpickrPosition::Auto;

    protected FlatpickrMonthSelectorType | Closure $monthSelectorType = FlatpickrMonthSelectorType::Dropdown;

    protected FlatpickrTheme | Closure $theme = FlatpickrTheme::Default;

    protected string | Closure | null $locale = null;

    protected int | Closure $defaultHour = 12;

    protected int | Closure $defaultMinute = 0;

    // Safe event hooks
    /** @var array<string, array{event: string, payload: array<string>}>|null */
    protected ?array $eventHooks = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefixIcon('heroicon-o-calendar');

        $this->afterStateHydrated(function (Flatpickr $component, mixed $state): void {
            $component->performStateHydration($state);
        });

        $this->dehydrateStateUsing(function (Flatpickr $component, mixed $state): mixed {
            return $component->performStateDehydration($state);
        });

        $this->registerValidationRules();
    }

    // =========================================================================
    // Core Configuration Methods
    // =========================================================================

    public function mode(FlatpickrMode | string | Closure $mode): static
    {
        $this->mode = is_string($mode) ? FlatpickrMode::from($mode) : $mode;

        return $this;
    }

    public function getMode(): FlatpickrMode
    {
        return $this->evaluate($this->mode);
    }

    public function time(bool | Closure $condition = true): static
    {
        $this->hasTime = $condition;

        return $this;
    }

    public function hasTime(): bool
    {
        return (bool) $this->evaluate($this->hasTime);
    }

    public function date(bool | Closure $condition = true): static
    {
        $this->hasDate = $condition;

        return $this;
    }

    public function hasDate(): bool
    {
        return (bool) $this->evaluate($this->hasDate);
    }

    public function seconds(bool | Closure $condition = true): static
    {
        $this->hasSeconds = $condition;

        return $this;
    }

    public function hasSeconds(): bool
    {
        return (bool) $this->evaluate($this->hasSeconds);
    }

    // =========================================================================
    // Preset Methods (Convenience)
    // =========================================================================

    /**
     * Configure as a date-only picker.
     */
    public function dateOnly(): static
    {
        return $this->date(true)->time(false);
    }

    /**
     * Configure as a time-only picker.
     */
    public function timeOnly(): static
    {
        return $this->date(false)->time(true);
    }

    /**
     * Configure as a date and time picker.
     */
    public function dateTime(): static
    {
        return $this->date(true)->time(true);
    }

    /**
     * Configure as a range picker.
     */
    public function range(): static
    {
        return $this->mode(FlatpickrMode::Range);
    }

    /**
     * Configure as a multiple date picker.
     */
    public function multiple(): static
    {
        return $this->mode(FlatpickrMode::Multiple);
    }

    // =========================================================================
    // Format Configuration
    // =========================================================================

    public function format(string | Closure | null $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string
    {
        $format = $this->evaluate($this->format);

        if (is_string($format)) {
            return $this->sanitizeFormatString($format) ?? $this->getDefaultFormat();
        }

        return $this->getDefaultFormat();
    }

    protected function getDefaultFormat(): string
    {
        if ($this->hasTime() && $this->hasDate()) {
            return $this->hasSeconds() ? 'Y-m-d H:i:s' : 'Y-m-d H:i';
        }

        if ($this->hasTime()) {
            return $this->hasSeconds() ? 'H:i:s' : 'H:i';
        }

        return 'Y-m-d';
    }

    public function displayFormat(string | Closure | null $format): static
    {
        $this->displayFormat = $format;

        return $this;
    }

    public function getDisplayFormat(): ?string
    {
        $format = $this->evaluate($this->displayFormat);

        if (is_string($format)) {
            return $this->sanitizeFormatString($format);
        }

        // FSRevs fix: Return a sensible default altFormat based on the field type
        // and time24hr setting so AM/PM is properly displayed when time24hr is false.
        return $this->getDefaultDisplayFormat();
    }

    /**
     * Get the default display format based on field configuration.
     *
     * FSRevs addition: Provides sensible defaults that respect time24hr setting.
     */
    protected function getDefaultDisplayFormat(): string
    {
        $hasTime = $this->hasTime();
        $hasDate = $this->hasDate();
        $time24hr = $this->getTime24hr();

        // Time only
        if (!$hasDate && $hasTime) {
            return $time24hr ? "H:i" : "h:i K";
        }

        // DateTime
        if ($hasTime) {
            return $time24hr ? "M j, Y H:i" : "M j, Y h:i K";
        }

        // Date only
        return "M j, Y";
    }

    public function useAltInput(bool | Closure $condition = true): static
    {
        $this->altInput = $condition;

        return $this;
    }

    // =========================================================================
    // Timezone Configuration
    // =========================================================================

    public function displayTimezone(string | Closure | null $timezone): static
    {
        $this->displayTimezone = $timezone;

        return $this;
    }

    public function getDisplayTimezone(): ?string
    {
        $tz = $this->evaluate($this->displayTimezone);

        return is_string($tz) ? $tz : null;
    }

    public function storageTimezone(string | Closure $timezone): static
    {
        $this->storageTimezone = $timezone;

        return $this;
    }

    public function getStorageTimezone(): string
    {
        $tz = $this->evaluate($this->storageTimezone);

        return is_string($tz) ? $tz : 'UTC';
    }

    public function timezoneMode(TimezoneMode | string | Closure $mode): static
    {
        $this->timezoneMode = is_string($mode) ? TimezoneMode::from($mode) : $mode;

        return $this;
    }

    public function getTimezoneMode(): TimezoneMode
    {
        return $this->evaluate($this->timezoneMode);
    }

    // =========================================================================
    // Date Restrictions
    // =========================================================================

    public function minDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->minDate = $date;

        return $this;
    }

    public function getMinDate(): ?string
    {
        $date = $this->evaluate($this->minDate);

        if ($date === null) {
            // Handle dynamic date restrictions
            $restriction = $this->getDateRestriction();
            if ($restriction === DateRestriction::NoPast) {
                return Carbon::today()->format('Y-m-d');
            }

            return null;
        }

        if ($date instanceof CarbonInterface) {
            return $date->format('Y-m-d');
        }

        return is_string($date) ? $date : null;
    }

    public function maxDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->maxDate = $date;

        return $this;
    }

    public function getMaxDate(): ?string
    {
        $date = $this->evaluate($this->maxDate);

        if ($date === null) {
            // Handle dynamic date restrictions
            $restriction = $this->getDateRestriction();
            if ($restriction === DateRestriction::NoFuture) {
                return Carbon::today()->format('Y-m-d');
            }

            return null;
        }

        if ($date instanceof CarbonInterface) {
            return $date->format('Y-m-d');
        }

        return is_string($date) ? $date : null;
    }

    public function dateRestriction(DateRestriction | string | Closure $restriction): static
    {
        $this->dateRestriction = is_string($restriction)
            ? DateRestriction::from($restriction)
            : $restriction;

        return $this;
    }

    public function getDateRestriction(): DateRestriction
    {
        return $this->evaluate($this->dateRestriction);
    }

    /**
     * @param  array<int>|Closure  $days  Array of weekday integers (0=Sunday, 6=Saturday)
     */
    public function disabledWeekdays(array | Closure $days): static
    {
        $this->disabledWeekdays = $days;

        return $this;
    }

    /**
     * @return array<int>
     */
    public function getDisabledWeekdays(): array
    {
        $days = $this->evaluate($this->disabledWeekdays);

        return $this->sanitizeWeekdayArray($days);
    }

    /**
     * @param  array<string>|Closure  $dates  Array of date strings in Y-m-d format
     */
    public function excludedDates(array | Closure $dates): static
    {
        $this->excludedDates = $dates;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getExcludedDates(): array
    {
        $dates = $this->evaluate($this->excludedDates);

        return $this->sanitizeDateArray($dates);
    }

    // =========================================================================
    // Time Configuration
    // =========================================================================

    public function time24hr(bool | Closure $condition = true): static
    {
        $this->time24hr = $condition;

        return $this;
    }

    public function getTime24hr(): bool
    {
        return (bool) $this->evaluate($this->time24hr);
    }

    public function hourIncrement(int | Closure $increment): static
    {
        $this->hourIncrement = $increment;

        return $this;
    }

    public function getHourIncrement(): int
    {
        return (int) $this->evaluate($this->hourIncrement);
    }

    public function minuteIncrement(int | Closure $increment): static
    {
        $this->minuteIncrement = $increment;

        return $this;
    }

    public function getMinuteIncrement(): int
    {
        return (int) $this->evaluate($this->minuteIncrement);
    }

    /**
     * Filament-style synonym for hourIncrement.
     */
    public function hoursStep(int | Closure $step): static
    {
        return $this->hourIncrement($step);
    }

    /**
     * Filament-style synonym for minuteIncrement.
     */
    public function minutesStep(int | Closure $step): static
    {
        return $this->minuteIncrement($step);
    }

    // =========================================================================
    // Business Hours & Time Slots
    // =========================================================================

    public function businessHours(string | Closure $start, string | Closure $end): static
    {
        $this->businessHoursStart = $start;
        $this->businessHoursEnd = $end;

        return $this;
    }

    public function getBusinessHoursStart(): ?string
    {
        $start = $this->evaluate($this->businessHoursStart);

        return $this->sanitizeTimeString($start);
    }

    public function getBusinessHoursEnd(): ?string
    {
        $end = $this->evaluate($this->businessHoursEnd);

        return $this->sanitizeTimeString($end);
    }

    public function useTimeSlots(bool | Closure $condition = true): static
    {
        $this->useTimeSlots = $condition;

        return $this;
    }

    public function getUseTimeSlots(): bool
    {
        return (bool) $this->evaluate($this->useTimeSlots);
    }

    public function timeSlotDurationMinutes(int | Closure $minutes): static
    {
        $this->timeSlotDurationMinutes = $minutes;

        // Also set minuteIncrement to match
        $this->minuteIncrement = $minutes;

        return $this;
    }

    public function getTimeSlotDurationMinutes(): ?int
    {
        $minutes = $this->evaluate($this->timeSlotDurationMinutes);

        return $this->sanitizePositiveInt($minutes, 1, 60);
    }

    // =========================================================================
    // Range Configuration
    // =========================================================================

    public function minRangeMinutes(int | Closure $minutes): static
    {
        $this->minRangeMinutes = $minutes;

        return $this;
    }

    public function getMinRangeMinutes(): ?int
    {
        $minutes = $this->evaluate($this->minRangeMinutes);

        return $this->sanitizePositiveInt($minutes, 1);
    }

    public function maxRangeMinutes(int | Closure $minutes): static
    {
        $this->maxRangeMinutes = $minutes;

        return $this;
    }

    public function getMaxRangeMinutes(): ?int
    {
        $minutes = $this->evaluate($this->maxRangeMinutes);

        return $this->sanitizePositiveInt($minutes, 1);
    }

    /**
     * Configure dual-state-path range mode.
     * This allows a single Flatpickr input to control two separate state paths.
     */
    public function dualStatePaths(string | Closure $startPath, string | Closure $endPath): static
    {
        $this->startStatePath = $startPath;
        $this->endStatePath = $endPath;
        $this->mode = FlatpickrMode::Range;

        return $this;
    }

    public function getStartStatePath(): ?string
    {
        return $this->evaluate($this->startStatePath);
    }

    public function getEndStatePath(): ?string
    {
        return $this->evaluate($this->endStatePath);
    }

    public function isDualStatePathMode(): bool
    {
        return $this->getStartStatePath() !== null && $this->getEndStatePath() !== null;
    }

    // =========================================================================
    // Flatpickr Options
    // =========================================================================

    public function allowInput(bool | Closure $condition = true): static
    {
        $this->allowInput = $condition;

        return $this;
    }

    public function getAllowInput(): bool
    {
        return (bool) $this->evaluate($this->allowInput);
    }

    public function disableMobile(bool | Closure $condition = true): static
    {
        $this->disableMobile = $condition;

        return $this;
    }

    public function getDisableMobile(): bool
    {
        return (bool) $this->evaluate($this->disableMobile);
    }

    public function inline(bool | Closure $condition = true): static
    {
        $this->inline = $condition;

        return $this;
    }

    public function getInline(): bool
    {
        return (bool) $this->evaluate($this->inline);
    }

    public function closeOnSelect(bool | Closure $condition = true): static
    {
        $this->closeOnSelect = $condition;

        return $this;
    }

    public function getCloseOnSelect(): bool
    {
        return (bool) $this->evaluate($this->closeOnSelect);
    }

    public function clickOpens(bool | Closure $condition = true): static
    {
        $this->clickOpens = $condition;

        return $this;
    }

    public function getClickOpens(): bool
    {
        return (bool) $this->evaluate($this->clickOpens);
    }

    public function showMonths(int | Closure $months): static
    {
        $this->showMonths = $months;

        return $this;
    }

    public function getShowMonths(): int
    {
        return (int) $this->evaluate($this->showMonths);
    }

    public function weekNumbers(bool | Closure $condition = true): static
    {
        $this->weekNumbers = $condition;

        return $this;
    }

    public function getWeekNumbers(): bool
    {
        return (bool) $this->evaluate($this->weekNumbers);
    }

    public function position(FlatpickrPosition | string | Closure $position): static
    {
        $this->position = is_string($position) ? FlatpickrPosition::from($position) : $position;

        return $this;
    }

    public function getPosition(): FlatpickrPosition
    {
        return $this->evaluate($this->position);
    }

    public function theme(FlatpickrTheme | string | Closure $theme): static
    {
        $this->theme = is_string($theme) ? FlatpickrTheme::from($theme) : $theme;

        return $this;
    }

    public function getTheme(): FlatpickrTheme
    {
        return $this->evaluate($this->theme);
    }

    public function locale(string | Closure | null $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->evaluate($this->locale);
    }

    public function defaultHour(int | Closure $hour): static
    {
        $this->defaultHour = $hour;

        return $this;
    }

    public function getDefaultHour(): int
    {
        return (int) $this->evaluate($this->defaultHour);
    }

    public function defaultMinute(int | Closure $minute): static
    {
        $this->defaultMinute = $minute;

        return $this;
    }

    public function getDefaultMinute(): int
    {
        return (int) $this->evaluate($this->defaultMinute);
    }

    // =========================================================================
    // Safe Event Hooks
    // =========================================================================

    /**
     * Emit a Livewire event when the date changes.
     *
     * @param  array<string>  $payloadKeys  Keys to include in payload (e.g., ['date', 'dateStr'])
     */
    public function emitOnChange(string $eventName, array $payloadKeys = ['dateStr']): static
    {
        $this->eventHooks['onChange'] = [
            'event' => $this->sanitizeTextString($eventName) ?? '',
            'payload' => $payloadKeys,
        ];

        return $this;
    }

    /**
     * Emit a Livewire event when the picker opens.
     */
    public function emitOnOpen(string $eventName): static
    {
        $this->eventHooks['onOpen'] = [
            'event' => $this->sanitizeTextString($eventName) ?? '',
            'payload' => [],
        ];

        return $this;
    }

    /**
     * Emit a Livewire event when the picker closes.
     */
    public function emitOnClose(string $eventName): static
    {
        $this->eventHooks['onClose'] = [
            'event' => $this->sanitizeTextString($eventName) ?? '',
            'payload' => [],
        ];

        return $this;
    }

    /**
     * @return array<string, array{event: string, payload: array<string>}>|null
     */
    public function getEventHooks(): ?array
    {
        return $this->eventHooks;
    }

    // =========================================================================
    // State Hydration & Dehydration
    // =========================================================================

    protected function performStateHydration(mixed $state): void
    {
        if (blank($state)) {
            return;
        }

        $mode = $this->getMode();
        $format = $this->getFormat();

        if ($mode === FlatpickrMode::Range || $mode === FlatpickrMode::Multiple) {
            $state = $this->hydrateMultipleState($state, $format);
        } else {
            $state = $this->hydrateSingleState($state, $format);
        }

        $this->state($state);
    }

    protected function hydrateSingleState(mixed $state, string $format): ?string
    {
        $carbon = $this->parseToCarbon($state);

        if ($carbon === null) {
            return null;
        }

        // Convert from storage timezone to display timezone if needed
        $displayTimezone = $this->getDisplayTimezone();
        if ($displayTimezone && $this->hasTime()) {
            $carbon = $carbon->setTimezone($displayTimezone);
        }

        return $carbon->format($format);
    }

    protected function hydrateMultipleState(mixed $state, string $format): ?string
    {
        $conjunction = $this->getMode() === FlatpickrMode::Range ? ' to ' : ', ';

        if (is_string($state)) {
            $parts = explode($conjunction, $state);
        } elseif (is_array($state)) {
            $parts = $state;
        } else {
            return null;
        }

        $formatted = collect($parts)
            ->map(fn ($date) => $this->parseToCarbon($date))
            ->filter()
            ->map(function (CarbonInterface $carbon) use ($format) {
                $displayTimezone = $this->getDisplayTimezone();
                if ($displayTimezone && $this->hasTime()) {
                    $carbon = $carbon->setTimezone($displayTimezone);
                }

                return $carbon->format($format);
            });

        return $formatted->implode($conjunction);
    }

    protected function performStateDehydration(mixed $state): mixed
    {
        if (blank($state)) {
            return null;
        }

        $mode = $this->getMode();

        if ($mode === FlatpickrMode::Range) {
            return $this->dehydrateRangeState($state);
        }

        if ($mode === FlatpickrMode::Multiple) {
            return $this->dehydrateMultipleState($state);
        }

        return $this->dehydrateSingleState($state);
    }

    protected function dehydrateSingleState(mixed $state): ?string
    {
        $carbon = $this->parseToCarbon($state);

        if ($carbon === null) {
            return null;
        }

        // Convert from display timezone to storage timezone
        $displayTimezone = $this->getDisplayTimezone();
        $storageTimezone = $this->getStorageTimezone();

        if ($displayTimezone && $this->hasTime()) {
            $carbon = $carbon->setTimezone($displayTimezone)->setTimezone($storageTimezone);
        }

        return $carbon->format($this->getFormat());
    }

    /**
     * @return array{start: string|null, end: string|null}|string
     */
    protected function dehydrateRangeState(mixed $state): array | string
    {
        $parts = is_string($state) ? explode(' to ', $state) : (array) $state;

        $start = $this->dehydrateSingleState($parts[0] ?? null);
        $end = $this->dehydrateSingleState($parts[1] ?? null);

        // If using dual-state-path mode, we handle this differently in the view
        if ($this->isDualStatePathMode()) {
            return ['start' => $start, 'end' => $end];
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * @return array<string>
     */
    protected function dehydrateMultipleState(mixed $state): array
    {
        $parts = is_string($state) ? explode(', ', $state) : (array) $state;

        return collect($parts)
            ->map(fn ($date) => $this->dehydrateSingleState($date))
            ->filter()
            ->values()
            ->all();
    }

    protected function parseToCarbon(mixed $state): ?CarbonInterface
    {
        if ($state === null || $state === '') {
            return null;
        }

        if ($state instanceof CarbonInterface) {
            return $state;
        }

        try {
            return Carbon::parse($state);
        } catch (InvalidFormatException) {
            return null;
        }
    }

    // =========================================================================
    // Validation Rules
    // =========================================================================

    protected function registerValidationRules(): void
    {
        // Date validation
        $this->rule(function () {
            if ($this->getMode() === FlatpickrMode::Single && $this->hasDate()) {
                return 'date';
            }

            return null;
        });

        // Min date validation
        $this->rule(function () {
            $minDate = $this->getMinDate();
            if ($minDate) {
                return "after_or_equal:{$minDate}";
            }

            return null;
        });

        // Max date validation
        $this->rule(function () {
            $maxDate = $this->getMaxDate();
            if ($maxDate) {
                return "before_or_equal:{$maxDate}";
            }

            return null;
        });

        // Custom validation for complex constraints
        $this->rules([
            fn () => $this->createConstraintValidationRule(),
        ]);
    }

    protected function createConstraintValidationRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (empty($value)) {
                return;
            }

            // Validate disabled weekdays
            $disabledWeekdays = $this->getDisabledWeekdays();
            if ($disabledWeekdays !== [] && $this->hasDate()) {
                $this->validateDisabledWeekdays($value, $disabledWeekdays, $fail);
            }

            // Validate excluded dates
            $excludedDates = $this->getExcludedDates();
            if ($excludedDates !== [] && $this->hasDate()) {
                $this->validateExcludedDates($value, $excludedDates, $fail);
            }

            // Validate business hours
            $businessStart = $this->getBusinessHoursStart();
            $businessEnd = $this->getBusinessHoursEnd();
            if ($businessStart !== null && $businessEnd !== null && $this->hasTime()) {
                $this->validateBusinessHours($value, $businessStart, $businessEnd, $fail);
            }

            // Validate time slot alignment
            $slotDuration = $this->getTimeSlotDurationMinutes();
            if ($slotDuration !== null && $this->hasTime()) {
                $this->validateTimeSlotAlignment($value, $slotDuration, $fail);
            }

            // Validate range constraints
            if ($this->getMode() === FlatpickrMode::Range) {
                $this->validateRangeConstraints($value, $fail);
            }
        };
    }

    /**
     * @param  array<int>  $disabledWeekdays
     */
    protected function validateDisabledWeekdays(mixed $value, array $disabledWeekdays, Closure $fail): void
    {
        $carbon = $this->parseToCarbon($value);
        if ($carbon && in_array($carbon->dayOfWeek, $disabledWeekdays, true)) {
            $fail('The selected date falls on a disabled day of the week.');
        }
    }

    /**
     * @param  array<string>  $excludedDates
     */
    protected function validateExcludedDates(mixed $value, array $excludedDates, Closure $fail): void
    {
        $carbon = $this->parseToCarbon($value);
        if ($carbon && in_array($carbon->format('Y-m-d'), $excludedDates, true)) {
            $fail('The selected date is not available.');
        }
    }

    protected function validateBusinessHours(mixed $value, string $start, string $end, Closure $fail): void
    {
        $carbon = $this->parseToCarbon($value);
        if ($carbon === null) {
            return;
        }

        $timeStr = $carbon->format('H:i');

        // Handle midnight wrap (end < start means next day)
        if ($end < $start) {
            if ($timeStr < $start && $timeStr >= $end) {
                $fail("The selected time is outside business hours ({$start} - {$end}).");
            }
        } else {
            if ($timeStr < $start || $timeStr > $end) {
                $fail("The selected time is outside business hours ({$start} - {$end}).");
            }
        }
    }

    protected function validateTimeSlotAlignment(mixed $value, int $slotDuration, Closure $fail): void
    {
        $carbon = $this->parseToCarbon($value);
        if ($carbon === null) {
            return;
        }

        $minutes = (int) $carbon->format('i');
        if ($minutes % $slotDuration !== 0) {
            $fail("The selected time must align with {$slotDuration}-minute slots.");
        }
    }

    protected function validateRangeConstraints(mixed $value, Closure $fail): void
    {
        $parts = is_string($value) ? explode(' to ', $value) : (array) $value;

        if (count($parts) !== 2) {
            return;
        }

        $start = $this->parseToCarbon($parts[0]);
        $end = $this->parseToCarbon($parts[1]);

        if ($start === null || $end === null) {
            return;
        }

        // Ensure end is after start
        if ($end->lessThan($start)) {
            $fail('The end date/time must be after the start date/time.');

            return;
        }

        $durationMinutes = $start->diffInMinutes($end);

        $minMinutes = $this->getMinRangeMinutes();
        if ($minMinutes !== null && $durationMinutes < $minMinutes) {
            $fail("The selected range must be at least {$minMinutes} minutes.");
        }

        $maxMinutes = $this->getMaxRangeMinutes();
        if ($maxMinutes !== null && $durationMinutes > $maxMinutes) {
            $fail("The selected range cannot exceed {$maxMinutes} minutes.");
        }
    }

    // =========================================================================
    // View Configuration
    // =========================================================================

    /**
     * Get all Flatpickr configuration options for the Alpine component.
     *
     * @return array<string, mixed>
     */
    public function getFlatpickrConfig(): array
    {
        $config = [
            'mode' => $this->getMode()->value,
            'dateFormat' => $this->getFormat(),
            'enableTime' => $this->hasTime(),
            'noCalendar' => ! $this->hasDate(),
            'enableSeconds' => $this->hasSeconds(),
            'time_24hr' => $this->getTime24hr(),
            'hourIncrement' => $this->getHourIncrement(),
            'minuteIncrement' => $this->getMinuteIncrement(),
            'allowInput' => $this->getAllowInput(),
            'disableMobile' => $this->getDisableMobile(),
            'inline' => $this->getInline(),
            'clickOpens' => $this->getClickOpens(),
            'closeOnSelect' => $this->getCloseOnSelect(),
            'showMonths' => $this->getShowMonths(),
            'weekNumbers' => $this->getWeekNumbers(),
            'position' => $this->getPosition()->value,
            'defaultHour' => $this->getDefaultHour(),
            'defaultMinute' => $this->getDefaultMinute(),
            'altInput' => (bool) $this->evaluate($this->altInput),
        ];

        // Optional configs
        $altFormat = $this->getDisplayFormat();
        if ($altFormat) {
            $config['altFormat'] = $altFormat;
        }

        $altInputClass = $this->evaluate($this->altInputClass);
        if (is_string($altInputClass)) {
            $config['altInputClass'] = $this->sanitizeCssClasses($altInputClass);
        }

        $minDate = $this->getMinDate();
        if ($minDate) {
            $config['minDate'] = $minDate;
        }

        $maxDate = $this->getMaxDate();
        if ($maxDate) {
            $config['maxDate'] = $maxDate;
        }

        $locale = $this->getLocale();
        if ($locale) {
            $config['locale'] = $locale;
        }

        // Constraint configs for client-side enforcement
        $disabledWeekdays = $this->getDisabledWeekdays();
        if ($disabledWeekdays !== []) {
            $config['disabledWeekdays'] = $disabledWeekdays;
        }

        $excludedDates = $this->getExcludedDates();
        if ($excludedDates !== []) {
            $config['disable'] = $excludedDates;
        }

        // Business hours for client-side enforcement
        $businessStart = $this->getBusinessHoursStart();
        $businessEnd = $this->getBusinessHoursEnd();
        if ($businessStart && $businessEnd) {
            $config['businessHours'] = [
                'start' => $businessStart,
                'end' => $businessEnd,
            ];
        }

        // Range constraints
        $minRange = $this->getMinRangeMinutes();
        if ($minRange) {
            $config['minRangeMinutes'] = $minRange;
        }

        $maxRange = $this->getMaxRangeMinutes();
        if ($maxRange) {
            $config['maxRangeMinutes'] = $maxRange;
        }

        // Timezone info for JS
        $displayTimezone = $this->getDisplayTimezone();
        if ($displayTimezone) {
            $config['displayTimezone'] = $displayTimezone;
        }

        $config['storageTimezone'] = $this->getStorageTimezone();
        $config['timezoneMode'] = $this->getTimezoneMode()->value;

        // Event hooks
        $eventHooks = $this->getEventHooks();
        if ($eventHooks) {
            $config['eventHooks'] = $eventHooks;
        }

        // Dual-state path config
        if ($this->isDualStatePathMode()) {
            // FSRevs fix: Prefix dualStatePaths with the container's state path
            // so Livewire can find the properties in nested form data structures.
            $containerPath = $this->getContainer()?->getStatePath();
            $prefix = $containerPath ? $containerPath . '.' : '';

            $config['dualStatePaths'] = [
                'start' => $prefix . $this->getStartStatePath(),
                'end' => $prefix . $this->getEndStatePath(),
            ];
        }

        return $config;
    }

    public function getExtraInputAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAttributes());
    }
}
