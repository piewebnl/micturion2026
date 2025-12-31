<div class="mb-6">

    <form wire:submit.prevent="search" onkeydown="return event.key != 'Enter';" wire:change="search"
        wire:keydown.debounce.300ms="search" x-data="{ searchOpen: false }" x-cloak class="max-w-full">

        <div class="item-start flex flex-row flex-wrap items-end gap-4">

            <x-forms.input :wireModel="'filterValues.keyword'" id="keyword" placeholder="Search" name="keyword" type="text"
                label="Search">
            </x-forms.input>

            <x-buttons.toggle wire:change="search" :wireModel="'filterValues.show_low_scores'" id="show-low-scores" label="Show low scores"
                placeholder="Show low scores" />

            <x-forms.select wire:change="toggleSort" :wireModel="'filterValues.sort'" id="sort" name="sort" label="Sort"
                :options="$searchFormData['sort']" placeholder="" />


            <button wire:click="toggleOrder" type="button" class="mb-3" name="order" id="order">
                @if ($searchFormData['order_toggle_icon'] == 'up')
                    <x-icons.up />
                @else
                    <x-icons.down />
                @endif
            </button>

            <x-buttons.button-group-radio wire:change="search" :wireModel="'filterValues.format'" id="format" name="format"
                :options="$searchFormData['formats']" placeholder="" :values="$filterValues['format']" />

            <button x-on:click="searchOpen =! searchOpen" type="button" class="btn-primary" name="clear"
                id="clear">
                Filters
                @if ($beenFiltered && $countFiltersUsed > 0)
                    <span
                        class="rounded-full bg-amber-600 px-2 text-zinc-200 dark:text-zinc-300">{{ $countFiltersUsed }}</span>
                @endif
            </button>

            @if ($beenFiltered)
                <button @click="searchOpen = false" wire:click="clear" type="button" class="btn" name="clear"
                    id="clear">
                    <x-icons.close />Clear
                </button>
            @endif

        </div>

        <div x-show="searchOpen" class="mt-4 flex flex-row flex-wrap gap-4 bg-zinc-200 p-4 dark:bg-zinc-800">

            <x-forms.select wire:change="search" :wireModel="'filterValues.wishlist_album'" id="wishlist-albums" name="wishlist_albums"
                label="Wishlist albums" :options="$searchFormData['wishlist_albums']" placeholder="Select album" />

            <x-forms.select wire:change="search" :wireModel="'filterValues.music_store'" id="music-stores" name="music_stored"
                label="Music stores" :options="$searchFormData['music_stores']" placeholder="Select music store" />

        </div>

        <div class="mt-4">

            @auth
                <a href="{{ route('filament.admin.resources.wishlist-albums.create') }}" class="btn btn-primary">Add
                    wishlist album</a>
            @endauth
        </div>
    </form>

</div>
