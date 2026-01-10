<?php

namespace App\Models\Wishlist;

use App\Models\Music\Album;
use App\Models\Music\Artist;
use App\Scopes\GlobalScopesTrait;
use App\Scopes\WishlistAlbumScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class WishlistAlbum extends Model
{
    use GlobalScopesTrait;
    use WishlistAlbumScopesTrait;
    use QueryCache;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = [];

    public function album()
    {
        return $this->belongsTo(Album::class, 'persistent_album_id', 'persistent_id');
    }

    public function artist()
    {
        return $this->belongsToThrough(Artist::class, Album::class);
    }

    public function wishlistAlbumPrices()
    {
        return $this->hasMany(WishlistAlbumPrice::class);
    }

    public function storeOrUpdate(WishlistAlbum $wishlistAlbum): WishlistAlbum
    {
        return WishlistAlbum::UpdateOrCreate(
            [
                'id' => $wishlistAlbum->id,

            ],
            [
                'persistent_album_id' => $wishlistAlbum->persistent_album_id,
                'notes' => $wishlistAlbum->notes,
                'formats' => $wishlistAlbum->formats,
            ]
        );
    }

    public function getTotalWishlistAlbums(array $filterValues): int
    {
        $filterValues['page'] = null;
        $allWishlistAlbums = $this->getAllWishlistAlbums($filterValues);

        return count($allWishlistAlbums);
    }

    public function getWishlistAlbum(int $id, array $filterValues = [])
    {
        $filterValues['id'] = $id;

        return $this->getWishlistAlbumsWithPrices($filterValues)->first();
    }

    public function getWishlistAlbumsWithPrices(array $filterValues, $skipGetCache = false)
    {

        $wishlistAlbumsWithPrices = [];

        if (!$skipGetCache) {
            $wishlistAlbumsWithPrices = $this->getCache('get-wishlist-albums-with-prices', $filterValues);
        }

        if (!$wishlistAlbumsWithPrices) {

            $wishlistAlbumsWithPrices = WishlistAlbum::selectRaw('
        wishlist_albums.id as wishlist_album_id,
        wishlist_albums.persistent_album_id as wishlist_album_persistent_album_id,
        wishlist_albums.notes as wishlist_album_notes,
        wishlist_albums.formats as wishlist_album_formats,
        wishlist_album_prices.id as wishlist_album_price_id,
        wishlist_album_prices.price as wishlist_album_price_price,
        wishlist_album_prices.score as wishlist_album_price_score,
        wishlist_album_prices.format as wishlist_album_price_format,
        wishlist_album_prices.url as wishlist_album_price_url,
        wishlist_album_prices.updated_at as wishlist_album_price_updated_at,
        albums.name as album_name,
        albums.sort_name as album_sort_name,
        albums.year as album_year,
        album_images.id as album_image_id,
        album_images.slug as album_image_slug,
        album_images.hash as album_image_hash,
        album_images.largest_width as album_image_largest_width,
        artists.name as artist_name,
        artists.sort_name as artist_sort_name,
        music_stores.name as music_store_name,
        music_stores.url as music_store_url
       ')

                ->joinRelationship('wishlistAlbumPrices')
                ->joinRelationship('album')
                ->joinRelationship('album.albumImage')
                ->joinRelationship('album.artist')
                ->joinRelationship('wishlistAlbumPrices.musicStore')
                ->whereId($filterValues, 'album_id', 'wishlist_album')
                ->whereId($filterValues, 'music_store_id', 'music_store')
                ->wishlistAlbumWhereFormat($filterValues)
                ->wishlistAlbumShowLowScores($filterValues)
                ->wishlistAlbumWhereKeyword($filterValues)
                ->wishlistAlbumSortAndOrderBy($filterValues)
                ->customPaginateOrLimit($filterValues);

            $this->setCache('get-wishlist-albums-with-prices', $filterValues, $wishlistAlbumsWithPrices);
        }

        return $wishlistAlbumsWithPrices;
    }

    public function getWishlistAlbums($filterValues)
    {

        $wishlistAlbums = WishlistAlbum::selectRaw('
            wishlist_albums.id as wishlist_album_id,
            wishlist_albums.persistent_album_id as  wishlist_album_persistent_album_id,
            wishlist_albums.notes as wishlist_album_notes,
            wishlist_albums.format as wishlist_album_format,
            albums.id as album_id,
            albums.name as album_name,
            artists.name as artist_name,
            artists.sort_name as artist_sort_name')
            ->joinRelationship('album')
            ->joinRelationship('album.artist')->orderBy('artist_sort_name', 'asc')
            ->customPaginateOrLimit($filterValues);

        return $wishlistAlbums;
    }

    public function getAllWishlistAlbums()
    {
        $wishlistAlbums = $this->getCache('get-all-wishlist-albums');

        if (!$wishlistAlbums) {
            $wishlistAlbums = WishlistAlbum::selectRaw('
            albums.id as album_id,
            albums.name as album_name,
            artists.name as artist_name,
            artists.sort_name as artist_sort_name')
                ->joinRelationship('album')
                ->joinRelationship('album.artist')->orderBy('artist_sort_name', 'asc')->get();

            $this->setCache('get-all-wishlist-albums', [], $wishlistAlbums);
        }

        return $wishlistAlbums;
    }
}
