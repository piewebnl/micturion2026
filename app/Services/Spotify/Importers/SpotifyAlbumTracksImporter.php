<?php

namespace App\Services\Spotify\Importers;

use App\Dto\Spotify\SpotifySearchTrackResult;
use App\Dto\Spotify\SpotifySearchQuery;
use App\Models\Music\Song;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyTrack;
use App\Services\Logger\Logger;
use App\Services\Spotify\Helpers\SpotifyNameHelper;
use App\Services\Spotify\Matchers\SpotifyTrackMatchScorer;
use App\Services\Spotify\Searchers\SpotifyTrackScoreSearch;
use App\Services\SpotifyApi\Getters\SpotifyApiAlbumTracksGetter;

// Import tracks for a spotify album and match by disc/track number
class SpotifyAlbumTracksImporter
{
    private $api;

    private SpotifyNameHelper $spotifyNameHelper;

    private string $channel = 'spotify_search_and_import_tracks';

    public function __construct($api)
    {
        $this->api = $api;
        $this->spotifyNameHelper = new SpotifyNameHelper;
    }

    public function importAlbumTracks(int $albumId, string $spotifyApiAlbumId, iterable $songs): void
    {
        $tracksGetter = new SpotifyApiAlbumTracksGetter($this->api);
        $spotifyTracks = $tracksGetter->getAll($spotifyApiAlbumId);

        $trackMap = $this->mapTracks($spotifyTracks);
        $spotifyAlbum = SpotifyAlbum::where('album_id', $albumId)->first(['artwork_url', 'name']);
        $artworkUrl = $spotifyAlbum?->artwork_url;
        $spotifyAlbumName = $spotifyAlbum?->name;
        $spotifyScoreSearch = new SpotifyTrackScoreSearch;
        $trackMatchScorer = new SpotifyTrackMatchScorer;

        foreach ($songs as $song) {
            $track = $this->findTrack($trackMap, $song);
            if (!$track) {
                Logger::log(
                    'warning',
                    $this->channel,
                    'Spotify track not found on album: ' . $song->artist_name . ' - ' . $song->album_name . ' - ' . $song->name
                );
                continue;
            }

            $spotifySearchQuery = SpotifySearchQuery::fromSong($song);
            $scoredTrack = $trackMatchScorer->scoreTrackMatch(
                $track,
                $spotifySearchQuery,
                $spotifyAlbumName,
                $spotifyScoreSearch
            );

            $trackArtist = $track->artists[0]->name ?? $song->artist_name ?? '';
            $trackName = $track->name ?? '';
            $albumName = $song->album_name ?? '';

            $spotifySearchTrackResult = new SpotifySearchTrackResult(
                spotify_api_track_id: $track->id ?? null,
                spotify_api_album_id: $spotifyApiAlbumId,
                name: $trackName,
                name_sanitized: $this->spotifyNameHelper->santizeSpotifyName($trackName),
                album: $albumName,
                album_sanitized: $this->spotifyNameHelper->santizeSpotifyName($albumName),
                artist: $trackArtist,
                artist_sanitized: $this->spotifyNameHelper->sanitzeSpotifyArtist($trackArtist),
                score: (int) round($scoredTrack->score ?? 0),
                status: $scoredTrack->status ?? 'error',
                search_name: $song->name ?? '',
                search_album: $albumName,
                search_artist: $song->artist_name ?? '',
                song_id: $song->id ?? 0,
                score_breakdown: $scoredTrack->score_breakdown ?? [],
                track_number: $track->track_number ?? $song->track_number ?? null,
                disc_number: $track->disc_number ?? $song->disc_number ?? null,
                year: is_numeric($song->album_year ?? null) ? (int) $song->album_year : null,
                artwork_url: $artworkUrl,
                all_results: []
            );

            $spotifyTrackModel = new SpotifyTrack;
            $spotifyTrackModel->storeFromSpotifySearchResultTrack($spotifySearchTrackResult, $song);
        }
    }

    private function mapTracks(array $spotifyTracks): array
    {
        $trackMap = [];
        foreach ($spotifyTracks as $track) {
            $disc = $track->disc_number ?? 1;
            $trackNumber = $track->track_number ?? null;
            if ($trackNumber === null) {
                continue;
            }
            $trackMap[$disc][$trackNumber] = $track;
        }

        return $trackMap;
    }

    private function findTrack(array $trackMap, Song $song)
    {
        $disc = $song->disc_number ?? 1;
        $trackNumber = $song->track_number ?? null;
        if ($trackNumber === null) {
            return null;
        }

        return $trackMap[$disc][$trackNumber] ?? null;
    }

}
