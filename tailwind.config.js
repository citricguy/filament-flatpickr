/**
 * Tailwind CSS v4 Configuration
 * 
 * Note: Tailwind v4 uses CSS-first configuration via @theme in CSS files.
 * This config file is kept for compatibility with tooling that expects it.
 * Primary configuration is in resources/css/index.css
 */

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.{js,php,blade.php}',
        './src/**/*.php',
    ],
}
