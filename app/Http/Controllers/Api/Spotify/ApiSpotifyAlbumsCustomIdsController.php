<?php

namespace App\Http\Controllers\Api\Spotify;

use App\Http\Controllers\Controller;
use App\Models\Spotify\SpotifyAlbumCustomId;
use Illuminate\Http\JsonResponse;

class ApiSpotifyAlbumsCustomIdsController extends Controller
{
    public function index(): JsonResponse
    {
        $spotifyAlbumsCustomIds = SpotifyAlbumCustomId::all();

        return response()->json($spotifyAlbumsCustomIds);
    }
}
