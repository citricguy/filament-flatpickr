@php
    use Filament\Support\Facades\FilamentAsset;

    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
    $prefixActions = $getPrefixActions();
    $suffixActions = $getSuffixActions();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $livewireKey = $getLivewireKey();

    // Build the Flatpickr configuration
    $flatpickrConfig = $getFlatpickrConfig();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline"
        :inline-suffix="$isSuffixInline"
        :prefix="$prefixLabel"
        :prefix-actions="$prefixActions"
        :prefix-icon="$prefixIcon"
        :prefix-icon-color="$getPrefixIconColor()"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :suffix-icon-color="$getSuffixIconColor()"
        :valid="! $errors->has($statePath)"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
        <div
            x-load
            x-load-src="{{ FilamentAsset::getAlpineComponentSrc('flatpickr', 'citricguy/filament-flatpickr') }}"
            x-data="flatpickrComponent({
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                config: @js($flatpickrConfig),
            })"
            x-on:keydown.escape.stop="fp?.close()"
            wire:ignore
            wire:key="{{ $livewireKey }}.{{
                substr(md5(serialize([
                    $isDisabled,
                    $flatpickrConfig['mode'] ?? 'single',
                    $flatpickrConfig['enableTime'] ?? false,
                    $flatpickrConfig['noCalendar'] ?? false,
                ])), 0, 64)
            }}"
            class="w-full"
            id="{{ $id }}-wrapper"
        >
            <input
                x-ref="input"
                type="text"
                {{ $isDisabled ? 'disabled' : '' }}
                id="{{ $id }}"
                dusk="filament.forms.{{ $statePath }}"
                @class([
                    'fi-input block w-full border-none bg-transparent py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6',
                    'ps-0' => ! $isPrefixInline,
                    'ps-1' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                    'pe-0' => ! $isSuffixInline,
                    'pe-1' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
                ])
                @if ($placeholder = $getPlaceholder())
                    placeholder="{{ $placeholder }}"
                @endif
                @if ($isRequired() && (! $isConcealed()))
                    required
                @endif
                autocomplete="off"
            />
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
