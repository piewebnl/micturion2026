<?php

namespace App\Services\SpotifyApi\Posters;

// post spotify albums via api
class SpotifyApiUserAlbumsPoster
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function post(array $albumIds)
    {
        return $this->api->addMyAlbums($albumIds);
    }
}
