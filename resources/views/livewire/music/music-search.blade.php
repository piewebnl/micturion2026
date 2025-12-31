<div class="relative mb-6">

    <form wire:submit.prevent="search" onkeydown="return event.key != 'Enter';" wire:change="search"
        wire:keydown.debounce.300ms="search" x-cloak class="max-w-full">

        <div class="item-start flex flex-row flex-wrap items-end gap-4">

            <x-forms.input :wireModel="'filterValues.keyword'" id="keyword" placeholder="Search" name="keyword" type="text"
                label="Search">
            </x-forms.input>

            <x-searchform.sort :searchFormData="$searchFormData" />

            <x-buttons.button-group-radio wire:change="search" :wireModel="'filterValues.view'" id="view" name="view"
                :options="$searchFormData['view']" placeholder="" :values="$filterValues['view']" />

            <button wire:click="toggleShowFilter" type="button" class="btn-primary" name="clear" id="clear">
                Filters
                <x-searchform.filters-used :countFiltersUsed="$countFiltersUsed" indicatorOnly="true" />
            </button>

            <x-searchform.clear :beenFiltered="$beenFiltered" />

        </div>


        @if ($filterValues['show_filter'])
            <div class="mt-4 bg-zinc-200 p-4 dark:bg-zinc-800">

                <div class="flex flex-row justify-end">
                    <button wire:click="toggleShowFilter" type="button" class="btn-outline" name="close"
                        id="close">
                        <x-icons.close />
                    </button>
                </div>

                <x-forms.show-filterted :values="$filterValues" :searchFormData="$searchFormData" />

                <div class="flex flex-row flex-wrap gap-4">

                    <x-forms.select-multiple wire:change="search" :wireModel="'filterValues.categories'" id="categories" name="categories"
                        label="" :options="$searchFormData['categories']" placeholder="Categories" :values="$filterValues['categories']" />

                    <x-forms.select-multiple wire:change="search" :wireModel="'filterValues.formats'" id="format" name="formats"
                        label="" :options="$searchFormData['formats']" placeholder="Formats" :values="$filterValues['formats']" />

                    <x-forms.select-multiple wire:change="search" :wireModel="'filterValues.genres'" id="genres" name="genres"
                        label="" :options="$searchFormData['genres']" placeholder="Genres" :values="$filterValues['genres']" />

                    <x-forms.select :wireModel="'filterValues.year'" :hideLabel=true placeholder="Year" label="" id="year"
                        name="year" :options="$searchFormData['years']" />

                </div>

                <div class="mt-4">
                    @auth
                        @if ($filterValues['view'] == 'spines')
                            <x-buttons.button-group-radio wire:change="search" :wireModel="'filterValues.spine_images_checked'" id="spine_images_checked"
                                label="Spine images" name="view" :options="$searchFormData['spine_images_checked']" placeholder="" :values="$filterValues['spine_images_checked']" />
                        @endif
                    @endauth
                </div>

                <div class="mt-4 flex flex-row flex-wrap items-end gap-4">
                    <button wire:click="owned" type="button" class="btn-primary mr-4" name="owned" id="owned">
                        Select owned
                    </button>

                    <x-buttons.toggle wire:change="search" :wireModel="'filterValues.songs'" id="songs" label="Songs"
                        placeholder="Songs" />

                    <x-buttons.toggle wire:change="search" :wireModel="'filterValues.compilations'" id="compilations" label="Compilations"
                        placeholder="Compilations" />



                </div>

                <div class="mt-2 flex flex-row flex-wrap items-center text-sm font-medium">
                    @foreach (range('a', 'z') as $letter)
                        @if ($filterValues['start_letter'] == $letter)
                            <button wire:click="loadLetter('{{ $letter }})" type="button"
                                class="p-2 text-amber-500">{{ strtoupper($letter) }}
                            </button>
                        @else
                            <button wire:click="loadLetter('{{ $letter }}')" class="p-2"
                                type="button">{{ strtoupper($letter) }}
                            </button>
                        @endif
                    @endforeach

                </div>

            </div>
        @endif
    </form>




</div>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('setUrlHash', ({
            letter
        }) => {
            location.hash = letter;
        });
    });
</script>
