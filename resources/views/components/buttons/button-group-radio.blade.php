@props([
    'label' => '',
    'wireModel',
    'name',
    'id',
    'options' => [],
    'values' => [],
])
@guest
    @foreach ($options as $option)
        @if (isset($option['admin']) && $option['admin'] == true)
            @php
                unset($options[$loop->index]);
            @endphp
        @endif
    @endforeach
@endguest
<div {{ $attributes->merge(['class' => 'flex flex-col']) }}>
    <label class="{{ $label == '' ? 'hidden' : '' }}">{{ $label }}</label>

    <div class="flex-end inline-flex" role="group" x-data="{ selected: @entangle($wireModel) }">
        @foreach ($options as $key => $option)
            @php
                $class = ($key === 0 ? 'first ' : '') . ($key === count($options) - 1 ? 'last ' : '');
                $icon = isset($option['icon']) ? 'icons.' . $option['icon'] : null;
                $inputId = $id . '-' . $key;
            @endphp

            <label class="mb-0" for="{{ $inputId }}">
                <input id="{{ $inputId }}" name="{{ $name }}" type="radio" class="sr-only"
                    :value="@js($option['value'])" x-model="selected">

                <span class="{{ $class }} btn-group-radio"
                    :class="{ 'selected': selected == @js($option['value']) }">
                    @if ($icon)
                        <x-dynamic-component :component="$icon" />
                    @endif
                    {{ $option['label'] }}
                </span>
            </label>
        @endforeach
    </div>
</div>
