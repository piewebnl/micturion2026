<section class="p-4">

    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <div class="table-layout">
        <table>
            <thead>
                <th></th>
                <th colspan="7">Discogs release</th>
                <th colspan="3">iTunes</th>
                <th>
                    </td>
            </thead>
            <thead>
                <th></th>
                <th>Artwork</th>
                <th>Artist</th>
                <th>Album</th>
                <th>Format</th>
                <th>Date</th>
                <th>Country</th>
                <th>Score</th>
                <th>Artwork</th>
                <th>Artist</th>
                <th>Album</th>
                <th>Notes</th>
            </thead>
            @foreach ($discogsReleases as $discogsRelease)
                @livewire('discogs.discogs-release', ['discogsRelease' => $discogsRelease, 'showNotes' => $filterValues['show_notes']], key(Str::random()))
            @endforeach
        </table>

        @if (!empty($loadedDiscogsReleases))
            <x-load-more.load-more :items=$loadedDiscogsReleases />
            @if ($loadedDiscogsReleases->currentPage() == $loadedDiscogsReleases->lastPage())
                <p class="p-4">No more results</p>
            @endif
        @else
            <p class="p-4">Nothing found</p>
        @endif
</section>
