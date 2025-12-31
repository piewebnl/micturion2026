@props(['label', 'wireModel', 'name', 'id'])

@php
    // Make sure it's a boolean (not integer)
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center mb-6']) }}>
    <input type="checkbox" wire:model="{{ $wireModel }}" id="{{ $id }}" name="{{ $name }}"
        class="h-5 w-5 rounded border-zinc-300 bg-zinc-100 text-blue-600 checked:bg-[16px,16px] focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:ring-offset-zinc-800 dark:focus:ring-blue-600 dark:focus:ring-offset-zinc-800" />
    <label for="{{ $name }}" class="mb-0 ml-2 font-medium text-zinc-900 dark:text-zinc-300">{{ $label }}
    </label>
    <x-forms.error-messages :messages="$errors->get($name)" />
</div>
