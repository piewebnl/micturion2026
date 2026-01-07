<?php

namespace App\Services\Spotify\Importers;

use App\Models\Music\Song;
use App\Services\Logger\Logger;
use Illuminate\Http\JsonResponse;
use App\Models\Spotify\SpotifyTrack;
use App\Dto\Spotify\SpotifySearchTrackQuery;
use App\Dto\Spotify\SpotifySearchTrackResult;
use App\Models\Spotify\SpotifyTrackUnavailable;
use App\Services\Spotify\Searchers\SpotifyTrackSearcher;
use App\Services\Spotify\Searchers\SpotifyTrackSearchPrepare;
use App\Services\Spotify\Searchers\SpotifyTrackCustomIdSearcher;

// Search for spotify track via unavailable, custom ID in DB or spotify api and then import to db
class SpotifyTrackSearchAndImporter
{

    private $api;

    private $response;

    private ?SpotifySearchTrackResult $spotifySearchTrackResult = null; // Best found spotify song

    private SpotifySearchTrackQuery $spotifySearchQuery;

    private Song $song;


    private $channel = 'spotify_search_and_import_tracks';

    public function __construct($api)
    {
        $this->api = $api;
    }


    public function import(Song $song)
    {
        $this->song = $song;


        $spotifyTrackSearchPrepare = new SpotifyTrackSearchPrepare;
        $this->spotifySearchQuery = $spotifyTrackSearchPrepare->prepareSpotifySearchTrack($this->song);

        // Search unavailable in own DB first
        $this->searchUnavailable();



        $found = false;

        if ($this->spotifySearchTrackResult?->status == 'unavailable') {
            $found = true;
        }

        if (!$found) {
            // Search for customId in own DB first
            /*
            $spotifyTrackCustomIdSearcher = new SpotifyTrackCustomIdSearcher($this->api);
            $this->spotifySearchTrackResult = $spotifyTrackCustomIdSearcher->search($this->spotifySearchQuery);
            if ($this->spotifySearchTrackResult?->status == 'custom') {
                $found = true;
            }
                */
        }


        // Try Spotify API to find match (if not customId)
        if (!$found) {
            $spotifyTrackSearcher = new SpotifyTrackSearcher($this->api);
            $this->spotifySearchTrackResult = $spotifyTrackSearcher->search($this->spotifySearchQuery);
            if ($this->spotifySearchTrackResult->status) {
                $found = true;
            }
        }

        if (!$found) {
            dd('nothing found');
        }

        $spotifyTrackModel = new SpotifyTrack;
        $spotifyTrackModel->storeFromSpotifySearchResultTrack($this->spotifySearchTrackResult, $this->song);


        $loggerText = 'Not found';
        $loggerStatus = 'error';
        if ($this->spotifySearchTrackResult->status == 'success') {
            $loggerText = 'Imported';
            $loggerStatus = 'notice';
        }
        if ($this->spotifySearchTrackResult->status == 'warning') {
            $loggerText = 'Imported, but check';
            $loggerStatus = 'warning';
        }
        Logger::log(
            $loggerStatus,
            $this->channel,
            'Spotify track ' . $loggerText . ': ' . $this->spotifySearchQuery->artist . ' - ' . $this->spotifySearchQuery->album . ' - ' . $this->spotifySearchQuery->name,
            [
                ['Score: ' . $this->spotifySearchTrackResult->score],
                ['Found: ' . $this->spotifySearchTrackResult->artist . ' - ' . $this->spotifySearchTrackResult->album . ' - ' . $this->spotifySearchTrackResult->name],
                ['spotifySearchTrackResult' => $this->spotifySearchTrackResult],
            ]

        );
    }

    // TO MODEL?
    private function searchUnavailable()
    {
        $found = SpotifyTrackUnavailable::where('persistent_id', $this->spotifySearchQuery->song_persistent_id)->first();

        if ($found) {

            $this->spotifySearchTrackResult = new SpotifySearchTrackResult(
                spotify_api_track_id: null,
                spotify_api_album_id: null,
                name: 'NOT FOUND',
                album: $this->spotifySearchQuery->album ?? '',
                artist: 'NOT FOUND',
                score: 0,
                score_breakdown: [],
                status: 'unavailable',
                search_name: $found['name'],
                search_album: $this->spotifySearchQuery->album ?? '',
                search_artist: $found['artist'],
                year: null,
                song_id: $this->song->id,
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
