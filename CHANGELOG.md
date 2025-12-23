# Changelog

All notable changes to `filament-flatpickr` will be documented in this file.

## 1.0.0 - 2025-01-XX

Initial release of Filament Flatpickr for Filament v4.

### Features

- **Date/Time Picker** - Full Flatpickr integration as a Filament form field
- **Selection Modes** - Single date, date range, and multiple date selection
- **Time Support** - Time-only, date-only, or combined datetime with optional seconds
- **Timezone Handling** - Display and storage timezone configuration with browser detection
- **Date Constraints** - Min/max dates, disable weekdays, exclude specific dates
- **Business Hours** - Restrict time selection to specified hours with validation
- **Time Slots** - Enforce fixed time slot intervals
- **Range Constraints** - Minimum and maximum duration for date ranges
- **Dual State Paths** - Store range start/end in separate database columns
- **9 Built-in Themes** - Default, Airbnb, Confetti, Dark, Light, and Material variants
- **Positioning** - 12 position options for calendar popup placement
- **Inline Mode** - Display calendar inline without popup
- **Multiple Months** - Show multiple months in the calendar view
- **Week Numbers** - Display ISO week numbers
- **Manual Input** - Allow keyboard entry with validation
- **Mobile Support** - Option to use or disable native mobile pickers
- **Localization** - Support for 40+ Flatpickr locales
- **Livewire Events** - Emit events on change, open, and close
- **Prefix/Suffix Icons** - Standard Filament affix support
- **XSS Protection** - Comprehensive input sanitization

### Components

- `Flatpickr` - Full-featured base component
- `FlatpickrDatePicker` - Pre-configured date-only picker
- `FlatpickrTimePicker` - Pre-configured time-only picker
- `FlatpickrDateTimePicker` - Pre-configured datetime picker

### Enums

- `FlatpickrMode` - Single, Range, Multiple
- `FlatpickrTheme` - Default, Airbnb, Confetti, Dark, Light, MaterialBlue, MaterialGreen, MaterialOrange, MaterialRed
- `FlatpickrPosition` - 12 calendar positioning options
- `DateRestriction` - None, NoPast, NoFuture
- `TimezoneMode` - LocationDefault, Fixed, Submitter
