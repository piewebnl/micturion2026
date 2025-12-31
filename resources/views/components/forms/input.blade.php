@props([
    'type' => 'text',
    'label',
    'wireModel',
    'name',
    'id',
    'size' => null,
    'placeholder' => '',
    'autocomplete' => 'on',
])

@php
    $labelClass = '';
    $inputClass = '';
    $inputClass = '';

    if (!$size) {
        $inputClass .= ' w-full';
    }

    if ($errors->get($name)) {
        $labelClass .= ' text-red-700 dark:text-red-500';
        $inputClass .=
            ' border-red-500 bg-red-50 text-red-900 placeholder-red-700 focus:border-red-500 focus:ring-red-500 dark:border-red-400 dark:bg-red-100';
    }
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col ']) }}>
    <label class="{{ $label == '' ? 'hidden' : '' }}">
        {{ $label }}
    </label>
    <input type="{{ $type }}" wire:model="{{ $wireModel }}" id="{{ $id }}" name="{{ $name }}"
        placeholder="{{ $placeholder }}" class="{{ $inputClass }}" size="{{ $size }}"
        autocomplete="{{ $autocomplete }}" />
    <x-forms.error-messages :messages="$errors->get($wireModel)" />
</div>
