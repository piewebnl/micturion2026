@props(['rating' => 0])

@php
    $stars = 0;
    if ($rating) {
        $stars = floor($rating / 20);
    }
@endphp

<div class="flex">
    @for ($i = 0; $i < $stars; $i++)
        <x-icons.star width="18" height="18" />
    @endfor
</div>
