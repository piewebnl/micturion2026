<?php

namespace App\Services\SpotifyApi\Getters;

// get playback info from user
class SpotifyApiNowPlaying
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function get()
    {
        return $this->api->getMyCurrentPlaybackInfo();
    }
}
