<?php

namespace App\Models\Wishlist;

use App\Models\Music\Album;
use App\Models\Music\Category;
use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;

class WishlistAlbumPrice extends Model
{
    use GlobalScopesTrait;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = [];

    public function musicStore()
    {
        return $this->belongsTo(MusicStore::class)->orderBy('name');
    }

    public function wishlistAlbum()
    {
        return $this->belongsTo(WishlistAlbum::class);
    }

    public function album()
    {
        return $this->belongsToThrough(Album::class, WishlistAlbum::class, 'id');
    }

    public function category()
    {
        return $this->belongsToThrough(Album::class, Category::class, 'id');
    }

    public function storeOrUpdate(WishlistAlbumPrice $wishlistAlbumPrice): WishlistAlbumPrice
    {
        return WishlistAlbumPrice::UpdateOrCreate(
            [
                'id' => $wishlistAlbumPrice->id,

            ],
            [
                'persistent_album_id' => $wishlistAlbumPrice->persistent_album_id,
                'music_store_id' => $wishlistAlbumPrice->music_store_id,
                'url' => $wishlistAlbumPrice->url,

            ]
        );
    }

    // NORMAL STORE
    public function storeFromScrapeResult(array $result)
    {
        return WishlistAlbumPrice::updateOrCreate(
            [
                'wishlist_album_id' => $result['wishlist_album']['id'],
                'music_store_id' => $result['music_store']['id'],
                'format' => $result['best_match']['format'],
            ],
            [
                'price' => $result['best_match']['price'] ?? null,
                'score' => $result['best_match']['score'] ?? null,
                'url' => $result['best_match']['page_url'] ?? null,
                'updated_at' => now(),
            ]
        );
    }
}
