<?php

namespace App\Services\SpotifyApi\Getters;

// get a single spotify album from user library
class SpotifyApiAlbumGetter
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function get(string $spotifyAlbumId)
    {
        return $this->api->getAlbum($spotifyAlbumId);
    }
}
