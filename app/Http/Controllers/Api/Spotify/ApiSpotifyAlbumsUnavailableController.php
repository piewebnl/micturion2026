<?php

namespace App\Http\Controllers\Api\Spotify;

use App\Http\Controllers\Controller;
use App\Models\Spotify\SpotifyAlbumUnavailable;
use Illuminate\Http\JsonResponse;

class ApiSpotifyAlbumsUnavailableController extends Controller
{
    public function index(): JsonResponse
    {
        $spotifyAlbumsUnavailable = SpotifyAlbumUnavailable::all();

        return response()->json($spotifyAlbumsUnavailable);
    }
}
