<?php

namespace App\Http\Controllers\Spotify;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Spotify\SpotifyMonthlyPlaylistSearchFormData;

class SpotifyMonthlyPlaylistController extends Controller
{
    public function index()
    {
        $spotifyMonthlyPlaylistSearchFormData = new SpotifyMonthlyPlaylistSearchFormData;
        $searchFormData = $spotifyMonthlyPlaylistSearchFormData->generate();

        return view('spotify.spotify-monthly-playlist', ['searchFormData' => $searchFormData]);
    }
}
