<div class="mb-6">

    <form wire:submit.prevent="search" onkeydown="return event.key != 'Enter';" wire:change="search"
        wire:keydown.debounce.300ms="search" x-data="{ searchOpen: false }" x-cloak class="max-w-full">


        <div class="item-start flex flex-row flex-wrap items-end gap-4">

            <x-forms.input :wireModel="'filterValues.keyword'" id="keyword" placeholder="Search" name="keyword" type="text"
                label="Search">
            </x-forms.input>

            <a href="/admin/discogs">Discogs releases</a>

            <x-forms.select :wireModel="'filterValues.matched'" :hideLabel=true id="matched" name="matched" :options="$searchFormData['matched']"
                class="mb-2" />

            <button x-on:click="searchOpen =! searchOpen" type="button" class="btn-primary" name="clear" <button
                x-on:click="searchOpen =! searchOpen" type="button" class="btn-primary" name="clear" id="clear">
                Filters
                <x-searchform.filters-used :countFiltersUsed="$countFiltersUsed" indicatorOnly="true" />
            </button>

            <x-searchform.clear :beenFiltered="$beenFiltered" />

        </div>


    </form>

</div>
