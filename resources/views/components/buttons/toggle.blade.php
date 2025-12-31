@props(['label', 'wireModel', 'id'])

@php
    // Make sure it's a boolean (0 or 1)
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col']) }}>
    <div class="flex rounded p-2">
        <label class="inline-flex cursor-pointer items-center">
            <input type="checkbox" wire:model="{{ $wireModel }}" id="{{ $id }}" class="peer sr-only">
            <div
                class="peer relative h-5 w-9 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:border-gray-500 dark:bg-gray-600 dark:peer-focus:ring-blue-800 rtl:peer-checked:after:translate-x-[-100%]">
            </div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $label }}</span>
        </label>
    </div>
</div>
