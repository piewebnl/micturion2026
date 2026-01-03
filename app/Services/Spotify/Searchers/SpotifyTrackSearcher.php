<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Spotify\SpotifySearchResultTrack;
use App\Models\Spotify\SpotifySearchTrack;
use App\Services\Spotify\Helpers\SpotifyNameHelper;
use App\Traits\Converters\ToSpotifySearchResultTrackConverter;
use App\Services\Logger\Logger;
use Exception;

// Search spotify api for tracks
class SpotifyTrackSearcher
{
    use ToSpotifySearchResultTrackConverter;

    private $api;

    private SpotifyNameHelper $spotifyNameHelper;

    private $result = []; // actual result meeting score requirement

    private $spotifySearchResultTrack;

    private $allResults = []; // All allResults from spotify search

    private $resource;

    private $spotifySearchString = '';

    private $highestScore = 0;

    private $successScore;

    private $warningScore;

    public function __construct($api)
    {
        $this->api = $api;
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function search(SpotifySearchTrack $spotifySearchTrack)
    {
        $this->searchByName($spotifySearchTrack);
        $this->resource = $this->spotifySearchResultTrack;
    }

    private function searchByName(SpotifySearchTrack $spotifySearchTrack)
    {

        $this->spotifySearchString = $spotifySearchTrack['artist'] . ' ' . $spotifySearchTrack['name'];

        try {
            $spotifyResults = $this->api->search($this->spotifySearchString, 'track', ['limit' => 10, 'market' => 'NL']);

            if (isset($spotifyResults->tracks->items)) {
                $this->searchResults($spotifyResults->tracks->items, $spotifySearchTrack);

                // echo "Results:\r\n";
                foreach ($this->allResults as $nr => $item) {
                    // echo $nr . '. SEARCHED:' . $item->search_artist . ' ' . $item->search_name . ' ' . $item->search_album . ' =>  SPOTIFY RESULT: ' . $item->artist . ' ' . $item->name . ' ' . $item->album . ' [Score:' . ceil($item->score) . "]cle\r\n";
                }
                $this->getBestResult();
            }

            // Nothing found?
            if ($this->spotifySearchResultTrack == null) {

                // Fake result
                $this->spotifySearchResultTrack = new SpotifySearchResultTrack;
                $this->spotifySearchResultTrack->fill([
                    'spotify_api_track_id' => null,
                    'name' => null,
                    'album' => null,
                    'artist' => null,
                    'year' => null,
                    'track_number' => null,
                    'disc_number' => null,
                    'spotify_api_album_id' => null,
                    'score' => 0,
                    'artwork_url' => null,
                    'status' => 'error',
                    'search_name' => $spotifySearchTrack['name'],
                    'search_album' => $spotifySearchTrack['album'],
                    'search_artist' => $spotifySearchTrack['artist'],
                    'song_id' => $spotifySearchTrack['song_id'],
                ]);
            }
            sleep(1);
        } catch (Exception $e) {
            echo 'Spotify API error: ' . $e;
            Logger::log('error', 'Spotify search and import tracks', 'Spotify Api error: ' . $e);

            return;
        }
        Logger::log('error', 'Spotify search and import tracks', 'Something went wrong');
    }

    private function searchResults($spotifyApiTracks, SpotifySearchTrack $spotifySearchTrack)
    {
        $spotifyScoreSearch = new SpotifyScoreSearch;

        $count = 0;
        while ($count <= count($spotifyApiTracks)) {

            if (isset($spotifyApiTracks[$count])) {

                // Sanitize album and track name
                if (isset($spotifyApiTracks[$count]->album->name)) {
                    $spotifyApiTracks[$count]->album->name_sanitized =
                        $this->spotifyNameHelper->removeUnwantedStrings($spotifyApiTracks[$count]->album->name);
                }

                if (isset($spotifyApiTracks[$count]->name)) {
                    $spotifyApiTracks[$count]->name_sanitized =
                        $this->spotifyNameHelper->removeUnwantedStrings($spotifyApiTracks[$count]->name);
                }

                $score = $spotifyScoreSearch->calculateScoreTrack($spotifyApiTracks[$count], $spotifySearchTrack);
                $status = $spotifyScoreSearch->determineStatus($score['total']);

                $this->allResults[] = $this->convertSpotifyApiTrackToSpotifySearchResultTrack($spotifyApiTracks[$count], $score, $status, $spotifySearchTrack);
            }

            $count = $count + 1;
        }
    }

    // Keep the best Spotify track
    private function getBestResult()
    {
        if ($this->allResults) {

            // Keep highest scoring song
            $this->highestScore = 0;
            $keep = -1;
            foreach ($this->allResults as $key => $item) {
                if ($item['score'] > $this->highestScore) {
                    $keep = $key;
                    $this->highestScore = $item['score'];
                }
            }
            if ($keep >= 0) {
                $this->spotifySearchResultTrack = $this->allResults[$keep];

                return;
            }
        }
    }

    public function getSpotifySearchResultTrack(): SpotifySearchResultTrack
    {
        if (!$this->spotifySearchResultTrack) {
            echo ' null';
            // dd($this->spotifySearchResultTrack);
        }

        return $this->spotifySearchResultTrack;
    }
}
