<section class="p-4">
    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <div class="table-layout">
        <table>

            <thead>
                <th>Artwork</th>
                <th>Artist</th>
                <th>Album</th>
                <th>Format</th>
                <th>Skipped</th>
                <th></th>
            </thead>
            @foreach ($albums as $album)
                <tr x-data="{ showMore: false }" x-cloak>
                    <td class="w-[50px]">
                        @if ($album['album_image_slug'])
                            <x-images.image type="{{ $album['category_image_type'] }}"
                                slug="{{ $album['album_image_slug'] }}" size="50"
                                largestWidth="{{ $album['album_image_largest_width'] }}" class="w-[50px]"
                                hash="{{ $album['album_image_hash'] }}"
                                alt="{{ $album['artist_name'] }} - {{ $album['album_name'] }} Album Cover" />
                        @endif
                    </td>
                    <td>{{ $album['artist_name'] }}</td>
                    <td>{{ $album['album_name'] }}</td>
                    <td>{{ $album['format_name'] }}</td>
                    <td>
                        @if ($album['discogs_release_release_id'] == '0')
                            Skipped
                        @endif
                    </td>
                    <td>

                        <x-buttons.button wire:click="skipDiscogsRelease({{ $album['album_id'] }})"
                            class="btn-secondary">Mark as
                            skip</x-buttons.button>
                    </td>

                </tr>
            @endforeach
        </table>

        @if (!empty($loadedAlbums))
            <x-load-more.load-more :items=$loadedAlbums />
            @if ($loadedAlbums->currentPage() == $loadedAlbums->lastPage())
                <p class="p-4">No more results</p>
            @endif
        @else
            <p class="p-4">Nothing found</p>
        @endif
</section>
