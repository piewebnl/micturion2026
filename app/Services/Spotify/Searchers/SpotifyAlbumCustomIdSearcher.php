<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbumCustomId;
use App\Models\Spotify\SpotifySearchAlbum;
use App\Models\Spotify\SpotifySearchResultAlbum;
use App\Services\Spotify\Importers\SpotifyAlbumImporter;
use Illuminate\Http\JsonResponse;

// Search for spotify custom ids in table and get the spotify album via its api
class SpotifyAlbumCustomIdSearcher
{
    private $api;

    private $spotifySearchResultAlbum;

    private // $response;

    private $resource;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function search(SpotifySearchAlbum $spotifySearchAlbum)
    {
        $spotifyAlbumCustomId = SpotifyAlbumCustomId::where('persistent_id', $spotifySearchAlbum['persistent_id'])->first();

        $album = Album::where('persistent_id', $spotifySearchAlbum['persistent_id'])->first();

        if ($spotifyAlbumCustomId && $album) {

            $spotifyAlbumImporter = new SpotifyAlbumImporter($this->api);
            $spotifyAlbumImporter->import($spotifyAlbumCustomId['spotify_api_album_custom_id'], $album);

            // Mhaw NAAR CONVERTER
            $this->spotifySearchResultAlbum = new SpotifySearchResultAlbum;
            $this->spotifySearchResultAlbum->fill([
                'spotify_api_album_id' => $spotifyAlbumCustomId['spotify_api_album_custom_id'],
                'name' => $spotifyAlbumCustomId['name'],
                'artist' => $spotifyAlbumCustomId['artists'],
                'score' => 100,
                'status' => 'success',
                'search_name' => $spotifySearchAlbum['name'],
                'search_artist' => $spotifySearchAlbum['artist'],
                'album_id' => $spotifySearchAlbum['album_id'],
            ]);
            $this->resource = $this->spotifySearchResultAlbum;
            $this->response = response()->error('Spotify search result NOT FOUND', $this->resource->toArray());

            return;
        }

        // Empty result
        $this->spotifySearchResultAlbum = new SpotifySearchResultAlbum;
        $this->resource = $this->spotifySearchResultAlbum;

        $this->response = response()->success('Spotify search result ', $this->resource->toArray());
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }

    public function getSpotifySearchResultAlbum(): SpotifySearchResultAlbum
    {
        return $this->spotifySearchResultAlbum;
    }
}
