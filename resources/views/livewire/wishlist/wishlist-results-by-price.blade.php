<section class="p-4">
    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <table class="table-layout w-full">
        <tbody>
            @foreach ($wishlistAlbums as $index => $wishlistAlbum)
                <tr>
                    <td class="w-14">
                        @if ($wishlistAlbum['album_image_slug'] ?? false)
                            <x-images.image type="album" slug="{{ $wishlistAlbum['album_image_slug'] }}" size="50"
                                largestWidth="{{ $wishlistAlbum['album_image_largest_width'] }}"
                                hash="{{ $wishlistAlbum['album_image_hash'] }}"
                                alt="{{ $wishlistAlbum['artist_name'] }} - {{ $wishlistAlbum['album_name'] }} Album Cover" />
                        @endif

                    </td>
                    <td>{{ $wishlistAlbum['wishlist_album_format'] }}</td>
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
                        @if ($wishlistAlbum['music_store_url'])
                            <a href="{{ $wishlistAlbum['music_store_url'] }}"
                                target="_new">{{ $wishlistAlbum['music_store_name'] }}</a>
                        @else
                            {{ $wishlistAlbum['music_store_name'] }}
                        @endif
                    </td>
                    <td class="text-amber-500">{{ $wishlistAlbum['wishlist_album_notes'] }}</td>
                    <td>{{ $wishlistAlbum['wishlist_album_price_format'] }} </td>
                    <td>
                        @if ($wishlistAlbum['wishlist_album_price_price'])
                            &euro; {{ $wishlistAlbum['wishlist_album_price_price'] }}
                        @else
                            No price found
                        @endif
                    </td>

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
