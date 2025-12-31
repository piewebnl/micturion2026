<section class="p-4">
    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <table class="table-layout w-full">
        <tbody>
            @foreach ($wishlistAlbums as $index => $wishlistAlbum)
                @if (
                    ($index > 0 && $wishlistAlbum['music_store_name'] != $wishlistAlbums[$index - 1]['music_store_name']) ||
                        $index == 0)
                    <tr>
                        <td colspan="5">
                            <div class="flex w-full flex-wrap items-center gap-4 py-4 text-2xl">
                                {{ $wishlistAlbum['music_store_name'] }}
                                @if ($wishlistAlbum['music_store_url'])
                                    <br />
                                    @if ($wishlistAlbum['wishlist_album_notes'] ?? false)
                                        <span class="text-lg text-amber-500">Notes:
                                            {{ $wishlistAlbum['wishlist_album_notes'] }}</span>
                                        </br />
                                    @endif
                                    <a href="{{ $wishlistAlbum['music_store_url'] }}" class="text-sm" target="_blank">Visit
                                        site</a>
                                @endif
                            </div>

                        </td>
                    </tr>
                @endif
                <tr>
                    <td>
                        @if ($wishlistAlbum['wishlist_album_price_url'])
                            <a href="{{ $wishlistAlbum['wishlist_album_price_url'] }}" target="_blank">
                                {{ $wishlistAlbum['artist_name'] }} -
                                {{ $wishlistAlbum['album_name'] }}
                            </a>
                        @else
                            <span>
                                {{ $wishlistAlbum['artist_name'] }} -
                                {{ $wishlistAlbum['album_name'] }}
                            </span>
                        @endif
                    </td>

                    <td>
                        @if ($wishlistAlbum['wishlist_album_price_price'])
                            &euro; {{ $wishlistAlbum['wishlist_album_price_price'] }}
                        @else
                            No price found
                        @endif
                    </td>
                    <td>{{ $wishlistAlbum['wishlist_album_price_format'] }}</td>
                    <td>{{ $wishlistAlbum['wishlist_album_price_score'] }}</td>
                    <td>
                        <a href="{{ route('filament.admin.resources.wishlist-albums.edit', $wishlistAlbum['wishlist_album_id']) }}"
                            class="">
                            Edit

                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (!empty($loadedWishlistAlbums))
        <x-load-more.load-more :items=$loadedWishlistAlbums />
        @if ($loadedWishlistAlbums->currentPage() == $loadedWishlistAlbums->lastPage())
            <p class="p-4">No more results</p>
        @endif
    @else
        <p class="p-4">Nothing found</p>
    @endif
</section>
