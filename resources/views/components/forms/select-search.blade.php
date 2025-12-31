@props([
    'label' => '',
    'value' => '',
    'options' => [],
    'fieldName' => 'album',
    'placeholder' => __('Select'),
])


@php
    $labelClass = 'block mb-2 font-medium text-zinc-900 dark:text-white';
@endphp

<div>
    @if ($label)
        <label for="tags" class="{{ $labelClass }}">
            {{ $label }}
        </label>
    @endif
    <div x-data="selectConfigs()" class="relative flex flex-col items-center">
        <div class="w-full">
            <div @click.away="close()" class="flex">
                <input x-model="filter" @keyup.debounce.300ms="show = true"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @keydown.enter.stop.prevent="selectOption()"
                    class="w-full appearance-none outline-none">
                <div class="mb-2 flex w-10 items-center p-2">
                    <button @click="toggle()"
                        class="h-6 w-6 cursor-pointer text-gray-600 outline-none focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline x-show="!isOpen()" points="18 15 12 20 6 15"></polyline>
                            <polyline x-show="isOpen()" points="18 15 12 9 6 15"></polyline>
                        </svg>

                    </button>
                </div>
                <button @click="clear();$wire.searchSelect('{{ $fieldName }}',null)" type="button" class=""
                    name="clear" id="clear">
                    <x-icons.close />
                </button>
            </div>
        </div>
        <div x-show="isOpen()" class="absolute left-0 top-[100%] z-40 mt-2 max-h-[300px] w-full overflow-y-auto">
            <div class="flex w-full flex-col dark:bg-zinc-800">
                <template x-for="(option, index) in filteredOptions()" :key="index">
                    <div @click="onOptionClick(index);$wire.searchSelect('{{ $fieldName }}',option.value)"
                        :class="classOption(option.value, index)" :aria-selected="focusedOptionIndex === index">
                        <span class="relative flex w-full items-center truncate px-2 py-0.5 hover:bg-zinc-500"
                            x-text="option.label"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>


<script>
    function selectConfigs() {
        return {
            filter: '',
            show: false,
            selected: null,
            focusedOptionIndex: null,
            options: @js($options),
            close() {
                this.show = false;
                this.filter = this.selectedName();
                this.focusedOptionIndex = this.selected ? this.focusedOptionIndex : null;
            },
            open() {
                this.show = true;
                this.filter = '';
            },
            toggle() {
                if (this.show) {
                    this.close();
                } else {
                    this.open()
                }
            },
            isOpen() {
                return this.show === true
            },
            selectedName() {
                return this.selected ? this.selected.label : this.filter;
            },
            clear() {
                this.filter = '';
                this.selected = null;
            },
            classOption(id, index) {
                const isSelected = this.selected ? (id == this.selected.value) : false;
                const isFocused = (index == this.focusedOptionIndex);
                return {
                    'cursor-pointer w-full': true
                };
            },
            filteredOptions() {
                return this.options ?
                    this.options.filter(option => {
                        return (option.label.toLowerCase().indexOf(this.filter) > -1)
                    }) : {}
            },
            onOptionClick(index) {
                this.focusedOptionIndex = index;
                this.selectOption();
            },
            selectOption() {
                if (!this.isOpen()) {
                    return;
                }
                this.focusedOptionIndex = this.focusedOptionIndex ?? 0;
                const selected = this.filteredOptions()[this.focusedOptionIndex]
                if (this.selected && this.selected.value == selected.value) {
                    this.filter = '';
                    this.selected = null;
                } else {
                    this.selected = selected;
                    this.filter = this.selectedName();
                }
                this.close();
            },

        }

    }
</script>
