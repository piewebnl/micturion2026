<?php

namespace App\Http\Controllers\SpotifyApi;

use App\Http\Controllers\Controller;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;

// Main spotify connect controller
class SpotifyApiController extends Controller
{
    protected $spotifyConnect;

    protected $api;

    public function __construct()
    {

        $this->api = (new SpotifyApiConnect)->getApi();
    }
}
