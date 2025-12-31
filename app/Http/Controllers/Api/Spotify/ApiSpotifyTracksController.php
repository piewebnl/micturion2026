<?php

namespace App\Http\Controllers\Api\Spotify;

use App\Http\Controllers\Controller;
use App\Models\Spotify\SpotifyTrack;
use Illuminate\Http\JsonResponse;

class ApiSpotifyTracksController extends Controller
{
    public function index(): JsonResponse
    {
        $spotifyTracks = SpotifyTrack::all();

        return response()->json($spotifyTracks);
    }
}
