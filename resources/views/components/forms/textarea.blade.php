@props(['label', 'wireModel', 'name', 'id', 'rows' => 5, 'placeholder' => ''])

@php
    $labelClass = '';
    $inputClass = '';
    if ($errors->get($name)) {
        $labelClass .= ' text-red-700 dark:text-red-500';
        $inputClass .=
            ' border-red-500 bg-red-50 text-red-900 placeholder-red-700 focus:border-red-500 focus:ring-red-500 dark:border-red-400 dark:bg-red-100';
    }
@endphp

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    @if ($label)
        <label for="{{ $name }}" class="{{ $labelClass }}">
            {{ $label }}
        </label>
    @endif
    <textarea wire:model="{{ $wireModel }}" id="{{ $id }}" name="{{ $name }}"
        placeholder="{{ $placeholder }}" class="{{ $inputClass }}" rows="{{ $rows }}"></textarea>
    <x-forms.error-messages :messages="$errors->get($name)" />
</div>
