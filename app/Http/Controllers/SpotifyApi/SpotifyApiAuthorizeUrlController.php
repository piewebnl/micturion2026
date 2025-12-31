<?php

namespace App\Http\Controllers\SpotifyApi;

use App\Http\Controllers\Controller;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;

// Get the url to authorize to spotify api
class SpotifyApiAuthorizeUrlController extends Controller
{
    public function index()
    {
        $spotifyConnect = new SpotifyApiConnect;
        $spotifyConnect->getAuthorizeUrl();

        return $spotifyConnect->getResponse();
    }
}
