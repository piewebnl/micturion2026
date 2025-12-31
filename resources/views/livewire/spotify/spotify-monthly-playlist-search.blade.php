<div class="mb-6">

    <form wire:submit.prevent="search" onkeydown="return event.key != 'Enter';" wire:change="search"
        wire:keydown.debounce.300ms="search" x-data="{ searchOpen: false }" x-cloak class="max-w-full">

        <div class="item-start flex flex-row flex-wrap items-end gap-4">

            <x-forms.input :wireModel="'filterValues.keyword'" id="keyword" placeholder="Search" name="keyword" type="text"
                label="Search">
            </x-forms.input>

            <x-buttons.button-group-radio wire:change="search" :wireModel="'filterValues.view'" id="view" name="view"
                :options="$searchFormData['view']" placeholder="" :values="$filterValues['view']" />

            <x-searchform.sort :searchFormData="$searchFormData" />

            <button x-on:click="searchOpen =! searchOpen" type="button" class="btn-primary" name="clear"
                id="clear">
                Filters
                @if ($beenFiltered && $countFiltersUsed > 0)
                    <span
                        class="rounded-full bg-amber-600 px-2 text-zinc-200 dark:text-zinc-300">{{ $countFiltersUsed }}</span>
                @endif
            </button>

            <x-searchform.clear :beenFiltered="$beenFiltered" />

        </div>
        <div x-show="searchOpen" class="mt-4 flex flex-row flex-wrap gap-4 bg-zinc-200 p-4 dark:bg-zinc-800">

            <x-forms.select :wireModel="'filterValues.year'" :hideLabel=true placeholder="Year" label="Year" id="year"
                name="year" :options="$searchFormData['years']" />
        </div>


    </form>

</div>
