<?php

declare(strict_types=1);

namespace Citricguy\FilamentFlatpickr\Concerns;

/**
 * Trait for sanitizing configuration values to prevent XSS attacks.
 *
 * All config values that may originate from user-editable sources (e.g., database)
 * must be sanitized before being serialized to JavaScript.
 */
trait SanitizesConfiguration
{
    /**
     * Allowlist of valid characters for date/time format strings.
     * Based on PHP date format tokens and Flatpickr format tokens.
     *
     * @var array<int, string>
     */
    protected static array $allowedFormatCharacters = [
        // Flatpickr tokens
        'd', 'D', 'l', 'j', 'J', 'w', 'W', 'F', 'm', 'n', 'M', 'U', 'y', 'Y', 'Z',
        'H', 'h', 'G', 'i', 's', 'S', 'K', 'a', 'A',
        // Separators and literals
        '-', '/', '.', ':', ' ', ',', 'T', '\\',
        // Ordinal suffix (PHP)
        'o',
    ];

    /**
     * Sanitize a date/time format string.
     *
     * @param  string|null  $format  The format string to sanitize
     * @return string|null The sanitized format string
     */
    protected function sanitizeFormatString(?string $format): ?string
    {
        if ($format === null || $format === '') {
            return null;
        }

        // Remove any HTML tags
        $format = strip_tags($format);

        // Validate each character is in our allowlist
        $sanitized = '';
        $length = mb_strlen($format);
        $escapeNext = false;

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($format, $i, 1);

            if ($escapeNext) {
                $sanitized .= $char;
                $escapeNext = false;

                continue;
            }

            if ($char === '\\') {
                $sanitized .= $char;
                $escapeNext = true;

                continue;
            }

            if (in_array($char, self::$allowedFormatCharacters, true)) {
                $sanitized .= $char;
            }
        }

        return $sanitized ?: null;
    }

    /**
     * Sanitize a CSS class string.
     * Only allows alphanumeric characters, underscores, hyphens, colons, and spaces.
     *
     * @param  string|null  $classString  The class string to sanitize
     * @return string|null The sanitized class string
     */
    protected function sanitizeCssClasses(?string $classString): ?string
    {
        if ($classString === null || $classString === '') {
            return null;
        }

        // Only allow safe characters for CSS class names
        $sanitized = preg_replace('/[^A-Za-z0-9_:\-\s]/', '', $classString);

        if (! is_string($sanitized) || $sanitized === '') {
            return null;
        }

        return $sanitized;
    }

    /**
     * Sanitize a text string for safe output.
     * Removes potential XSS vectors.
     *
     * @param  string|null  $text  The text to sanitize
     * @return string|null The sanitized text
     */
    protected function sanitizeTextString(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return null;
        }

        // Strip HTML tags
        $text = strip_tags($text);

        // Remove javascript: and data: URI schemes
        $text = (string) preg_replace('/javascript\s*:/i', '', $text);
        $text = (string) preg_replace('/data\s*:/i', '', $text);

        // Remove on* event handlers
        $text = (string) preg_replace('/on\w+\s*=/i', '', $text);

        // Escape HTML entities
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sanitize an array of date strings.
     * Validates each date matches expected format.
     *
     * @param  array<int, mixed>|null  $dates  Array of date strings
     * @param  string  $format  Expected date format (default: Y-m-d)
     * @return array<int, string> Sanitized array of valid dates
     */
    protected function sanitizeDateArray(?array $dates, string $format = 'Y-m-d'): array
    {
        if ($dates === null || $dates === []) {
            return [];
        }

        $validDates = [];

        foreach ($dates as $date) {
            if (! is_string($date)) {
                continue;
            }

            // Validate date format
            $parsed = \DateTimeImmutable::createFromFormat($format, $date);

            if ($parsed !== false && $parsed->format($format) === $date) {
                $validDates[] = $date;
            }
        }

        return $validDates;
    }

    /**
     * Sanitize an array of weekday integers.
     * Ensures values are between 0-6 (Sunday=0 to Saturday=6).
     *
     * @param  array<int, int|string>|null  $weekdays  Array of weekday values
     * @return array<int, int> Sanitized array of valid weekday integers
     */
    protected function sanitizeWeekdayArray(?array $weekdays): array
    {
        if ($weekdays === null || $weekdays === []) {
            return [];
        }

        $validWeekdays = [];

        foreach ($weekdays as $day) {
            $dayInt = filter_var($day, FILTER_VALIDATE_INT);

            if ($dayInt !== false && $dayInt >= 0 && $dayInt <= 6) {
                $validWeekdays[] = $dayInt;
            }
        }

        return array_unique($validWeekdays);
    }

    /**
     * Sanitize a time string in HH:MM format.
     *
     * @param  string|null  $time  The time string to sanitize
     * @return string|null The sanitized time string or null if invalid
     */
    protected function sanitizeTimeString(?string $time): ?string
    {
        if ($time === null || $time === '') {
            return null;
        }

        // Validate HH:MM format
        if (preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/', $time, $matches)) {
            return sprintf('%02d:%02d', (int) $matches[1], (int) $matches[2]);
        }

        return null;
    }

    /**
     * Validate and sanitize a positive integer.
     *
     * @param  int|string|null  $value  The value to validate
     * @param  int  $min  Minimum allowed value
     * @param  int|null  $max  Maximum allowed value (null for no limit)
     * @return int|null The sanitized integer or null if invalid
     */
    protected function sanitizePositiveInt(int | string | null $value, int $min = 1, ?int $max = null): ?int
    {
        if ($value === null) {
            return null;
        }

        $intValue = filter_var($value, FILTER_VALIDATE_INT);

        if ($intValue === false || $intValue < $min) {
            return null;
        }

        if ($max !== null && $intValue > $max) {
            return null;
        }

        return $intValue;
    }
}
