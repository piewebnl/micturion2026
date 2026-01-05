<?php

namespace App\Services\Spotify\Importers;

use App\Models\Music\Album;
use App\Services\Logger\Logger;
use Illuminate\Http\JsonResponse;
use App\Models\Spotify\SpotifyAlbum;
use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Dto\Spotify\SpotifySearchQuery;
use App\Models\AlbumSpotifyAlbum\AlbumSpotifyAlbum;
use App\Models\Spotify\SpotifyAlbumUnavailable;
use App\Models\Spotify\SpotifySearchResultAlbum;
use App\Traits\Converters\ToSpotifyAlbumConverter;
use App\Services\Spotify\Searchers\SpotifyAlbumSearcher;
use App\Services\Spotify\Searchers\SpotifyAlbumSearchPrepare;
use App\Services\Spotify\Searchers\spotifyAlbumCustomIdSearcher;

// Search for spotify album via api and then import to db
class SpotifyAlbumSearchAndImporter
{
    use ToSpotifyAlbumConverter;

    private $api;

    private $response;

    private $resource = [];

    private ?SpotifySearchAlbumResult $spotifySearchAlbumResult = null; // Best found spotify album

    private SpotifySearchQuery $spotifySearchQuery;

    private Album $album;

    private $channel = 'spotify_search_and_import_albums';

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function import(Album $album)
    {
        $this->album = $album;

        $spotifyAlbumSearchPrepare = new SpotifyAlbumSearchPrepare;
        $this->spotifySearchQuery = $spotifyAlbumSearchPrepare->prepareSpotifySearchAlbum($this->album);

        // Search unavailable in own DB first
        $this->searchUnavailable();

        if ($this->spotifySearchAlbumResult?->source == 'unavailabe') {
            Logger::log(
                'warning',
                $this->channel,
                'CUSTOM ID Save to dB? ' . $this->album->name
            );
            return;
        }

        if (!$this->spotifySearchAlbumResult) {
            // Search for customId in own DB first
            //$spotifyAlbumCustomIdSearcher = new spotifyAlbumCustomIdSearcher($this->api);
            //$this->spotifySearchAlbumResult = $spotifyAlbumCustomIdSearcher->search($this->spotifySearchQuery);
        }
        /*
        if (!$this->spotifySearchAlbumResult) {
            // Already in DB?
            $this->searchAlbumId();
        }
            */


        // Try Spotify API to find match (if not customId)
        if (!$this->spotifySearchAlbumResult) {
            $this->spotifySearchAlbumResult = $this->searchSpotifyApi();
        }


        $spotifyAlbum = new SpotifyAlbum();
        $spotifyAlbumResult = $spotifyAlbum->storeFromSpotifySearchResultAlbum($this->spotifySearchAlbumResult);

        $albumSpotifyAlbum = new AlbumSpotifyAlbum();
        $albumSpotifyAlbum->storeFromSpotifySearchResultAlbum($this->spotifySearchAlbumResult, $spotifyAlbumResult);

        Logger::log(
            'notice',
            $this->channel,
            'Spotify album found and imported:<br/>Searched for: ' .  $this->spotifySearchAlbumResult->score . ': ' . $this->spotifySearchQuery->artist . ' ' . $this->spotifySearchQuery->album . "<br/>Result:" . $this->spotifySearchAlbumResult->artist . ' - ' . $this->spotifySearchAlbumResult->name,
            ['spotifySearchAlbumResult' => $this->spotifySearchAlbumResult]

        );
    }



    private function searchUnavailable()
    {
        $found = SpotifyAlbumUnavailable::where('persistent_id', $this->spotifySearchQuery->album_persistent_id)->first();

        if ($found) {

            $this->spotifySearchAlbumResult = new SpotifySearchAlbumResult(
                spotify_api_album_id: null,
                name: '',
                name_sanitized: null,
                artist: '',
                artist_sanitized: null,
                score: 0,
                status: 'error',
                search_name: $found['name'],
                search_artist: $found['artist'],
                album_id: $this->album->id,
                source: 'unavailabe',
                all_results: null
            );
        }
    }


    private function searchSpotifyApi()
    {
        $spotifyAlbumSearcher = new SpotifyAlbumSearcher($this->api);
        return $spotifyAlbumSearcher->search($this->spotifySearchQuery);
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
