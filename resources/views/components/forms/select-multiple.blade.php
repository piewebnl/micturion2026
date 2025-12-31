@props([
    'label' => '',
    'values' => [],
    'options' => [],
    'id' => '',
    'wireModel',
    'name' => '',
    'placeholder' => __('Select'),
])

@php
    $labelClass = '';
    $countSelected = 0;
    foreach ($options as $option) {
        if (in_array($option['value'], $values)) {
            $countSelected++;
        }
    }
@endphp


<div {{ $attributes->merge(['class' => 'flex flex-col']) }} @click.outside="dropDownOpen=false" x-data="{
    options: {{ json_encode($options) }},
    name: '{{ $name }}',
    dropDownOpen: false
}">


    @if ($label)
        <label class="{{ $label == '' ? 'hidden' : '' }}">
            {{ $label }}
        </label>
    @endif


    <div class="relative">
        <button x-on:click="dropDownOpen =! dropDownOpen" class="btn" type="button">
            {{ $placeholder }}
            @if ($countSelected != count($options))
                <x-searchform.filters-used :countFiltersUsed="$countSelected" indicatorOnly="true" />
            @endif
            <x-icons.arrow-down />
        </button>
        <ul class="absolute z-10 bg-white py-2 shadow-sm dark:bg-gray-700" x-show="dropDownOpen">
            @foreach ($options as $index => $option)
                <li>
                    <div
                        class="mb-2 flex cursor-pointer items-center justify-center gap-2 px-3 hover:bg-gray-100 dark:hover:bg-gray-600">
                        <input value="{{ $option['value'] }}" id="{{ $id }}-{{ $index }}"
                            name="{{ $name }}" type="checkbox" aria-label="Select {{ $name }}"
                            class="checkbox" wire:model="{{ $wireModel }}" x-on:click="clicked = true">
                        <label for="{{ $id }}-{{ $index }}"
                            {{ $attributes->merge(['class' => 'mb-0 w-full cursor-pointer" ']) }}>
                            {{ $option['label'] }}
                        </label>
                    </div>
                </li>
            @endforeach
            @if ($countSelected != count($options))
                <li class="ml-8">
                    <a wire:click="checkAll('{{ $name }}')"type="link" class="p-2" name="uncheckall"
                        id="uncheckall">
                        Select all
                    </a>
                </li>
            @endif
            @if ($countSelected != 0)
                <li class="ml-8">
                    <a wire:click="uncheckAll('{{ $name }}')" type="link" class="p-2" name="uncheckall"
                        id="uncheckall">
                        Unselect all
                    </a>
                </li>
            @endif

        </ul>
    </div>

</div>
