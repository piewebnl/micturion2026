<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Spotify\SpotifySearchAlbum;
use App\Models\Spotify\SpotifySearchResultAlbum;
use App\Services\Spotify\Helpers\SpotifyNameHelper;
use App\Traits\Converters\ToSpotifySearchResultAlbumConverter;
use App\Services\Logger\Logger;
use Exception;

// Search spotify api for albums
class SpotifyAlbumSearcher
{
    use ToSpotifySearchResultAlbumConverter;

    private $api;

    private SpotifyNameHelper $spotifyNameHelper;

    private $result = []; // actual result meeting score requirement

    private $spotifySearchResultAlbum;

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

    public function search(SpotifySearchAlbum $spotifySearchAlbum)
    {
        $this->searchByName($spotifySearchAlbum);
        $this->resource = $this->spotifySearchResultAlbum;
    }

    private function searchByName(SpotifySearchAlbum $spotifySearchAlbum)
    {

        $this->spotifySearchString = $spotifySearchAlbum['artist'] . ' ' . $spotifySearchAlbum['name'];

        try {
            $spotifyResults = $this->api->search($this->spotifySearchString, 'album', ['limit' => 10, 'market' => 'NL']);

            if (isset($spotifyResults->albums->items)) {
                $this->searchResults($spotifyResults->albums->items, $spotifySearchAlbum);

                // echo "Results:\r\n";
                foreach ($this->allResults as $nr => $item) {
                    // echo $nr . '. SEARCHED:' . $item->search_artist . ' ' . $item->search_name . ' ' . $item->search_album . ' =>  SPOTIFY RESULT: ' . $item->artist . ' ' . $item->name . ' ' . $item->album . ' [Score:' . ceil($item->score) . "]cle\r\n";
                }
                $this->getBestResult();
            }

            // Nothing found?
            if ($this->spotifySearchResultAlbum == null) {

                // Fake result
                $this->spotifySearchResultAlbum = new SpotifySearchResultAlbum;
                $this->spotifySearchResultAlbum->fill([
                    'spotify_api_album_id' => null,
                    'name' => null,
                    'artist' => null,
                    'year' => null,
                    'spotify_api_album_id' => null,
                    'score' => 0,
                    'artwork_url' => null,
                    'status' => 'error',
                    'search_name' => $spotifySearchAlbum['name'],
                    'search_artist' => $spotifySearchAlbum['artist'],
                    'album_id' => $spotifySearchAlbum['album_id'],
                ]);
            }
            sleep(1);
        } catch (Exception $e) {
            echo 'Spotify API error: ' . $e;
            Logger::log('error', 'Spotify search and import albums', 'Spotify Api error: ' . $e);

            return;
        }

        Logger::log('error', 'Spotify search and import albums', 'Something went wrong');
    }

    private function searchResults($spotifyApiAlbums, SpotifySearchAlbum $spotifySearchAlbum)
    {
        $spotifyScoreSearch = new SpotifyScoreSearch;

        $count = 0;
        while ($count <= count($spotifyApiAlbums)) {

            if (isset($spotifyApiAlbums[$count])) {

                // Sanitize album name
                if (isset($spotifyApiAlbums[$count]->name)) {

                    // HELPER
                    $spotifyApiAlbums[$count]->name_sanitized =
                        $this->spotifyNameHelper->removeUnwantedStrings($spotifyApiAlbums[$count]->name);
                }
                $score = $spotifyScoreSearch->calculateScoreAlbum($spotifyApiAlbums[$count], $spotifySearchAlbum);
                $status = $spotifyScoreSearch->determineStatus($score['total']);

                $this->allResults[] = $this->convertSpotifyApiAlbumToSpotifySearchResultAlbum($spotifyApiAlbums[$count], $score, $status, $spotifySearchAlbum);
            }

            $count = $count + 1;
        }
    }

    // Keep the best Spotify album
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
                $this->spotifySearchResultAlbum = $this->allResults[$keep];

                return;
            }
        }
    }

    public function getSpotifySearchResultAlbum(): SpotifySearchResultAlbum
    {
        if (!$this->spotifySearchResultAlbum) {
            echo ' null';
            // dd($this->spotifySearchResultAlbum);
        }

        return $this->spotifySearchResultAlbum;
    }
}
