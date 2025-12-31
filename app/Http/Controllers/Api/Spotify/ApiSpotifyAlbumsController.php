<?php

namespace App\Http\Controllers\Api\Spotify;

use App\Http\Controllers\Controller;
use App\Models\Spotify\SpotifyAlbumCustomId;
use Illuminate\Http\JsonResponse;

class ApiSpotifyAlbumsController extends Controller
{
    public function index(): JsonResponse
    {
        $spotifyAlbums = SpotifyAlbumCustomId::all();

        return response()->json($spotifyAlbums);
    }
}
