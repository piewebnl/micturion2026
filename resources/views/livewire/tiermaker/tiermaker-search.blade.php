<div class="mb-6">

    <form wire:submit.prevent="searchNew" onkeydown="return event.key != 'Enter';" wire:change="searchNew"
        wire:keydown.debounce.300ms="searchNew" x-cloak class="max-w-full">

        <div class="mb-4">
            <x-forms.input :wireModel="'newkeyword'" id="newkeyword" placeholder="New" name="SearchNew" type="text"
                label="New Tiermaker">
            </x-forms.input>
        </div>

        @if ($artists)
            <div class="mb-4">
                @foreach ($artists as $artist)
                    <a href="/tiermaker/edit/{{ $artist->id }}" class="btn btn-secondary">Create {{ $artist->name }}
                        Tiermaker</a><br />
                @endforeach
            </div>
        @endif

    </form>

    <form wire:submit.prevent="search" onkeydown="return event.key != 'Enter';" wire:change="search"
        wire:keydown.debounce.300ms="search" x-data="{ searchOpen: false }" x-cloak class="max-w-full">

        <div class="item-start flex flex-row flex-wrap items-end gap-4">


            <x-forms.input :wireModel="'filterValues.keyword'" id="keyword" placeholder="Search" name="keyword" type="text"
                label="Search">
            </x-forms.input>


            <button x-on:click="searchOpen =! searchOpen" type="button" class="btn-primary" name="clear"
                id="clear">
                Filters
                <x-searchform.filters-used :countFiltersUsed="$countFiltersUsed" indicatorOnly="true" />
            </button>

            <x-searchform.clear :beenFiltered="$beenFiltered" />

        </div>

        <div x-show="searchOpen" class="mt-4 bg-zinc-200 p-4 dark:bg-zinc-800">

            <x-forms.show-filterted :values="$filterValues" :searchFormData="$searchFormData" />


        </div>

    </form>

</div>
