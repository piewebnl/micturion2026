<?php

namespace App\Services\SpotifyApi\Getters;

// get spotify track directly via api
class SpotifyApiTrackGetter
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function get(string $spotifyTrackId)
    {
        return $this->api->getTrack($spotifyTrackId);
    }
}
