@props(['width' => 22, 'height' => 22])

<div>
    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16" width="{{ $width }}"
        height="{{ $height }}" {{ $attributes->merge(['class' => '']) }}>
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M1 1h14M1 6h14M1 11h7" />
    </svg>
</div>
