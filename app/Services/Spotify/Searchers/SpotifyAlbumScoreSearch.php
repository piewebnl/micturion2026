<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchQuery;
use App\Services\Spotify\Helpers\SpotifyNameHelper;

// Score spotify album search results
class SpotifyAlbumScoreSearch
{
    private $spotifyNameHelper;

    private const YEAR_PROXIMITY_RANGE = 5;

    public function __construct()
    {
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function determineStatus($scoreTotal)
    {

        $successScore = config('spotify.album_search_results_success_score');
        $warningScore = config('spotify.album_search_results_warning_score');

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

    public function calculateScore($spotifyApiResult, SpotifySearchQuery $spotifySearchQuery)
    {
        $score = ['total' => 0];
        $weightTotal = 0;

        $searchName = $spotifySearchQuery->album;
        $searchArtist = $spotifySearchQuery->artist;
        $searchYear = $spotifySearchQuery->year;

        $releaseDate = $spotifyApiResult->release_date ?? null;
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
