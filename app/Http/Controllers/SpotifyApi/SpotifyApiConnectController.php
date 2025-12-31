<?php

namespace App\Http\Controllers\SpotifyApi;

use App\Http\Controllers\Controller;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;

// Main spotify connect controller
class SpotifyApiConnectController extends Controller
{
    protected $spotifyConnect;

    protected $api;

    public function index()
    {

        $spotifyConnect = new SpotifyApiConnect;
        $spotifyConnect->getAuthorizeUrl();

        $authUrl = $spotifyConnect->getResponse()->original;

        // $this->api = (new SpotifyApiConnect())->getApi();
        return view('spotify.spotify-connect', ['authUrl' => $authUrl]);
    }
}
