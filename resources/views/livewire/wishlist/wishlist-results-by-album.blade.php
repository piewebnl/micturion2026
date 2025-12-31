<section class="p-4">
    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <table class="table-layout w-full">
        <tbody>
            @foreach ($wishlistAlbums as $index => $wishlistAlbum)
                @if (($index > 0 && $wishlistAlbum['album_name'] != $wishlistAlbums[$index - 1]['album_name']) || $index == 0)
                    <tr>
                        <td colspan="4">
                            <div class="flex w-full flex-wrap items-center gap-4 py-4">
                                @if ($wishlistAlbum['album_image_slug'] ?? false)
                                    <x-images.image type="album" slug="{{ $wishlistAlbum['album_image_slug'] }}"
                                        size="150" largestWidth="{{ $wishlistAlbum['album_image_largest_width'] }}"
                                        hash="{{ $wishlistAlbum['album_image_hash'] }}"
                                        alt="{{ $wishlistAlbum['artist_name'] }} - {{ $wishlistAlbum['album_name'] }} Album Cover" />
                                @endif

                                <span class="text-2xl">
                                    {{ $wishlistAlbum['artist_name'] }} -
                                    {{ $wishlistAlbum['album_name'] }}
                                    <br />

                                    <span class="text-sm">{{ $wishlistAlbum['album_year'] }}</span>
                                    <br />
                                    <span class="text-lg">
                                        @if ($wishlistAlbum['wishlist_album_format'] == '')
                                            CD/LP
                                        @else
                                            {{ $wishlistAlbum['wishlist_album_format'] }}
                                        @endif
                                    </span>

                                    <br />
                                    @if ($wishlistAlbum['wishlist_album_notes'] ?? false)
                                        <span class="text-lg text-amber-500">Notes:
                                            {{ $wishlistAlbum['wishlist_album_notes'] }}</span>
                                        </br />
                                    @endif
                                    <a href="{{ route('filament.admin.resources.wishlist-albums.edit', $wishlistAlbum['wishlist_album_id']) }}"
                                        class="text-sm">
                                        Edit

                                    </a>
                                </span>

                            </div>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td>
                        @if ($wishlistAlbum['wishlist_album_price_url'])
                            <a href="{{ $wishlistAlbum['wishlist_album_price_url'] }}"
                                target="_blank">{{ $wishlistAlbum['music_store_name'] }}</a>
                        @else
                            {{ $wishlistAlbum['music_store_name'] }}
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
