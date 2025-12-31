<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Spotify\SpotifySearchAlbum;
use App\Models\Spotify\SpotifySearchTrack;
use App\Services\Spotify\Helpers\SpotifyNameHelper;

// Search spotify api for albums
class SpotifyScoreSearch
{
    private $spotifyNameHelper;

    public function __construct()
    {
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function calculateScoreTrack($spotifyApiTrack, SpotifySearchTrack $spotifySearchTrack): array
    {
        $score['total'] = 0;
        $weightTotal = 10;

        // Result should match artist
        if (isset($spotifyApiTrack->artists[0]->name)) {
            $score['artist'] = $this->spotifyNameHelper->areNamesSimilar($spotifySearchTrack['artist'], $spotifyApiTrack->artists[0]->name) * 3;
            $score['total'] = $score['total'] + $score['artist'];
        }

        if (isset($spotifyApiTrack->album->name)) {

            $score['album'] = $this->spotifyNameHelper->areNamesSimilar($spotifySearchTrack['album'], $spotifyApiTrack->album->name_sanitized) * 2;
            $score['total'] = $score['total'] + $score['album'];
        }

        if (isset($spotifyApiTrack->name)) {

            $score['name'] = $this->spotifyNameHelper->areNamesSimilar($spotifySearchTrack['name'], $spotifyApiTrack->name_sanitized) * 3;
            $score['total'] = $score['total'] + $score['name'];
        }

        $spotifyAlbumYear = substr($spotifyApiTrack->album->release_date, 0, 4);
        if ($spotifyAlbumYear == $spotifySearchTrack['year']) {
            $score['year'] = 90;
            $score['total'] = $score['total'] + $score['year'];
        }

        if ($spotifyApiTrack->track_number == $spotifySearchTrack['track_number']) {
            $score['track_number'] = 100;
            $score['total'] = $score['total'] + $score['track_number'];
        }

        $score['total'] = $score['total'] / $weightTotal;

        return $score;
    }

    public function calculateScoreAlbum($spotifyApiAlbum, SpotifySearchAlbum $spotifySearchAlbum): array
    {
        $score['total'] = 0;
        $weightTotal = 7;

        // Result should match artist
        if (isset($spotifyApiAlbum->artists[0]->name)) {
            $score['artist'] = $this->spotifyNameHelper->areNamesSimilar($spotifySearchAlbum['artist'], $spotifyApiAlbum->artists[0]->name) * 3;
            // echo $spotifySearchAlbum['artist'] . ' vs  ' . $spotifyApiAlbum->artists[0]->name;
            $score['total'] = $score['total'] + $score['artist'];
        }

        // Similar name?
        if (isset($spotifyApiAlbum->name)) {
            $score['name'] = $this->spotifyNameHelper->areNamesSimilar($spotifySearchAlbum['name'], $spotifyApiAlbum->name) * 3;
            $score['total'] = $score['total'] + $score['name'];
        }

        $spotifyAlbumYear = substr($spotifyApiAlbum->release_date, 0, 4);
        if ($spotifyAlbumYear == $spotifySearchAlbum['year']) {
            $score['year'] = 90;
            $score['total'] = $score['total'] + $score['year'];
        }

        $score['total'] = $score['total'] / $weightTotal;

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
}
