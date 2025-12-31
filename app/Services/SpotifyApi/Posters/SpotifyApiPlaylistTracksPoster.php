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

    public function postPerPage(string $spotifyPlaylistId, array $trackIds, int $page)
    {
        // First batch (replace)
        if ($page == 1) {
            $this->api->replacePlaylistTracks($spotifyPlaylistId, $trackIds);

            return;
        }

        $this->api->addPlaylistTracks($spotifyPlaylistId, $trackIds);
    }
}
