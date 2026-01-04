<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Music\Album;
use App\Dto\Spotify\SpotifySearchQuery;
use App\Models\Spotify\SpotifyAlbumCustomId;
use App\Models\Spotify\SpotifySearchResultAlbum;
use App\Services\Spotify\Importers\SpotifyAlbumImporter;

// Search for spotify custom ids in table and get the spotify album via its api
class SpotifyAlbumCustomIdSearcher
{
    private $api;


    public function __construct($api)
    {
        $this->api = $api;
    }

    public function search(SpotifySearchQuery $spotifySearchQuery)
    {
        $spotifyAlbumCustomId = SpotifyAlbumCustomId::where('persistent_id', $spotifySearchQuery->album_persistent_id)->first();

        if ($spotifyAlbumCustomId) {

            dd('custom!!!');
            $album = Album::where('persistent_id', $spotifySearchQuery->persistent_id)->first();

            if ($album) {

                $spotifyAlbumImporter = new SpotifyAlbumImporter($this->api);
                $spotifyAlbumImporter->import($spotifyAlbumCustomId['spotify_api_album_custom_id'], $album);

                // Mhaw NAAR CONVERTER
                $spotifySearchResultAlbum = new SpotifySearchResultAlbum;
                $spotifySearchResultAlbum->fill([
                    'spotify_api_album_id' => $spotifyAlbumCustomId['spotify_api_album_custom_id'],
                    'name' => $spotifyAlbumCustomId['name'],
                    'artist' => $spotifyAlbumCustomId['artists'],
                    'score' => 100,
                    'status' => 'success',
                    'search_name' => $spotifySearchQuery->name,
                    'search_artist' => $spotifySearchQuery->artist,
                    'album_id' => $spotifySearchQuery->album_id,
                ]);
                return  $spotifySearchResultAlbum;
            }
        }
        return null;
    }
}
