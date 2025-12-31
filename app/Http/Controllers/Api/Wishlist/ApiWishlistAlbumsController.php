<?php

namespace App\Http\Controllers\Api\Wishlist;

use App\Http\Controllers\Controller;
use App\Models\Wishlist\WishlistAlbum;
use Illuminate\Http\JsonResponse;

class ApiWishlistAlbumsController extends Controller
{
    public function index(): JsonResponse
    {
        $wishlistAlbums = WishlistAlbum::all();

        return response()->json($wishlistAlbums);
    }
}
