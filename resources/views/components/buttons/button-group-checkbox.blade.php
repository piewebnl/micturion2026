@props([
    'label' => '',
    'wireModel',
    'name',
    'id',
    'options' => [],
    'values' => [],
])
@php
    $countSelected = 0;
    foreach ($options as $option) {
        if (in_array($option['value'], $values)) {
            $countSelected++;
        }
    }
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col']) }}>

    <label class="{{ $label == '' ? 'hidden' : '' }}">
        {{ $label }}

        @if ($countSelected != count($options))
            <span class="mb-2 text-sm">({{ $countSelected }} out of {{ count($options) }} selected)</span>
        @endif
    </label>
    <div class="mb-4 flex gap-4">
        @if ($countSelected != count($options))
            <a wire:click="checkAll('{{ $name }}')" type="link" name="uncheckall" id="uncheckall">
                Select all
            </a>
        @endif
        @if ($countSelected != 0)
            <a wire:click="uncheckAll('{{ $name }}')" type="link" name="uncheckall" id="uncheckall">
                Unselect all
            </a>
        @endif
    </div>
    @if ($label == '')
        <p class="mb-2 text-sm">{{ $countSelected }} out of {{ count($options) }} {{ $name }} selected.</p>
    @endif

    <div class="flex flex-wrap gap-2" role="group">
        @foreach ($options as $option)
            <label>
                <input value="{{ $option['value'] }}" id="{{ $id }}" name="{{ $name }}"
                    type="checkbox" aria-label="Select {{ $name }}" wire:model="{{ $wireModel }}"
                    class="absolute hidden" x-on:click="clicked = true">
                @php
                    $selectedClass = '';
                    if (in_array($option['value'], $values)) {
                        $selectedClass = 'selected';
                        $countSelected++;
                    }
                @endphp
                <span {{ $attributes->merge(['class' => 'btn-group-checkbox cursor-pointer" ' . $selectedClass]) }}>
                    @if ($selectedClass != '')
                        <x-icons.checked class="fill-red-500" />
                    @endif
                    {{ $option['label'] }}
                </span>
            </label>
        @endforeach
    </div>

</div>
