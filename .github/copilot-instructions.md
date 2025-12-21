# Filament v4 Plugin Development Guidelines

This is a **Filament v4 plugin** for Laravel. Follow these conventions:

## Framework Versions
- PHP 8.3+
- Laravel 12
- Filament v4 (latest)
- Livewire v3

## Plugin Structure
- Based on [filamentphp/plugin-skeleton](https://github.com/filamentphp/plugin-skeleton/tree/4.x)
- Uses Spatie's laravel-package-tools for service provider setup
- Follows PSR-4 autoloading standards

## Coding Standards
- Follow Laravel 12 best practices
- Use strict types where applicable
- PHPStan level 8 compliance
- Laravel Pint for code formatting (PSR-12 with Laravel preset)

## Filament v4 Conventions
- Use Filament's asset registration system (`FilamentAsset::register()`)
- Register icons with `FilamentIcon::register()`
- Components should extend appropriate Filament base classes
- Use Filament's schema-based builders for forms, tables, and infolists
- Follow Filament's naming conventions for fields and components

## Testing
- Use Pest v3 for testing
- Include architecture tests
- Test Livewire components when applicable
- Orchestra Testbench for package testing

## Documentation
- Keep README updated with usage examples
- Document all public methods and classes
- Include type hints and return types
