@props(['width' => 22, 'height' => 22])

<div>
    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" width="{{ $width }}"
        height="{{ $height }}" {{ $attributes->merge(['class' => '']) }}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M10 16.5c0-1-8-2.7-9-2V1.8c1-1 9 .707 9 1.706M10 16.5V3.506M10 16.5c0-1 8-2.7 9-2V1.8c-1-1-9 .707-9 1.706" />
    </svg>
</div>
