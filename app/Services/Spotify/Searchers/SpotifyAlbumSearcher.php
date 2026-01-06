<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Dto\Spotify\SpotifySearchQuery;
use App\Services\Logger\Logger;
use App\Services\Spotify\Helpers\SpotifyNameHelper;
use Exception;

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

    public function search(SpotifySearchQuery $spotifySearchQuery): SpotifySearchAlbumResult
    {

        // Use album name
        $this->spotifySearchString = $spotifySearchQuery->artist . ' ' . $spotifySearchQuery->album;


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
            exit();
        }

        return $this->spotifySearchResultAlbum;
    }

    private function scoreAndPickBest(array $spotifyApiAlbums, SpotifySearchQuery $spotifySearchQuery): ?SpotifySearchAlbumResult
    {
        $spotifyScoreSearch = new SpotifyScoreSearch;
        $bestAlbum = null;
        $highestScore = 0;

        foreach ($spotifyApiAlbums as $album) {

            // Sanitize the names coming from spotify
            if (isset($album->name)) {
                $album->name_sanitized =
                    $this->spotifyNameHelper->santizeSpotifyName($album->name);
            }

            if (isset($album->artists[0]->name)) {
                $album->artist_sanitized =
                    $this->spotifyNameHelper->sanitzeSpotifyArtist($album->artists[0]->name);
            }

            $scoredAlbum = $spotifyScoreSearch->calculateScore($album, $spotifySearchQuery, false);
            $scoredAlbum->status = $spotifyScoreSearch->determineStatus($scoredAlbum->score);

            if ($scoredAlbum->score > $highestScore) {
                $highestScore = $scoredAlbum->score;
                $bestAlbum = $scoredAlbum;
            }
        }

        /*
        if (!$bestAlbum) {
            return null;
        }
            */

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
            artist_sanitized: $bestAlbum->artist_sanitized ?? null,
            score: (int) round($bestAlbum->score),
            status: $bestAlbum->status ?? 'error',
            search_name: $spotifySearchQuery->album ?? '',
            search_artist: $spotifySearchQuery->artist ?? '',
            album_id: $spotifySearchQuery->album_id ?? 0,
            year: $releaseYear,
            artwork_url: $bestAlbum->images[0]->url ?? null,
            score_breakdown: $bestAlbum->score_breakdown ?? [],
            all_results: $spotifyApiAlbums
        );
    }
}
