<?php

namespace App\Services\Spotify\Creators;

use App\Models\SpotifyApi\SpotifyApiPlaylist;
use App\Services\SpotifyApi\Getters\SpotifyApiUserPlaylistGetter;

// Find (if not exists create) playlist on spotify and create in db
class SpotifyPlaylistCreator
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function create(string $name)
    {

        $spotifyApiUserPlaylistGetter = new SpotifyApiUserPlaylistGetter($this->api, 50);
        $foundPlaylist = $spotifyApiUserPlaylistGetter->getByName($name);

        if (!$foundPlaylist) {
            $foundPlaylist = $this->api->createPlaylist(env('SPOTIFY_USER_ID'), [
                'name' => $name,
            ]);
        }

        $foundPlaylist->snapshot_id_has_changed = true;
        $spotifyApiPlaylist = new SpotifyApiPlaylist;
        $spotifyApiPlaylist->updateOrCreateSingle($foundPlaylist);
    }
}
