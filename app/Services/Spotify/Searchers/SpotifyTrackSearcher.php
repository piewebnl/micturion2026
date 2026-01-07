<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchTrackResult;
use App\Dto\Spotify\SpotifySearchQuery;
use App\Services\Logger\Logger;
use App\Services\Spotify\Helpers\SpotifyNameHelper;
use App\Services\Spotify\Searchers\SpotifyTrackScoreSearch;
use Exception;

// Search spotify api for tracks
class SpotifyTrackSearcher
{
    private $api;

    private SpotifyNameHelper $spotifyNameHelper;

    private $spotifySearchString = '';

    private ?SpotifySearchTrackResult $spotifySearchResultTrack = null;

    public function __construct($api)
    {
        $this->api = $api;
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function search(SpotifySearchQuery $spotifySearchQuery): SpotifySearchTrackResult
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

    private function buildSearchString(SpotifySearchQuery $spotifySearchQuery, bool $includeAlbum): string
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

    private function scoreAndPickBest(array $spotifyApiTracks, SpotifySearchQuery $spotifySearchQuery): ?SpotifySearchTrackResult
    {
        $spotifyScoreSearch = new SpotifyTrackScoreSearch;
        $bestTrack = null;
        $highestScore = 0;

        foreach ($spotifyApiTracks as $track) {

            // Sanitize the names coming from spotify
            if (isset($track->name)) {
                $track->name_sanitized =
                    $this->spotifyNameHelper->santizeSpotifyName($track->name);
            }

            if (isset($track->album->name)) {
                $track->album_sanitized =
                    $this->spotifyNameHelper->santizeSpotifyName($track->album->name);
            }
            if (isset($track->artists[0]->name)) {
                $track->artist_sanitized =
                    $this->spotifyNameHelper->sanitzeSpotifyArtist($track->artists[0]->name);
            }

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

        return new SpotifySearchTrackResult(
            spotify_api_track_id: $bestTrack->id ?? null,
            spotify_api_album_id: $bestTrack->album->id ?? null,
            name: $bestTrack->name ?? '',
            name_sanitized: $bestTrack->name_sanitized ?? null,
            album: $bestTrack->album->name ?? '',
            album_sanitized: $bestTrack->album_sanitized ?? null,
            artist: $bestTrack->artists[0]->name ?? '',
            artist_sanitized: $bestTrack->artist_sanitized ?? null,
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
