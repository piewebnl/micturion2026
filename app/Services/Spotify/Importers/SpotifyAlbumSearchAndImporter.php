<?php

namespace App\Services\Spotify\Importers;

use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyAlbumUnavailable;
use App\Models\Spotify\SpotifySearchResultAlbum;
use App\Services\Spotify\Searchers\SpotifyAlbumCustomIdSearcher;
use App\Services\Spotify\Searchers\SpotifyAlbumSearcher;
use App\Services\Spotify\Searchers\SpotifyAlbumSearchPrepare;
use App\Traits\Converters\ToSpotifyAlbumConverter;
use Illuminate\Http\JsonResponse;

// Search for spotify album via api and then import to db
class SpotifyAlbumSearchAndImporter
{
    use ToSpotifyAlbumConverter;

    private $api;

    private $response;

    private $resource = [];

    private $spotifySearchResultAlbum; // Best found spotify album

    private $album;

    private $spotifySearchAlbum;

    public function __construct($api)
    {
        $this->api = $api;
    }

    // REWRITE
    public function import(Album $album)
    {
        $this->album = $album;

        $SpotifyAlbumSearchPrepare = new SpotifyAlbumSearchPrepare;
        $this->spotifySearchAlbum = $SpotifyAlbumSearchPrepare->prepareSpotifySearchAlbum($this->album);

        // Search unavailable in own DB first
        $this->searchUnavailable();

        if (!$this->spotifySearchResultAlbum) {

            // Search for customId in own DB first
            $this->searchCustomId();

            // Already in DB?
            $this->searchAlbumId();

            // Try Spotify API to find match (if not customId)
            if (!$this->spotifySearchResultAlbum->spotify_api_album_id) {
                // $this->searchSpotifyApi();
            }
        }

        // All good use the SpotifyAlbumImporter?
        $this->storeSpotifySearchResultAlbum();
    }

    private function storeSpotifySearchResultAlbum()
    {
        $spotifySearchResultAlbum = new SpotifySearchResultAlbum;
        $spotifyAlbum = $spotifySearchResultAlbum->store($this->spotifySearchResultAlbum);

        $this->resource = SpotifyAlbum::with('AlbumSpotifyAlbum.album.artist')->find($spotifyAlbum->id)->toArray();

        if ($this->resource['album_spotify_album']['status'] == 'success') {
            $this->response = response()->success('Spotify album found', $this->resource);

            return;
        }
        if ($this->resource['album_spotify_album']['status'] == 'warning') {
            $this->response = response()->warning('Spotify album found (low scoring)', $this->resource);

            return;
        }
        $this->response = response()->error('Spotify album found very low scoring', $this->resource);
    }

    private function searchUnavailable()
    {
        $found = SpotifyAlbumUnavailable::where('persistent_id', $this->spotifySearchAlbum['persistent_id'])->first();
        if ($found) {
            $this->spotifySearchResultAlbum = new SpotifySearchResultAlbum;
            $this->spotifySearchResultAlbum->fill([
                'spotify_api_track_id' => null,
                'name' => '',
                'artist' => '',
                'score' => 0,
                'status' => 'error',
                'search_name' => $found['name'],
                'search_artist' => $found['artist'],
                'album_id' => $this->album->id,
            ]);
        }
    }

    private function searchCustomId()
    {
        $SpotifyAlbumCustomIdSearcher = new SpotifyAlbumCustomIdSearcher($this->api);
        $SpotifyAlbumCustomIdSearcher->search($this->spotifySearchAlbum);
        $this->spotifySearchResultAlbum = $SpotifyAlbumCustomIdSearcher->getSpotifySearchResultAlbum();
    }

    private function searchAlbumId()
    {
        $SpotifyAlbumCustomIdSearcher = new SpotifyAlbumCustomIdSearcher($this->api);
        $SpotifyAlbumCustomIdSearcher->search($this->spotifySearchAlbum);
        $this->spotifySearchResultAlbum = $SpotifyAlbumCustomIdSearcher->getSpotifySearchResultAlbum();
    }

    private function searchSpotifyApi()
    {
        $spotifyAlbumSearcher = new SpotifyAlbumSearcher($this->api);
        $spotifyAlbumSearcher->search($this->spotifySearchAlbum);
        $this->spotifySearchResultAlbum = $spotifyAlbumSearcher->getSpotifySearchResultAlbum();
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
