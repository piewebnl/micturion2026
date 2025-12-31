@props(['width' => 22, 'height' => 22])

<div>
    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"
        width="{{ $width }}" height="{{ $height }}" {{ $attributes->merge(['class' => '']) }}>
        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
    </svg>
</div>
