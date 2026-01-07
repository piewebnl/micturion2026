<?php

namespace App\Services\Spotify\Importers;

use App\Dto\Spotify\SpotifySearchTrackResult;
use App\Dto\Spotify\SpotifySearchTrackQuery;
use App\Models\Music\Song;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyTrack;
use App\Services\Logger\Logger;
use App\Services\Spotify\Searchers\SpotifyTrackScoreSearch;
use App\Services\SpotifyApi\Getters\SpotifyApiAlbumTracksGetter;
use Illuminate\Support\Collection;

// Import spotifyTracks for a spotify album and match by disc/spotifyTrack number
class SpotifyAlbumTracksImporter
{
    private $api;

    private string $channel = 'spotify_search_and_import_spotifyTracks';

    private const MIN_NAME_SIMILARITY = 50;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function import(SpotifyAlbum $spotifyAlbum): void
    {
        $spotifyApiAlbumId = $spotifyAlbum->spotify_api_album_id;
        $spotifyTracksGetter = new SpotifyApiAlbumTracksGetter($this->api);
        $spotifyTracks = $spotifyTracksGetter->getAll($spotifyApiAlbumId);

        $spotifyTrackMap = $this->mapFoundSpotifyTracks($spotifyTracks);

        foreach ($spotifyAlbum->songs as $song) {
            $spotifyTrack = $this->findMatchingSpotifyTrack($spotifyTrackMap, $song);
            if (!$spotifyTrack) {
                Logger::log(
                    'warning',
                    $this->channel,
                    'Spotify spotifyTrack not found on album: ' . $song->artist_name . ' - ' . $song->album_name . ' - ' . $song->name
                );
                continue;
            }

            $spotifySearchQuery = SpotifySearchTrackQuery::fromSong($song);

            $spotifyScoreSearch = new SpotifyTrackScoreSearch;
            $scoredTrack = $spotifyScoreSearch->calculateScore($spotifyTrack, $spotifySearchQuery);
            $scoredTrack->status = $spotifyScoreSearch->determineStatus($scoredTrack->score ?? 0);

            $nameSimilarity = $scoredTrack->score_breakdown['name_raw'] ?? null;
            if ($nameSimilarity !== null && $nameSimilarity < self::MIN_NAME_SIMILARITY) {
                Logger::log(
                    'warning',
                    $this->channel,
                    'Spotify spotifyTrack name mismatch on album: ' . $song->album_name . ' - ' . $song->name,
                    [
                        ['iTunes spotifyTrack: ' . ($song->name ?? '')],
                        ['Spotify spotifyTrack: ' . $spotifyTrack->name],
                        ['Similarity: ' . round($nameSimilarity)],
                    ]
                );
                continue;
            }

            $spotifySearchTrackResult = new SpotifySearchTrackResult(
                spotify_api_track_id: $spotifyTrack->id ?? null,
                spotify_api_album_id: $spotifyApiAlbumId,
                name: $spotifyTrack->name,
                album: $spotifyAlbum->name,
                artist: $spotifyTrack->artists[0]->name ?? $song->artist_name ?? '',
                score: (int) round($scoredTrack->score ?? 0),
                status: $scoredTrack->status ?? 'error',
                search_name: $song->name ?? '',
                search_album: null,
                search_artist: null,
                song_id: $song->id ?? 0,
                score_breakdown: $scoredTrack->score_breakdown ?? [],
                track_number: $spotifyTrack->track_number ?? $song->track_number ?? null,
                disc_number: $spotifyTrack->disc_number ?? $song->disc_number ?? null,
                year: is_numeric($song->album_year ?? null) ? (int) $song->album_year : null,
                artwork_url: $spotifyAlbum?->artwork_url,
                all_results: []
            );

            $spotifyTrackModel = new SpotifyTrack;
            $spotifyTrackModel->storeFromSpotifySearchResultTrack($spotifySearchTrackResult, $song);
        }
    }

    private function mapFoundSpotifyTracks(array $spotifyTracks): Collection
    {
        $spotifyTrackMap = collect();
        foreach ($spotifyTracks as $spotifyTrack) {
            $disc = $spotifyTrack->disc_number ?? 1;
            $spotifyTrackNumber = $spotifyTrack->track_number ?? null;
            if ($spotifyTrackNumber === null) {
                continue;
            }
            $discKey = (string) $disc;
            if (!$spotifyTrackMap->has($discKey)) {
                $spotifyTrackMap->put($discKey, collect());
            }
            $spotifyTrackMap->get($discKey)->put((string) $spotifyTrackNumber, $spotifyTrack);
        }

        return $spotifyTrackMap;
    }

    private function findMatchingSpotifyTrack(Collection $spotifyTrackMap, Song $song)
    {
        $disc = $song->disc_number ?? 1;
        $spotifyTrackNumber = $song->track_number ?? null;
        if ($spotifyTrackNumber === null) {
            return null;
        }

        $discKey = (string) $disc;
        $spotifyTrackKey = (string) $spotifyTrackNumber;

        return $spotifyTrackMap->get($discKey)?->get($spotifyTrackKey);
    }
}
