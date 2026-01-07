<?php

namespace App\Services\Spotify\Matchers;

use App\Dto\Spotify\SpotifySearchTrackQuery;
use App\Services\Spotify\Helpers\SpotifyNameHelper;
use App\Services\Spotify\Searchers\SpotifyTrackScoreSearch;

// Score a spotify track against a song search query
class SpotifyTrackMatchScorer
{
    private SpotifyNameHelper $spotifyNameHelper;

    public function __construct()
    {
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function scoreTrackMatch(
        object $track,
        SpotifySearchTrackQuery $spotifySearchQuery,
        ?string $spotifyAlbumName,
        SpotifyTrackScoreSearch $spotifyScoreSearch
    ): object {
        if (isset($track->name)) {
            $track->name_sanitized = $this->spotifyNameHelper->santizeSpotifyName($track->name);
        }

        if (isset($track->artists[0]->name)) {
            $track->artist_sanitized = $this->spotifyNameHelper->sanitzeSpotifyArtist($track->artists[0]->name);
        }

        if (!isset($track->album)) {
            $track->album = (object) [];
        }

        if (empty($track->album->name) && $spotifyAlbumName) {
            $track->album->name = $spotifyAlbumName;
        }

        if (!empty($track->album->name)) {
            $track->album->name_sanitized =
                $this->spotifyNameHelper->santizeSpotifyName($track->album->name);
        }

        $scoredTrack = $spotifyScoreSearch->calculateScore($track, $spotifySearchQuery);
        $scoredTrack->status = $spotifyScoreSearch->determineStatus($scoredTrack->score ?? 0);

        return $scoredTrack;
    }
}
