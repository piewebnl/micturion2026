<?php

namespace App\Http\Controllers\SpotifyApi;

use App\Http\Controllers\Controller;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;

// When authorized at spotify api, redirect to this
class SpotifyApiAuthorizeCallbackController extends Controller
{
    public function index()
    {
        $spotifyConnect = new SpotifyApiConnect;
        $spotifyConnect->callback();

        return $spotifyConnect->getResponse();
    }
}
