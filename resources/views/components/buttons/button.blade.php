@props(['link' => null, 'label', 'type' => 'button'])

@if ($link || $type == 'link')
    <a href="{{ $link }}" type="{{ $type }}" {{ $attributes->merge(['class' => '']) }}>
        {{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => '']) }}>
        {{ $slot }}</button>
@endif
