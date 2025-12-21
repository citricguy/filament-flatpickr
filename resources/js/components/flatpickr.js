import dayjs from 'dayjs'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import timezone from 'dayjs/plugin/timezone'
import utc from 'dayjs/plugin/utc'
import flatpickr from 'flatpickr'

// Extend dayjs with required plugins
dayjs.extend(customParseFormat)
dayjs.extend(utc)
dayjs.extend(timezone)

/**
 * Filament Flatpickr Alpine Component
 *
 * Provides a loop-proof, timezone-aware Flatpickr integration for Filament.
 *
 * Features:
 * - Bidirectional sync with Livewire state via $entangle (loop-proof)
 * - Timezone conversion (display vs storage)
 * - Range mode with min/max constraints
 * - Dual-state-path range support
 * - Business hours enforcement
 * - Disabled weekdays/dates
 * - Safe event hooks
 */
export default function flatpickrComponent({ state, config }) {
    return {
        // Core state - entangled with Livewire
        state: state,
        config: config,
        fp: null,
        isSyncing: false,
        componentId: null,

        /**
         * Initialize the Flatpickr instance
         */
        init() {
            this.componentId = this.$el.id || `flatpickr-${Math.random().toString(36).substr(2, 9)}`

            // Build Flatpickr configuration
            const fpConfig = this.buildFlatpickrConfig()

            // Initialize Flatpickr
            this.fp = flatpickr(this.$refs.input, fpConfig)

            // Set initial value if present
            if (this.state) {
                this.isSyncing = true
                this.fp.setDate(this.parseStateForDisplay(this.state), false)
                this.isSyncing = false
            }

            // Watch for Livewire state changes (via $entangle)
            this.$watch('state', (newValue, oldValue) => {
                this.onLivewireStateChange(newValue, oldValue)
            })

            // Listen for timezone change events (submitter mode)
            window.addEventListener('flatpickr:set-timezone', (event) => {
                if (event.detail?.id === this.componentId || event.detail?.id === undefined) {
                    this.handleTimezoneChange(event.detail?.timezone)
                }
            })

            // Listen for component destruction
            this.$cleanup(() => {
                if (this.fp) {
                    this.fp.destroy()
                    this.fp = null
                }
            })
        },

        /**
         * Build the Flatpickr configuration object
         */
        buildFlatpickrConfig() {
            const config = {
                // Core options from PHP config
                mode: this.config.mode || 'single',
                dateFormat: this.config.dateFormat || 'Y-m-d',
                enableTime: this.config.enableTime || false,
                noCalendar: this.config.noCalendar || false,
                enableSeconds: this.config.enableSeconds || false,
                time_24hr: this.config.time_24hr ?? true,
                hourIncrement: this.config.hourIncrement || 1,
                minuteIncrement: this.config.minuteIncrement || 5,
                allowInput: this.config.allowInput || false,
                disableMobile: this.config.disableMobile ?? true,
                inline: this.config.inline || false,
                clickOpens: this.config.clickOpens ?? true,
                closeOnSelect: this.config.closeOnSelect ?? true,
                showMonths: this.config.showMonths || 1,
                weekNumbers: this.config.weekNumbers || false,
                position: this.config.position || 'auto',
                defaultHour: this.config.defaultHour || 12,
                defaultMinute: this.config.defaultMinute || 0,
                altInput: this.config.altInput ?? true,

                // Event handlers
                onChange: (selectedDates, dateStr, instance) => {
                    this.onFlatpickrChange(selectedDates, dateStr, instance)
                },
                onOpen: () => {
                    this.emitHookEvent('onOpen')
                },
                onClose: () => {
                    this.emitHookEvent('onClose')
                },
                onReady: (selectedDates, dateStr, instance) => {
                    this.applyConstraints(instance)
                },
            }

            // Optional alt format
            if (this.config.altFormat) {
                config.altFormat = this.config.altFormat
            }

            // Alt input class
            if (this.config.altInputClass) {
                config.altInputClass = this.config.altInputClass
            }

            // Min/max dates
            if (this.config.minDate) {
                config.minDate = this.config.minDate
            }
            if (this.config.maxDate) {
                config.maxDate = this.config.maxDate
            }

            // Locale
            if (this.config.locale) {
                config.locale = this.config.locale
            }

            // Disabled dates (from excludedDates)
            if (this.config.disable && this.config.disable.length > 0) {
                config.disable = this.config.disable
            }

            // Add disabled weekdays to disable function
            if (this.config.disabledWeekdays && this.config.disabledWeekdays.length > 0) {
                const existingDisable = config.disable || []
                config.disable = [
                    ...existingDisable,
                    (date) => {
                        return this.config.disabledWeekdays.includes(date.getDay())
                    }
                ]
            }

            return config
        },

        /**
         * Apply additional constraints that can't be set via config
         */
        applyConstraints(instance) {
            // Business hours enforcement is handled via onChange validation
            // Range constraints are handled via onChange validation
        },

        /**
         * Parse stored state value for display in Flatpickr
         * Handles timezone conversion from storage to display
         */
        parseStateForDisplay(value) {
            if (!value) {
                return null
            }

            const displayTimezone = this.config.displayTimezone
            const storageTimezone = this.config.storageTimezone || 'UTC'

            // For range mode with "to" separator
            if (this.config.mode === 'range' && typeof value === 'string' && value.includes(' to ')) {
                const [start, end] = value.split(' to ')
                return [
                    this.convertFromStorageTimezone(start, storageTimezone, displayTimezone),
                    this.convertFromStorageTimezone(end, storageTimezone, displayTimezone),
                ]
            }

            // For array-based range
            if (this.config.mode === 'range' && Array.isArray(value)) {
                return value.map(v => this.convertFromStorageTimezone(v, storageTimezone, displayTimezone))
            }

            // Single date/time
            if (displayTimezone && storageTimezone && displayTimezone !== storageTimezone) {
                return this.convertFromStorageTimezone(value, storageTimezone, displayTimezone)
            }

            return value
        },

        /**
         * Convert date from storage timezone to display timezone
         */
        convertFromStorageTimezone(dateStr, fromTimezone, toTimezone) {
            if (!toTimezone || fromTimezone === toTimezone) {
                return dateStr
            }

            try {
                const parsed = dayjs.tz(dateStr, fromTimezone)
                return parsed.tz(toTimezone).format(this.config.dateFormat || 'YYYY-MM-DD HH:mm:ss')
            } catch (e) {
                console.warn('Flatpickr: Failed to convert from storage timezone', e)
                return dateStr
            }
        },

        /**
         * Handle Flatpickr onChange event
         */
        onFlatpickrChange(selectedDates, dateStr, instance) {
            if (this.isSyncing) {
                return
            }

            // Validate business hours
            if (this.config.businessHours && this.config.enableTime) {
                const isValid = this.validateBusinessHours(selectedDates)
                if (!isValid) {
                    return
                }
            }

            // Validate range constraints
            if (this.config.mode === 'range' && selectedDates.length === 2) {
                const isValid = this.validateRangeConstraints(selectedDates)
                if (!isValid) {
                    return
                }
            }

            // Convert to storage format
            const storageValue = this.formatForStorage(selectedDates, dateStr)

            // Update state via $entangle (if changed)
            if (this.state !== storageValue) {
                this.isSyncing = true

                // Handle dual-state-path mode
                if (this.config.dualStatePaths && this.config.mode === 'range') {
                    this.updateDualStatePaths(selectedDates)
                }

                // Update the entangled state (automatically syncs to Livewire)
                this.state = storageValue

                // Emit onChange hook
                this.emitHookEvent('onChange', { dateStr: storageValue, dates: selectedDates })

                this.$nextTick(() => {
                    this.isSyncing = false
                })
            }
        },

        /**
         * Handle Livewire state changes (via $entangle watcher)
         */
        onLivewireStateChange(newValue, oldValue) {
            if (this.isSyncing) {
                return
            }

            // Get current Flatpickr value in storage format
            const currentValue = this.fp.selectedDates.length > 0
                ? this.formatForStorage(this.fp.selectedDates, this.fp.input.value)
                : null

            // Only update if the value actually changed
            if (currentValue !== newValue) {
                this.isSyncing = true
                this.fp.setDate(this.parseStateForDisplay(newValue), false)
                this.$nextTick(() => {
                    this.isSyncing = false
                })
            }
        },

        /**
         * Format selected dates for storage
         */
        formatForStorage(selectedDates, dateStr) {
            if (selectedDates.length === 0) {
                return null
            }

            const displayTimezone = this.config.displayTimezone
            const storageTimezone = this.config.storageTimezone || 'UTC'

            if (this.config.mode === 'range' && selectedDates.length === 2) {
                const start = this.convertToStorageTimezone(selectedDates[0], displayTimezone, storageTimezone)
                const end = this.convertToStorageTimezone(selectedDates[1], displayTimezone, storageTimezone)
                return `${start} to ${end}`
            }

            if (this.config.mode === 'multiple') {
                return selectedDates
                    .map(date => this.convertToStorageTimezone(date, displayTimezone, storageTimezone))
                    .join(', ')
            }

            return this.convertToStorageTimezone(selectedDates[0], displayTimezone, storageTimezone)
        },

        /**
         * Convert a date to storage timezone format
         */
        convertToStorageTimezone(date, displayTimezone, storageTimezone) {
            let dayjsDate = dayjs(date)

            if (displayTimezone && this.config.enableTime) {
                // Set display timezone, then convert to storage timezone
                dayjsDate = dayjsDate.tz(displayTimezone).tz(storageTimezone)
            }

            return dayjsDate.format(this.config.dateFormat.replace('Y', 'YYYY').replace('m', 'MM').replace('d', 'DD').replace('H', 'HH').replace('i', 'mm').replace('s', 'ss'))
        },

        /**
         * Validate business hours
         */
        validateBusinessHours(selectedDates) {
            const { start, end } = this.config.businessHours

            for (const date of selectedDates) {
                const hours = date.getHours()
                const minutes = date.getMinutes()
                const timeStr = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`

                if (timeStr < start || timeStr > end) {
                    // Auto-correct to nearest valid time
                    if (timeStr < start) {
                        const [startHours, startMinutes] = start.split(':').map(Number)
                        date.setHours(startHours, startMinutes)
                    } else {
                        const [endHours, endMinutes] = end.split(':').map(Number)
                        date.setHours(endHours, endMinutes)
                    }

                    // Update the picker
                    this.fp.setDate(selectedDates, false)
                    return false
                }
            }

            return true
        },

        /**
         * Validate range constraints
         */
        validateRangeConstraints(selectedDates) {
            const start = dayjs(selectedDates[0])
            const end = dayjs(selectedDates[1])
            const durationMinutes = end.diff(start, 'minute')

            if (this.config.minRangeMinutes && durationMinutes < this.config.minRangeMinutes) {
                // Range too short - adjust end date
                const newEnd = start.add(this.config.minRangeMinutes, 'minute')
                this.fp.setDate([selectedDates[0], newEnd.toDate()], false)
                return false
            }

            if (this.config.maxRangeMinutes && durationMinutes > this.config.maxRangeMinutes) {
                // Range too long - adjust end date
                const newEnd = start.add(this.config.maxRangeMinutes, 'minute')
                this.fp.setDate([selectedDates[0], newEnd.toDate()], false)
                return false
            }

            return true
        },

        /**
         * Update dual state paths (for range mode with separate start/end paths)
         */
        updateDualStatePaths(selectedDates) {
            const { start: startPath, end: endPath } = this.config.dualStatePaths

            if (selectedDates.length >= 1) {
                const startValue = this.convertToStorageTimezone(
                    selectedDates[0],
                    this.config.displayTimezone,
                    this.config.storageTimezone || 'UTC'
                )
                this.$wire.set(startPath, startValue)
            }

            if (selectedDates.length >= 2) {
                const endValue = this.convertToStorageTimezone(
                    selectedDates[1],
                    this.config.displayTimezone,
                    this.config.storageTimezone || 'UTC'
                )
                this.$wire.set(endPath, endValue)
            }
        },

        /**
         * Handle runtime timezone changes (submitter mode)
         */
        handleTimezoneChange(newTimezone) {
            if (!newTimezone) {
                return
            }

            // Update the config
            this.config.displayTimezone = newTimezone

            // Re-render the displayed value without changing the stored value
            if (this.state && this.fp) {
                // The stored value is in storage timezone
                // We just need to update how it's displayed
                this.fp.setDate(this.state, false)
            }
        },

        /**
         * Emit safe hook events to Livewire
         */
        emitHookEvent(hookName, payload = {}) {
            const hooks = this.config.eventHooks
            if (!hooks || !hooks[hookName]) {
                return
            }

            const hook = hooks[hookName]
            const eventPayload = {}

            // Only include allowed payload keys
            for (const key of hook.payload || []) {
                if (payload[key] !== undefined) {
                    eventPayload[key] = payload[key]
                }
            }

            this.$wire.$dispatch(hook.event, eventPayload)
        },
    }
}
