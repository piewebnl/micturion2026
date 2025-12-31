<div class="mb-6">

    <form wire:submit.prevent="search" onkeydown="return event.key != 'Enter';" wire:change="search"
        wire:keydown.debounce.300ms="search" x-data="{ searchOpen: false }" x-cloak class="max-w-full">

        <div class="item-start flex flex-row flex-wrap items-end gap-4">

            <x-forms.input :wireModel="'filterValues.keyword'" id="keyword" placeholder="Search" name="keyword" type="text"
                label="Search">
            </x-forms.input>

            <x-searchform.sort :searchFormData="$searchFormData" />

            <x-buttons.button-group-radio wire:change="search" :wireModel="'filterValues.view'" id="view" name="view"
                :options="$searchFormData['view']" placeholder="" :values="$filterValues['view']" />


            <button x-on:click="searchOpen =! searchOpen" type="button" class="btn-primary" name="clear"
                id="clear">
                Filters
                <x-searchform.filters-used :countFiltersUsed="$countFiltersUsed" indicatorOnly="true" />
            </button>


            @if ($beenFiltered)
                <button @click="searchOpen = false" wire:click="clear" type="button" class="btn" name="clear"
                    id="clear">
                    <x-icons.close />Clear
                </button>
            @endif
        </div>
        <div x-show="searchOpen" class="mt-4 flex flex-row flex-wrap gap-4 bg-zinc-200 p-4 dark:bg-zinc-800">


            <x-buttons.button-group-radio wire:change="search" :wireModel="'filterValues.status'" id="status" name="status"
                :options="$searchFormData['status']" placeholder="" :values="$filterValues['status']" />

        </div>

    </form>

</div>
