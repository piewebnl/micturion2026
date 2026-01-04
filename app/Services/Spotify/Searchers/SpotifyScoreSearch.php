<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchQuery;
use App\Services\Spotify\Helpers\SpotifyNameHelper;

// Search spotify api for albums
class SpotifyScoreSearch
{
    private $spotifyNameHelper;
    private const YEAR_PROXIMITY_RANGE = 5;
    private const TRACK_COUNT_RANGE = 5;

    public function __construct()
    {
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function calculateScoreTrack($spotifyApiTrack, SpotifySearchQuery $context): array
    {
        $score['total'] = 0;
        $weightTotal = 0;
        $searchArtist = $context->artist;
        $searchAlbum = $context->album_name;
        $searchName = $context->name;
        $searchYear = $context->year;
        $searchTrackNumber = $context->track_number;

        // Result should match artist
        if ($searchArtist && isset($spotifyApiTrack->artists[0]->name)) {
            $weightTotal += 3;
            $score['artist'] = $this->spotifyNameHelper->areNamesSimilar($searchArtist, $spotifyApiTrack->artists[0]->name) * 3;
            $score['total'] = $score['total'] + $score['artist'];
        }

        if ($searchAlbum && isset($spotifyApiTrack->album->name)) {

            $weightTotal += 2;
            $score['album'] = $this->spotifyNameHelper->areNamesSimilar($searchAlbum, $spotifyApiTrack->album->name_sanitized) * 2;
            $score['total'] = $score['total'] + $score['album'];
        }

        if ($searchName && isset($spotifyApiTrack->name)) {

            $weightTotal += 3;
            $score['name'] = $this->spotifyNameHelper->areNamesSimilar($searchName, $spotifyApiTrack->name_sanitized) * 3;
            $score['total'] = $score['total'] + $score['name'];
        }

        $spotifyAlbumYear = $this->getReleaseYear($spotifyApiTrack->album->release_date ?? null);
        if ($spotifyAlbumYear && $searchYear) {
            $weightTotal += 2;
            $score['year'] = $this->scoreProximity($searchYear, $spotifyAlbumYear, self::YEAR_PROXIMITY_RANGE) * 2;
            $score['total'] = $score['total'] + $score['year'];
        }

        if ($searchTrackNumber && isset($spotifyApiTrack->track_number)) {
            $weightTotal += 2;
            $score['track_number'] = ($spotifyApiTrack->track_number == $searchTrackNumber ? 100 : 0) * 2;
            $score['total'] = $score['total'] + $score['track_number'];
        }

        if (isset($spotifyApiTrack->popularity) && is_numeric($spotifyApiTrack->popularity)) {
            $weightTotal += 1;
            $score['popularity'] = $this->scorePopularity($spotifyApiTrack->popularity) * 1;
            $score['total'] = $score['total'] + $score['popularity'];
        }

        $score['total'] = $this->finalizeScore($score['total'], $weightTotal);

        return $score;
    }

    public function calculateScoreAlbum($spotifyApiAlbum, SpotifySearchQuery $context): array
    {
        $score['total'] = 0;
        $weightTotal = 0;
        $searchArtist = $context->artist;
        $searchName = $context->name;
        $searchYear = $context->year;
        $searchTrackCount = $context->track_count;

        // Result should match artist
        if ($searchArtist && isset($spotifyApiAlbum->artists[0]->name)) {
            $weightTotal += 3;
            $score['artist'] = $this->spotifyNameHelper->areNamesSimilar($searchArtist, $spotifyApiAlbum->artists[0]->name) * 3;
            // echo $spotifySearchAlbum['artist'] . ' vs  ' . $spotifyApiAlbum->artists[0]->name;
            $score['total'] = $score['total'] + $score['artist'];
        }

        // Similar name?
        if ($searchName && isset($spotifyApiAlbum->name)) {
            $weightTotal += 3;
            $score['name'] = $this->spotifyNameHelper->areNamesSimilar($searchName, $spotifyApiAlbum->name) * 3;
            $score['total'] = $score['total'] + $score['name'];
        }

        $spotifyAlbumYear = $this->getReleaseYear($spotifyApiAlbum->release_date ?? null);
        if ($spotifyAlbumYear && $searchYear) {
            $weightTotal += 2;
            $score['year'] = $this->scoreProximity($searchYear, $spotifyAlbumYear, self::YEAR_PROXIMITY_RANGE) * 2;
            $score['total'] = $score['total'] + $score['year'];
        }

        if ($searchTrackCount && isset($spotifyApiAlbum->total_tracks)) {
            $weightTotal += 2;
            $score['track_count'] = $this->scoreProximity($searchTrackCount, $spotifyApiAlbum->total_tracks, self::TRACK_COUNT_RANGE) * 2;
            $score['total'] = $score['total'] + $score['track_count'];
        }

        if (isset($spotifyApiAlbum->popularity) && is_numeric($spotifyApiAlbum->popularity)) {
            $weightTotal += 1;
            $score['popularity'] = $this->scorePopularity($spotifyApiAlbum->popularity) * 1;
            $score['total'] = $score['total'] + $score['popularity'];
        }

        $score['total'] = $this->finalizeScore($score['total'], $weightTotal);

        return $score;
    }

    public function determineStatus($scoreTotal)
    {

        $config = config('spotify');
        $this->successScore = $config['track_search_results_success_score'];
        $this->warningScore = $config['track_search_results_warning_score'];

        if ($scoreTotal >= $this->successScore) {
            return 'success';
        }
        if ($scoreTotal >= $this->warningScore) {
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

    private function scorePopularity(int $popularity): float
    {
        $bounded = max(0, min(100, $popularity));

        return (float) $bounded;
    }
}
