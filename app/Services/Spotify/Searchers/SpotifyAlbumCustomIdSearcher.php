<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchAlbumQuery;
use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbumCustomId;
use App\Services\Spotify\Importers\SpotifyAlbumImporter;

// Search for spotify custom ids in table and get the spotify album via its api
class SpotifyAlbumCustomIdSearcher
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function search(SpotifySearchAlbumQuery $spotifySearchQuery): ?SpotifySearchAlbumResult
    {
        $spotifyAlbumCustomId = SpotifyAlbumCustomId::where('persistent_id', $spotifySearchQuery->album_persistent_id)->first();

        if ($spotifyAlbumCustomId) {

            $album = Album::where('persistent_id', $spotifySearchQuery->album_persistent_id)->first();

            $spotifyAlbumImporter = new SpotifyAlbumImporter($this->api);

            return $spotifyAlbumImporter->import($spotifyAlbumCustomId['spotify_api_album_custom_id'], $album);
            // Save it to Custom Ids (not needer here)
            /*
            $spotifyAlbumCustomIdModel = new SpotifyAlbumCustomId;
            $spotifyAlbumCustomIdModel->storeFromSpotifyApiAlbum($spotifyApiAlbum, $album);
            */
        }

        return null;
    }
}
