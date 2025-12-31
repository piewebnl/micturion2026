<?php

namespace App\Http\Controllers\Api\Spotify;

use App\Http\Controllers\Controller;
use App\Models\Spotify\SpotifyTrackUnavailable;
use Illuminate\Http\JsonResponse;

class ApiSpotifyTracksUnavailableController extends Controller
{
    public function index(): JsonResponse
    {
        $spotifyTracksUnavailable = SpotifyTrackUnavailable::all();

        return response()->json($spotifyTracksUnavailable);
    }
}
