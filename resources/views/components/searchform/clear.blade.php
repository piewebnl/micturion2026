@props(['beenFiltered'])

@if ($beenFiltered)
    <button @click="searchOpen = false" wire:click="clear" type="button" class="btn" name="clear" id="clear">
        <x-icons.close />Clear
    </button>
@endif
