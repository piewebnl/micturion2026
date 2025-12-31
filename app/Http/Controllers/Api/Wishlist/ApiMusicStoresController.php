<?php

namespace App\Http\Controllers\Api\Wishlist;

use App\Http\Controllers\Controller;
use App\Models\Wishlist\MusicStore;
use Illuminate\Http\JsonResponse;

class ApiMusicStoresController extends Controller
{
    public function index(): JsonResponse
    {
        $musicStores = MusicStore::all();

        return response()->json($musicStores);
    }
}
