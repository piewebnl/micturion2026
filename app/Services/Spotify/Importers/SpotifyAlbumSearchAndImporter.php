<?php

namespace App\Services\Spotify\Importers;

use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Dto\Spotify\SpotifySearchQuery;
use App\Models\AlbumSpotifyAlbum\AlbumSpotifyAlbum;
use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyAlbumUnavailable;
use App\Services\Logger\Logger;
use App\Services\Spotify\Searchers\spotifyAlbumCustomIdSearcher;
use App\Services\Spotify\Searchers\SpotifyAlbumSearcher;
use App\Services\Spotify\Searchers\SpotifyAlbumSearchPrepare;
use Illuminate\Http\JsonResponse;

// Search for spotify album via api and then import to db
class SpotifyAlbumSearchAndImporter
{
    private $api;

    private $response;

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

        $found = false;

        if ($this->spotifySearchAlbumResult?->status == 'unavailable') {
            $found = true;
        }

        if (!$found) {
            // Search for customId in own DB first
            $spotifyAlbumCustomIdSearcher = new spotifyAlbumCustomIdSearcher($this->api);
            $this->spotifySearchAlbumResult = $spotifyAlbumCustomIdSearcher->search($this->spotifySearchQuery);
            if ($this->spotifySearchAlbumResult?->status == 'custom') {
                $found = true;
            }
        }

        /*
        if (!$this->spotifySearchAlbumResult) {
            // Already in DB?
            $this->searchAlbumId();
        }
        */

        // Try Spotify API to find match (if not customId)
        if (!$found) {
            $spotifyAlbumSearcher = new SpotifyAlbumSearcher($this->api);
            $this->spotifySearchAlbumResult = $spotifyAlbumSearcher->search($this->spotifySearchQuery);
            if ($this->spotifySearchAlbumResult->status) {
                $found = true;
            }
        }

        if (!$found) {
            dd('nothing found');
        }

        $spotifyAlbumModel = new SpotifyAlbum;
        $spotifyAlbum = $spotifyAlbumModel->storeFromSpotifySearchResultAlbum($this->spotifySearchAlbumResult);

        $albumSpotifyAlbum = new AlbumSpotifyAlbum;
        $albumSpotifyAlbum->storeFromSpotifySearchResultAlbum($this->spotifySearchAlbumResult, $spotifyAlbum);

        $loggerText = 'Not found';
        $loggerStatus = 'error';
        if ($this->spotifySearchAlbumResult->status == 'success') {
            $loggerText = 'Imported';
            $loggerStatus = 'notice';
        }
        if ($this->spotifySearchAlbumResult->status == 'warning') {
            $loggerText = 'Imported, but check';
            $loggerStatus = 'warning';
        }
        Logger::log(
            $loggerStatus,
            $this->channel,
            'Spotify album ' . $loggerText . ': ' . $this->spotifySearchQuery->artist . ' ' . $this->spotifySearchQuery->album,
            [
                ['Score: ' . $this->spotifySearchAlbumResult->score],
                ['Found: ' . $this->spotifySearchAlbumResult->artist . ' - ' . $this->spotifySearchAlbumResult->name],
                ['spotifySearchAlbumResult' => $this->spotifySearchAlbumResult],
            ]

        );
    }

    private function searchUnavailable()
    {
        $found = SpotifyAlbumUnavailable::where('persistent_id', $this->spotifySearchQuery->album_persistent_id)->first();

        if ($found) {

            $this->spotifySearchAlbumResult = new SpotifySearchAlbumResult(
                spotify_api_album_id: null,
                name: 'NOT FOUND',
                name_sanitized: null,
                artist: 'NOT FOUND',
                artist_sanitized: null,
                score: 0,
                score_breakdown: [],
                status: 'unavailable',
                search_name: $found['name'],
                search_artist: $found['artist'],
                year: null,
                album_id: $this->album->id,
                artwork_url: null,
                all_results: null
            );
        }
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }
}
