<?php

namespace App\Jobs\Wishlist;

use App\Models\Wishlist\MusicStore;
use App\Models\Wishlist\WishlistAlbum;
use App\Services\Wishlist\MusicStoreScraper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class WishlistAlbumPricesScrapeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    private int $page = 1;

    private int $perPage = 1;

    private JsonResponse $response;

    public function __construct(int $page, int $perPage)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function handle()
    {

        // Scrape per page...each page is 1 album, 1 store
        $wishlistAlbumModel = new WishlistAlbum;

        $wishlistAlbum = $wishlistAlbumModel->getWishlistAlbums(
            [
                'page' => $this->page,
                'per_page' => $this->perPage,
            ]
        )->first();

        $musicStores = MusicStore::all();
        foreach ($musicStores as $musicStore) {

            if (App::environment() == 'local' or !$musicStore->local_scrape) {
                // if ($musicStore['id'] == 10) {
                $s = new MusicStoreScraper($musicStore, $wishlistAlbum);
                if ($wishlistAlbum->wishlist_album_format == '' or $wishlistAlbum->wishlist_album_format == 'LP') {
                    $s->scrape('lp');
                }
                if ($wishlistAlbum->wishlist_album_format == '' or $wishlistAlbum->wishlist_album_format == 'CD') {
                    $s->scrape('cd');
                }
                // }
            }
        }
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
