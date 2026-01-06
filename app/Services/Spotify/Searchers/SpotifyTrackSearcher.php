<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchTrackResult;
use App\Dto\Spotify\SpotifySearchQuery;
use App\Services\Logger\Logger;
use App\Services\Spotify\Helpers\SpotifyNameHelper;
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

        // Use album name
        $this->spotifySearchString = $spotifySearchQuery->artist . ' ' . $spotifySearchQuery->album;


        try {
            $spotifyResults = $this->api->search($this->spotifySearchString, 'track', ['limit' => 10, 'market' => 'NL']);

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

    private function scoreAndPickBest(array $spotifyApiTracks, SpotifySearchQuery $spotifySearchQuery): ?SpotifySearchTrackResult
    {
        $spotifyScoreSearch = new SpotifyScoreSearch;
        $bestTrack = null;
        $highestScore = 0;

        foreach ($spotifyApiTracks as $album) {

            // Sanitize the names coming from spotify
            if (isset($album->name)) {
                $album->name_sanitized =
                    $this->spotifyNameHelper->santizeSpotifyName($album->name);
            }

            if (isset($album->artists[0]->name)) {
                $album->artist_sanitized =
                    $this->spotifyNameHelper->sanitzeSpotifyArtist($album->artists[0]->name);
            }

            $scoredTrack = $spotifyScoreSearch->calculateScore($album, $spotifySearchQuery, false);
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
            name: $bestTrack->name ?? '',
            name_sanitized: $bestTrack->name_sanitized ?? null,
            album: $bestTrack->albums[0]->name ?? '',
            album_sanitized: $bestTrack->album_sanitized ?? null,
            artist: $bestTrack->artists[0]->name ?? '',
            artist_sanitized: $bestTrack->artist_sanitized ?? null,
            score: (int) round($bestTrack->score),
            status: $bestTrack->status ?? 'error',
            search_name: $spotifySearchQuery->album ?? '',
            search_artist: $spotifySearchQuery->artist ?? '',
            song_id: $spotifySearchQuery->song_id ?? 0,
            year: $releaseYear,
            artwork_url: $bestTrack->images[0]->url ?? null,
            score_breakdown: $bestTrack->score_breakdown ?? [],
            all_results: $spotifyApiTracks
        );
    }
}
