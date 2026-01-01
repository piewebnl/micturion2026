<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Spotify\SpotifySearchAlbum;
use Illuminate\Http\JsonResponse;

// Search for spotify ids in table
class SpotifyAlbumIdSearcher
{
    private $api;

    private $spotifySearchResultAlbum;

    private $response;

    private $resource;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function search(SpotifySearchAlbum $spotifySearchAlbum)
    {

        //
        dd($spotifySearchAlbum);
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
