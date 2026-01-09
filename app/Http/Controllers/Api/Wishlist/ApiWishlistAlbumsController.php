<?php

namespace App\Http\Controllers\Api\Wishlist;

use Illuminate\Http\Request;
use App\Services\Logger\Logger;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Wishlist\WishlistAlbum;
use App\Models\Wishlist\WishlistAlbumPrice;

class ApiWishlistAlbumsController extends Controller
{

    private string $channel = 'api_store_wishlist_album_prices';


    public function index(): JsonResponse
    {
        $wishlistAlbums = WishlistAlbum::with(['album.artist'])->get();

        return response()->json($wishlistAlbums);
    }


    public function store(Request $request)
    {
        $scrapeResult = $request->input('scrape_result', $request->all());

        Logger::log('info', $this->channel, 'Stored', ['scrape_result' => $scrapeResult]);

        $wishlistAlbumPrice = new WishlistAlbumPrice();
        $wishlistAlbumPrice->storeFromScrapeResult($scrapeResult);

        return response()->json([
            'status'    => 'ok'
        ]);
    }
}
