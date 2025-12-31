@props(['type', 'images', 'size' => 500, 'showDelete' => false, 'alt' => ''])

@if (is_array($images))
    @foreach ($images as $key => $image)
        <x-images.image type="{{ $type }}" alt="tekst" id="{{ $id }}" size="{{ $size }}"
            slug="{{ $images->slug }}" hash="{{ $images->hash }}" largestWidth="{{ $images->largest_width }}"
            showDelete="{{ $showDelete }}" {{ $attributes->merge(['class' => '']) }} />
    @endforeach
@else
    @if (isset($images->id))
        <x-images.image type="{{ $type }}" alt="tekst" id="{{ $images->id }}" size="{{ $size }}"
            slug="{{ $images->slug }}" hash="{{ $images->hash }}" largestWidth="{{ $images->largest_width }}"
            showDelete="{{ $showDelete }}" {{ $attributes->merge(['class' => '']) }} />
    @else
        <x-images.image-empty size="{{ $size }}" />
    @endif
@endif
