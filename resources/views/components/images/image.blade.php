@props([
    'type',
    'id',
    'slug',
    'size' => 500,
    'hash' => '',
    'largestWidth' => 150,
    'showDelete' => false,
    'alt' => '',
    'label' => '',
    'lazy' => true,
])

@php
    $config = Config::get('images');

    $loading = '';
    if ($lazy) {
        $loading = 'loading="lazy"';
    }

    $folder = $config[$type]['dest_image_path'];
    $imageSizes = $config[$type]['sizes'];

    $actualSize = $size;
    if ($largestWidth < $size) {
        $actualSize = $largestWidth;
    }

    $srcSet = '';
    foreach ($imageSizes as $index => $imageSize) {
        $w = $imageSize[0];
        $h = $imageSize[1];

        if ($index == 1) {
            $srcSet =
                '/storage/images' . $folder . '-' . $w . 'x' . $h . '/' . $slug . '.webp?' . $hash . ' ' . $w . 'w';
        }

        if ($index > 1) {
            if ($actualSize >= $w && $largestWidth >= $h) {
                $srcSet =
                    $srcSet .
                    ', /storage/images' .
                    $folder .
                    '-' .
                    $w .
                    'x' .
                    $h .
                    '/' .
                    $slug .
                    '.webp?' .
                    $hash .
                    ' ' .
                    $w .
                    'w';
            }
        }
    }

@endphp
<div class="relative">

    @if ($showDelete)
        <button @click="$wire.deleteImage({{ $id }})" class="absolute right-0 m-2"> <svg
                class="h-6 w-6 text-gray-800 drop-shadow dark:text-white" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z" />
            </svg></button>
    @endif
    @if ($slug)
        <img src="/storage/images/{{ $type }}-{{ $actualSize }}x{{ $actualSize }}/{{ $slug }}.webp?{{ $hash }}"
            srcSet="{{ $srcSet }}" alt="{{ $alt }}" width="{{ $actualSize }}"
            height="{{ $actualSize }}" {{ $attributes->merge(['class' => '']) }} {{ $loading }} />
        @if ($label)
            <span
                class="absolute bottom-0 right-0 bg-slate-500 bg-opacity-50 px-5 py-2 text-zinc-800 dark:text-zinc-300">{{ $label }}</span>
        @endif
    @else
        <x-images.image-empty size="{{ $size }}" width="{{ $actualSize }}" height="{{ $actualSize }}"
            {{ $attributes->merge(['class' => '']) }} />
        @if ($label)
            <span
                class="absolute bottom-0 right-0 bg-slate-500 bg-opacity-50 px-5 py-2 text-zinc-800 dark:text-zinc-300">{{ $label }}</span>
        @endif
    @endif
</div>
