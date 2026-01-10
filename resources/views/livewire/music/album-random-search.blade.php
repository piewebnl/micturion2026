<div class="mb-6">

    <form wire:submit.prevent="search" onkeydown="return event.key != 'Enter';" wire:change="search"
        wire:keydown.debounce.300ms="search" x-data="{ searchOpen: false }" class="max-w-full">

        <div class="item-start flex flex-row flex-wrap items-end gap-4">

            <button
                wire:click="$dispatch('album-random-search-set-filter', { field: 'formats_by_label', value: ['CD','LP'] })"
                type="button" class="btn-primary">
                Random CDs and LPs
            </button>

            <button
                wire:click="$dispatch('album-random-search-set-filter', { field: 'formats_by_label', value: ['LP'] })"
                type="button" class="btn-primary">
                LPs only
            </button>

            <button
                wire:click="$dispatch('album-random-search-set-filter', { field: 'formats_by_label', value: ['CD'] })"
                type="button" class="btn-primary">
                CDs only
            </button>

            <button wire:click="$dispatch('album-random-search')" type="button" class="btn-primary">
                Re-roll
            </button>

            <button x-on:click="searchOpen =! searchOpen" type="button" class="btn-primary" name="clear"
                id="clear">
                Filters
            </button>

            <x-searchform.clear :beenFiltered="$beenFiltered" />

        </div>


        <div x-show="searchOpen" class="mt-4 bg-zinc-200 p-4 dark:bg-zinc-800">

            <x-buttons.toggle wire:change="search" :wireModel="'filterValues.compilations'" id="compilations" label="Compilations"
                placeholder="Compilations" />

            <x-buttons.toggle wire:change="search" :wireModel="'filterValues.songs'" id="songs" label="Songs"
                placeholder="Songs" />

            <div class="flex flex-row flex-wrap gap-4">

                <x-forms.select-multiple wire:change="search" :wireModel="'filterValues.categories'" id="categories" name="categories"
                    label="" :options="$searchFormData['categories']" placeholder="Categories" :values="$filterValues['categories']" />

                <x-forms.select-multiple wire:change="search" :wireModel="'filterValues.formats'" id="format" name="formats"
                    label="" :options="$searchFormData['formats']" placeholder="Formats" :values="$filterValues['formats']" />

            </div>

        </div>

    </form>

</div>
