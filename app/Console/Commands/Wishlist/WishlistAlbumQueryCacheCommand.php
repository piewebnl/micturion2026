<?php

namespace App\Console\Commands\Wishlist;

use App\Models\Wishlist\WishlistAlbum;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;

// php artisan command:WishlistAlbumQueryCache
class WishlistAlbumQueryCacheCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:WishlistAlbumQueryCache';

    private string $channel;

    private array $config;

    private WishlistAlbum $wishlistAlbum;

    public function handle()
    {

        // $this->clearCache('get-wishlist-albums-with-prices');

        $this->wishlistAlbum = new WishlistAlbum;

        $filterValues = [
            'page' => 1,
            'view' => 'grid',
            'sort' => 'artist_sort_name',
            'order' => 'asc',
            'per_page' => 50,
            'keyword' => null,
            'wishlist_album' => null,
            'music_store' => null,
            'show_low_scores' => false,
        ];
        $this->makeQueries($filterValues);

        $filterValues = [
            'page' => 1,
            'view' => 'grid',
            'sort' => 'artist_sort_name',
            'order' => 'desc',
            'per_page' => 50,
            'keyword' => null,
            'wishlist_album' => null,
            'music_store' => null,
            'show_low_scores' => false,
        ];
        $this->makeQueries($filterValues);
    }

    private function makeQueries($filterValues)
    {
        $wishlistAlbums = $this->wishlistAlbum->getWishlistAlbumsWithPrices($filterValues, true);
        $lastPage = $wishlistAlbums->lastPage();
        $perPage = $filterValues['per_page'];
        for ($page = 2; $page <= $lastPage; $page++) {
            $filterValues['per_page'] = $filterValues['per_page'] + $perPage;
            $wishlistAlbums = $this->wishlistAlbum->getWishlistAlbumsWithPrices($filterValues, true);
        }
    }
}
