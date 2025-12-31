@props([
    'label' => '',
    'values' => [],
    'options' => [],
    'fieldName' => '',
    'placeholder' => 'Select',
    'tagClass' => 'bg-teal-400',
])

@php
    $labelClass = '';
@endphp

<div>
    @if ($label)
        <label for="tags" class="{{ $labelClass }}">
            {{ $label }}
        </label>
    @endif

    <div x-data="{
        searchValue: '',
        options: {{ json_encode($options) }},
        fieldName: '{{ $fieldName }}',
        showOptions: false
    
    }">
        <div @click.outside="showOptions=false">
            <div class="flex flex-row">
                <ul class="flex">
                    @foreach ($options as $option)
                        @if (in_array($option['value'], $values))
                            <li
                                class="mr-1 inline-flex flex-shrink-0 items-center gap-2 rounded-full px-2 text-white {{ $tagClass }}">
                                {{ $option['label'] }}
                            </li>
                        @endif
                    @endforeach
                </ul>

                <input class="ml-2 flex-1" x-ref="input" @focus="showOptions=true" type="text"
                    x-model.debounce="searchValue" placeholder="Find tags..." />
            </div>

            <ul class="absolute z-50 border border-solid dark:border-zinc-900" x-show="showOptions">
                <template x-for="option in options">
                    <li class="bg-zinc-800 dark:hover:bg-zinc-700">
                        <button @click="$wire.multiSelectAdd(fieldName,option.value); showOptions = false"
                            class='h-full w-full p-2 text-left' x-text="option.label"></button>
                    </li>
                </template>
            </ul>
        </div>
    </div>

</div>
