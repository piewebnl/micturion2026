@props(['width' => 22, 'height' => 22])

<div>
    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
        width="{{ $width }}" height="{{ $height }}" {{ $attributes->merge(['class' => '']) }}>
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 19V5m0 14-4-4m4 4 4-4" />
    </svg>
</div>
