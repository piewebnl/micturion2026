<?php

namespace App\Services\Spotify\Importers;

use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Dto\Spotify\SpotifySearchAlbumQuery;
use App\Models\AlbumSpotifyAlbum\AlbumSpotifyAlbum;
use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbum;
use App\Services\SpotifyApi\Getters\SpotifyApiAlbumGetter;

// Import a spotify album by a given spotify album id and a album
class SpotifyAlbumImporter
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function import(string $spotifyApiAlbumId, Album $album): ?SpotifySearchAlbumResult
    {

        // Get spotify album via api directly
        $spotifyAlbumsGetter = new SpotifyApiAlbumGetter($this->api);
        $spotifyApiAlbum = $spotifyAlbumsGetter->get($spotifyApiAlbumId);

        if (!$spotifyApiAlbum) {
            return null;
        }

        $spotifySearchQuery = SpotifySearchAlbumQuery::fromAlbum($album);

        $spotifyApiAlbum->status = 'custom';
        $spotifyApiAlbum->score = 100;

        $spotifySearchAlbumResult = SpotifySearchAlbumResult::fromSpotifyApiAlbum(
            $spotifyApiAlbum,
            $spotifySearchQuery
        );

        // Store to album and the relation
        $spotifyAlbumModel = new SpotifyAlbum;
        $spotifyAlbum = $spotifyAlbumModel->storeFromSpotifySearchResultAlbum($spotifySearchAlbumResult, $album);


        return $spotifySearchAlbumResult;
    }
}
