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


## Laravel Boost (optional, dev)

We include instructions for using Laravel Boost to run a local MCP server for documentation & AI guidelines while developing the package.

Install Boost (dev):

```bash
composer require --dev laravel/boost
```

Install the local MCP server and guidelines (we provide wrappers that use Testbench so you can run these in this package repository):

```bash
composer boost:install
```

Start the local MCP server (for VS Code/other editor integration):

```bash
composer boost:mcp
```

Update Boost guidelines from the ecosystem:

```bash
composer boost:update
```

Notes:
- Boost expects an Artisan environment; in package repositories we use `orchestra/testbench` to provide an application context used by the wrapper scripts above.
- Adding Boost as a dev dependency is optional. It makes interactive docs, semantically indexed docs, and AI-guidelines available to contributors and is particularly useful for maintaining package guidance that Boost can surface to editors and agents.
- See `INSTALL_BOOST.md` for detailed steps we followed (persistent sqlite DB, cache table migration, and troubleshooting tips).


## Development status

This repository is ready for Filament v4 plugin development.

- **PHP**: ^8.3
- **Filament**: v4
- **Checks**: `composer test`, `composer analyse`, `composer lint` all pass locally

See `INSTALL_BOOST.md` for how to enable the local Laravel Boost MCP server for docs and AI-guidance.

---

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
