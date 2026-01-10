<?php

namespace App\Services\SpotifyApi\Posters;

// post spotify playlist tracks via api
class SpotifyApiPlaylistTracksPoster
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    }
