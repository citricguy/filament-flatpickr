/**
 * Filament Flatpickr - Alpine.js Integration
 *
 * This module registers the Flatpickr Alpine component for use with Filament Forms.
 */

import flatpickrComponent from './components/flatpickr.js'

// Register the Alpine component globally
document.addEventListener('alpine:init', () => {
    window.Alpine.data('flatpickrComponent', flatpickrComponent)
})

// Export for direct use
export { flatpickrComponent }
