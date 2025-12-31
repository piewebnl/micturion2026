<?php

namespace App\Http\Controllers\Api\Spotify;

use App\Http\Controllers\Controller;
use App\Models\Spotify\SpotifyTrackCustomId;
use Illuminate\Http\JsonResponse;

class ApiSpotifyTracksCustomIdsController extends Controller
{
    public function index(): JsonResponse
    {
        $spotifyTracksCustomIds = SpotifyTrackCustomId::all();

        return response()->json($spotifyTracksCustomIds);
    }
}
