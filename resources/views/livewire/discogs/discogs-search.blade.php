<div class="mb-6">

    <form wire:submit.prevent="search" onkeydown="return event.key != 'Enter';" wire:change="search"
        wire:keydown.debounce.300ms="search" x-data="{ searchOpen: false }" x-cloak class="max-w-full">


        <div class="item-start flex flex-row flex-wrap items-end gap-4">

            <x-forms.input :wireModel="'filterValues.keyword'" id="keyword" placeholder="Search" name="keyword" type="text"
                label="Search">
            </x-forms.input>


            <a href="music/albums-without-discogs">Show without</a>

            <button x-on:click="searchOpen =! searchOpen" type="button" class="btn-primary" name="clear"
                id="clear">
                Filters
                <x-searchform.filters-used :countFiltersUsed="$countFiltersUsed" indicatorOnly="true" />
            </button>

            <x-forms.select :wireModel="'filterValues.matched'" :hideLabel=true id="matched" name="matched" :options="$searchFormData['matched']"
                class="mb-2" />

            <x-searchform.clear :beenFiltered="$beenFiltered" />

        </div>

        <div x-show="searchOpen" class="mt-4 bg-zinc-200 p-4 dark:bg-zinc-800">

            <x-forms.show-filterted :values="$filterValues" :searchFormData="$searchFormData" />

            <x-forms.select-multiple wire:change="search" :wireModel="'filterValues.formats'" id="format" name="formats" label=""
                :options="$searchFormData['formats']" placeholder="Formats" :values="$filterValues['formats']" class="mb-2" />



            <x-buttons.toggle wire:change="search" :wireModel="'filterValues.show_notes'" id="show-notes" label="Show Notes"
                placeholder="Show Notes" />

        </div>

    </form>

</div>
