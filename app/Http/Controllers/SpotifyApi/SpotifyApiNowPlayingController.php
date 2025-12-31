<?php

namespace App\Http\Controllers\SpotifyApi;

use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\SpotifyApi\Getters\SpotifyApiNowPlaying;

// Get Spotify Now playing data
class SpotifyApiNowPlayingController extends SpotifyApiController
{
    public function index()
    {
        $spotifyConnect = new SpotifyApiConnect;
        $api = $spotifyConnect->getApi();

        $currentTrack = null;
        if ($api) {
            $spotifyNowPlaying = new SpotifyApiNowPlaying($api);
            $currentTrack = $spotifyNowPlaying->get();
        }

        return view('spotify.spotify-now-playing', ['currentTrack' => $currentTrack]);
    }
}
