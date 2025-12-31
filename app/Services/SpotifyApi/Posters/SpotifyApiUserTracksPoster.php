<?php

namespace App\Services\SpotifyApi\Posters;

// post spotify tracks (favourite songs) via api
class SpotifyApiUserTracksPoster
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function post(array $trackIds)
    {
        $this->api->addMyTracks($trackIds);
    }
}
