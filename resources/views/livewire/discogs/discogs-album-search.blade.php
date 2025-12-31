<div class="flex items-center gap-2" wire:key="album-search-{{ $discogsReleaseId }}">

    <input type="text" placeholder="Search album/artist…" wire:model.live.debounce.300ms="keyword" autocomplete="off" />

    @if ($items)
        <select wire:change="selectAlbum($event.target.value)">
            <option value="">Search for an album</option>
            @foreach ($items as $item)
                <option value="{{ $item['value'] }}" @selected($selectedAlbumId === $item['value'])>
                    {{ $item['label'] }}
                </option>
            @endforeach
        </select>
    @endif
    @if ($selectedAlbumId)
        <x-buttons.button wire:click="saveDiscogsReleaseAlbum()" class="btn-secondary">Save</x-buttons.button>
    @endif

</div>
