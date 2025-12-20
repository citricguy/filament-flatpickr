# Integration for Filament with Air Datepicker as a field.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/citricguy/filament-air-datepicker.svg?style=flat-square)](https://packagist.org/packages/citricguy/filament-air-datepicker)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/citricguy/filament-air-datepicker/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/citricguy/filament-air-datepicker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/citricguy/filament-air-datepicker/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/citricguy/filament-air-datepicker/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/citricguy/filament-air-datepicker.svg?style=flat-square)](https://packagist.org/packages/citricguy/filament-air-datepicker)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require citricguy/filament-air-datepicker
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-air-datepicker-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-air-datepicker-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-air-datepicker-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentAirDatepicker = new Citricguy\FilamentAirDatepicker();
echo $filamentAirDatepicker->echoPhrase('Hello, Citricguy!');
```

## Testing

Run all tests:

```bash
composer test
```

Run code style checks:

```bash
composer lint
```

Fix code style issues:

```bash
composer format
```

Run static analysis:

```bash
composer analyse
```

Run refactoring checks:

```bash
composer refactor
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Josh Sommers](https://github.com/citricguy)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
