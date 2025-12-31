@props([
    'label' => '',
    'wireModel',
    'name',
    'value',
    'id',
    'placeholder' => __('Select'),
    'options' => [],
])

@php
    $selectClass = '';
    if ($errors->get($name)) {
        $labelClass .= ' text-red-700 dark:text-red-500';
        $selectClass .=
            ' border-red-500 bg-red-50 text-red-900 placeholder-red-700 focus:border-red-500 focus:ring-red-500 dark:border-red-400 dark:bg-red-100';
    }
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col']) }}>
    <label class="{{ $label == '' ? 'hidden' : '' }}">
        {{ $label }}
    </label>

    <select id="{{ $id }}" name="{{ $name }}" aria-label="Select {{ $name }}"
        wire:model="{{ $wireModel }}" class="{{ $selectClass }}">
        @if ($placeholder != '')
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $option)
            <option value="{{ $option['value'] }}">
                {{ $option['label'] }}</option>
        @endforeach
    </select>
    <x-forms.error-messages :messages="$errors->get($wireModel)" />
</div>
