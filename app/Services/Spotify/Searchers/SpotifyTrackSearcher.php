<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchAlbumTrackResult;
use App\Dto\Spotify\SpotifySearchTrackQuery;
use App\Services\Logger\Logger;
use App\Services\Spotify\Searchers\SpotifyTrackScoreSearch;
use Exception;

// Search spotify api for tracks
class SpotifyTrackSearcher
{
    private $api;

    private $spotifySearchString = '';

    private ?SpotifySearchAlbumTrackResult $spotifySearchResultTrack = null;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function search(SpotifySearchTrackQuery $spotifySearchQuery): SpotifySearchAlbumTrackResult
    {

        // Prefer a fielded query to avoid wrong versions (e.g. live takes)
        $this->spotifySearchString = $this->buildSearchString($spotifySearchQuery, true);

        try {
            $spotifyResults = $this->api->search($this->spotifySearchString, 'track', ['limit' => 8, 'market' => 'NL']);
            if (!isset($spotifyResults->tracks->items) || count($spotifyResults->tracks->items) === 0) {
                $this->spotifySearchString = $this->buildSearchString($spotifySearchQuery, false);
                $spotifyResults = $this->api->search($this->spotifySearchString, 'track', ['limit' => 8, 'market' => 'NL']);
            }
            if (isset($spotifyResults->tracks->items)) {
                $this->spotifySearchResultTrack = $this->scoreAndPickBest(
                    $spotifyResults->tracks->items,
                    $spotifySearchQuery
                );
            }
        } catch (Exception $e) {
            Logger::log('error', 'Spotify search and import tracks', 'Spotify Api error: ' . $e);
            exit();
        }

        return $this->spotifySearchResultTrack;
    }

    private function buildSearchString(SpotifySearchTrackQuery $spotifySearchQuery, bool $includeAlbum): string
    {
        $terms = [];

        if (!empty($spotifySearchQuery->name)) {
            $terms[] = 'track:"' . $this->escapeSearchTerm($spotifySearchQuery->name) . '"';
        }

        if (!empty($spotifySearchQuery->artist)) {
            $terms[] = 'artist:"' . $this->escapeSearchTerm($spotifySearchQuery->artist) . '"';
        }

        if ($includeAlbum && !empty($spotifySearchQuery->album)) {
            $terms[] = 'album:"' . $this->escapeSearchTerm($spotifySearchQuery->album) . '"';
        }

        $searchString = trim(implode(' ', $terms));

        if ($searchString === '') {
            $searchString = trim(($spotifySearchQuery->artist ?? '') . ' ' . ($spotifySearchQuery->name ?? ''));
        }

        return $searchString;
    }

    private function escapeSearchTerm(string $value): string
    {
        return str_replace('"', '\"', $value);
    }

    private function scoreAndPickBest(array $spotifyApiTracks, SpotifySearchTrackQuery $spotifySearchQuery): ?SpotifySearchAlbumTrackResult
    {
        $spotifyScoreSearch = new SpotifyTrackScoreSearch;
        $bestTrack = null;
        $highestScore = 0;

        foreach ($spotifyApiTracks as $track) {

            $scoredTrack = $spotifyScoreSearch->calculateScore($track, $spotifySearchQuery);
            $scoredTrack->status = $spotifyScoreSearch->determineStatus($scoredTrack->score);

            if ($scoredTrack->score > $highestScore) {
                $highestScore = $scoredTrack->score;
                $bestTrack = $scoredTrack;
            }
        }

        /*
        if (!$bestTrack) {
            return null;
        }
            */

        $releaseYear = null;
        if (isset($bestTrack->release_date)) {
            $year = substr($bestTrack->release_date, 0, 4);
            $releaseYear = is_numeric($year) ? (int) $year : null;
        }

        return new SpotifySearchAlbumTrackResult(
            spotify_api_track_id: $bestTrack->id ?? null,
            spotify_api_album_id: $bestTrack->album->id ?? null,
            name: $bestTrack->name ?? '',
            album: $bestTrack->album->name ?? '',
            artist: $bestTrack->artists[0]->name ?? '',
            score: (int) round($bestTrack->score),
            status: $bestTrack->status ?? 'error',
            search_name: $spotifySearchQuery->name ?? '',
            search_album: $spotifySearchQuery->album ?? '',
            search_artist: $spotifySearchQuery->artist ?? '',
            song_id: $spotifySearchQuery->song_id ?? 0,
            year: $releaseYear,
            track_number: $bestTrack->track_number ?? $spotifySearchQuery->track_number ?? null,
            disc_number: $bestTrack->disc_number ?? null,
            artwork_url: $bestTrack->album->images[0]->url ?? null,
            score_breakdown: $bestTrack->score_breakdown ?? [],
            all_results: $spotifyApiTracks
        );
    }
}
