<?php

namespace App\Services\Spotify\Searchers;

use Exception;
use App\Services\Logger\Logger;
use App\Dto\Spotify\SpotifySearchQuery;
use App\Services\Spotify\Dto\SpotifySearchAlbum;
use App\Services\Spotify\Helpers\SpotifyNameHelper;

// Search spotify api for albums
class SpotifyAlbumSearcher
{
    private $api;

    private SpotifyNameHelper $spotifyNameHelper;

    private $allResults = []; // All allResults from spotify search

    private $spotifySearchString = '';

    private $highestScore = 0;

    public function __construct($api)
    {
        $this->api = $api;
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function search(SpotifySearchQuery $spotifySearchQuery)
    {

        dd($spotifySearchQuery);

        $this->spotifySearchString = $spotifySearchQuery->artist . ' ' . $spotifySearchQuery->name;

        try {
            $spotifyResults = $this->api->search($this->spotifySearchString, 'album', ['limit' => 10, 'market' => 'NL']);
            dd($spotifyResults);

            if (isset($spotifyResults->albums->items)) {

                $this->searchResults($spotifyResults->albums->items, $spotifySearchQuery);

                // echo "Results:\r\n";
                foreach ($this->allResults as $nr => $item) {
                    // echo $nr . '. SEARCHED:' . $item->search_artist . ' ' . $item->search_name . ' ' . $item->search_album . ' =>  SPOTIFY RESULT: ' . $item->artist . ' ' . $item->name . ' ' . $item->album . ' [Score:' . ceil($item->score) . "]cle\r\n";
                }
                $this->getBestResult();
            }
        } catch (Exception $e) {
            echo 'Spotify API error: ' . $e;
            Logger::log('error', 'Spotify search and import albums', 'Spotify Api error: ' . $e);

            return;
        }

        Logger::log('error', 'Spotify search and import albums', 'Something went wrong');
    }


    private function searchResults($spotifyApiAlbums, SpotifySearchQuery $spotifySearchQuery)
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
                $score = $spotifyScoreSearch->calculateScoreAlbum($spotifyApiAlbums[$count], $spotifySearchQuery);
                $status = $spotifyScoreSearch->determineStatus($score['total']);

                //$this->allResults[] = $this->convertSpotifyApiAlbumToSpotifySearchResultAlbum($spotifyApiAlbums[$count], $score, $status, $spotifySearchAlbumDto);
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
}
