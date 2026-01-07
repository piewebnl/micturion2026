<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchAlbumQuery;
use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Services\Logger\Logger;
use Exception;

// Search spotify api for albums
class SpotifyAlbumSearcher
{
    private $api;

    private $spotifySearchString = '';

    private ?SpotifySearchAlbumResult $spotifySearchResultAlbum = null;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function search(SpotifySearchAlbumQuery $spotifySearchQuery): SpotifySearchAlbumResult
    {

        // Use album name
        $this->spotifySearchString = $spotifySearchQuery->artist . ' ' . $spotifySearchQuery->album;

        try {
            $spotifyResults = $this->api->search($this->spotifySearchString, 'album', ['limit' => 8, 'market' => 'NL']);
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

    private function scoreAndPickBest(array $spotifyApiAlbums, SpotifySearchAlbumQuery $spotifySearchQuery): ?SpotifySearchAlbumResult
    {
        $spotifyScoreSearch = new SpotifyAlbumScoreSearch;
        $bestAlbum = null;
        $highestScore = 0;

        foreach ($spotifyApiAlbums as $album) {

            $scoredAlbum = $spotifyScoreSearch->calculateScore($album, $spotifySearchQuery);
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
            artist: $bestAlbum->artists[0]->name ?? '',
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
