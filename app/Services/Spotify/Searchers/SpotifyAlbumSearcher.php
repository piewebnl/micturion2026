<?php

namespace App\Services\Spotify\Searchers;

use Exception;
use App\Services\Logger\Logger;
use App\Dto\Spotify\SpotifySearchQuery;
use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Services\Spotify\Helpers\SpotifyNameHelper;

// Search spotify api for albums
class SpotifyAlbumSearcher
{
    private $api;

    private SpotifyNameHelper $spotifyNameHelper;

    private $spotifySearchString = '';
    private ?SpotifySearchAlbumResult $spotifySearchResultAlbum = null;

    public function __construct($api)
    {
        $this->api = $api;
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function search(SpotifySearchQuery $spotifySearchQuery)
    {

        $this->spotifySearchString = $spotifySearchQuery->artist . ' ' . $spotifySearchQuery->name;

        try {
            $spotifyResults = $this->api->search($this->spotifySearchString, 'album', ['limit' => 10, 'market' => 'NL']);

            if (isset($spotifyResults->albums->items)) {
                $this->spotifySearchResultAlbum = $this->scoreAndPickBest(
                    $spotifyResults->albums->items,
                    $spotifySearchQuery
                );
            }
        } catch (Exception $e) {
            Logger::log('error', 'Spotify search and import albums', 'Spotify Api error: ' . $e);
            return;
        }

        return $this->spotifySearchResultAlbum;
    }

    private function scoreAndPickBest(array $spotifyApiAlbums, SpotifySearchQuery $spotifySearchQuery): ?SpotifySearchAlbumResult
    {
        $spotifyScoreSearch = new SpotifyScoreSearch;
        $bestAlbum = null;
        $highestScore = 0;

        foreach ($spotifyApiAlbums as $album) {
            if (isset($album->name)) {
                $album->name_sanitized =
                    $this->spotifyNameHelper->removeUnwantedStrings($album->name);
            }

            $scoredAlbum = $spotifyScoreSearch->calculateScore($album, $spotifySearchQuery, false);
            $scoredAlbum->status = $spotifyScoreSearch->determineStatus($scoredAlbum->score);

            if ($scoredAlbum->score > $highestScore) {
                $highestScore = $scoredAlbum->score;
                $bestAlbum = $scoredAlbum;
            }
        }

        if (!$bestAlbum) {
            return null;
        }

        $artworkUrl = $bestAlbum->images[0]->url ?? null;
        $releaseYear = null;
        if (isset($bestAlbum->release_date)) {
            $year = substr($bestAlbum->release_date, 0, 4);
            $releaseYear = is_numeric($year) ? (int) $year : null;
        }

        return new SpotifySearchAlbumResult(
            spotify_api_album_id: $bestAlbum->id ?? null,
            name: $bestAlbum->name ?? '',
            name_sanitized: $bestAlbum->name_sanitized ?? null,
            artist: $bestAlbum->artists[0]->name ?? '',
            score: (int) round($bestAlbum->score),
            status: $bestAlbum->status ?? 'error',
            search_name: $spotifySearchQuery->name ?? '',
            search_artist: $spotifySearchQuery->artist ?? '',
            album_id: $spotifySearchQuery->album_id ?? 0,
            source: 'spotify',
            year: $releaseYear,
            artwork_url: $artworkUrl,
            score_breakdown: $bestAlbum->score_breakdown ?? []
        );
    }
}
