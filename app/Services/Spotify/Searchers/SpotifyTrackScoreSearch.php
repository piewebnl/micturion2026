<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchTrackQuery;
use App\Services\Spotify\Helpers\SpotifyNameHelper;

// Score spotify track search results
class SpotifyTrackScoreSearch
{
    private $spotifyNameHelper;

    private const YEAR_PROXIMITY_RANGE = 5;

    public function __construct()
    {
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function determineStatus($scoreTotal)
    {
        $successScore = config('spotify.track_search_results_success_score');
        $warningScore = config('spotify.track_search_results_warning_score');

        if ($scoreTotal >= $successScore) {
            return 'success';
        }
        if ($scoreTotal >= $warningScore) {
            return 'warning';
        }

        return 'error';
    }

    private function finalizeScore(float $scoreTotal, int $weightTotal): float
    {
        if ($weightTotal <= 0) {
            return 0;
        }

        return $scoreTotal / $weightTotal;
    }

    public function calculateScore($spotifyApiResult, SpotifySearchTrackQuery $spotifySearchQuery)
    {
        $score = ['total' => 0];
        $weightTotal = 0;

        $searchName = $spotifySearchQuery->name;
        $searchArtist = $spotifySearchQuery->artist;
        $searchYear = $spotifySearchQuery->year;
        $searchAlbum = $spotifySearchQuery->album;

        $releaseDate = $spotifyApiResult->album->release_date ?? null;
        $spotifyReleaseYear = $this->getReleaseYear($releaseDate);

        if ($searchArtist && isset($spotifyApiResult->artist_sanitized)) {
            $this->addScore(
                $score,
                $weightTotal,
                'artist',
                $this->spotifyNameHelper->areNamesSimilar($searchArtist, $spotifyApiResult->artist_sanitized),
                4
            );
        }

        if ($searchName && isset($spotifyApiResult->name)) {

            $candidateName = $spotifyApiResult->name_sanitized ?? $spotifyApiResult->name;
            $this->addScore(
                $score,
                $weightTotal,
                'name',
                $this->spotifyNameHelper->areNamesSimilar($searchName, $candidateName),
                3
            );
        }

        if ($spotifyReleaseYear && $searchYear) {
            $this->addScore(
                $score,
                $weightTotal,
                'year',
                $this->scoreProximity($searchYear, $spotifyReleaseYear, self::YEAR_PROXIMITY_RANGE),
                1
            );
        }

        if ($searchAlbum && isset($spotifyApiResult->album)) {
            $candidateAlbum = $spotifyApiResult->album->name_sanitized ?? $spotifyApiResult->album->name;
            $this->addScore(
                $score,
                $weightTotal,
                'album',
                $this->spotifyNameHelper->areNamesSimilar($searchAlbum, $candidateAlbum),
                2
            );
        }

        if ($spotifySearchQuery->track_number && isset($spotifyApiResult->track_number)) {
            $this->addScore(
                $score,
                $weightTotal,
                'track_number',
                $spotifyApiResult->track_number == $spotifySearchQuery->track_number ? 100 : 0,
                2
            );
        }

        $score['total'] = $this->finalizeScore($score['total'], $weightTotal);
        $spotifyApiResult->score = $score['total'];
        $spotifyApiResult->score_breakdown = $score;

        return $spotifyApiResult;
    }

    private function addScore(array &$score, int &$weightTotal, string $key, float $value, int $weight): void
    {
        $weightTotal += $weight;
        $score[$key] = $value * $weight;
        $score['total'] += $score[$key];
    }

    private function getReleaseYear(?string $releaseDate): ?int
    {
        if (!$releaseDate) {
            return null;
        }

        $year = substr($releaseDate, 0, 4);
        if (!is_numeric($year)) {
            return null;
        }

        return (int) $year;
    }

    private function scoreProximity(int $expected, int $actual, int $range): float
    {
        $diff = abs($expected - $actual);
        if ($diff >= $range) {
            return 0;
        }

        return 100 - ($diff * (100 / $range));
    }
}
