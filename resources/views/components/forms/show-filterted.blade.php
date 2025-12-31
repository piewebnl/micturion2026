@props([
    'values' => [],
    'searchFormData' => [],
    'wireModelPrefix' => 'filterValues.',
])

<ul class="mb-2 block w-full">

    @foreach ($values as $key => $value)
        @if (isset($searchFormData[$key]))
            @if (is_array($value))
                @foreach ($searchFormData[$key] as $index => $data)
                    @if (in_array($data['value'], $value))
                        <span class="relative inline-flex items-center rounded-2xl bg-gray-500 px-3">
                            {{ $data['label'] }}
                            <input value="{{ $data['value'] }}" id="test-{{ $index }}" type="checkbox"
                                name="{{ $data['label'] }}" class="peer sr-only" wire:model="{{ $wireModelPrefix . $key }}"
                                x-on:click="clicked = true">
                            <label for="test-{{ $index }}"
                                class="ml-2 mt-2 cursor-pointer font-bold text-white peer-checked:text-white">
                                <x-icons.close />
                            </label>
                        </span>
                    @endif
                @endforeach
            @else
            @endif
        @endif
    @endforeach

</ul>
