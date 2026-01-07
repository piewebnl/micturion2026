<?php

namespace App\Dto\Spotify;

class SpotifySearchTrackResult
{
    public function __construct(
        public ?string $spotify_api_track_id,
        public ?string $spotify_api_album_id,
        public string $name,
        public ?string $name_sanitized,
        public string $album,
        public ?string $album_sanitized,
        public string $artist,
        public ?string $artist_sanitized,
        public int $score,
        public string $status, // error, warning, succes, custom or unavailable
        public string $search_name,
        public string $search_album,
        public string $search_artist,
        public int $song_id,
        public array $score_breakdown = [],
        public ?int $track_number = null,
        public ?int $disc_number = null,
        public ?int $year = null,
        public ?string $artwork_url = null,
        public ?array $all_results = []
    ) {}

    public function toArray(): array
    {
        return [
            'spotify_api_track_id' => $this->spotify_api_track_id,
            'spotify_api_album_id' => $this->spotify_api_album_id,
            'name' => $this->name,
            'name_sanitized' => $this->name_sanitized,
            'album' => $this->album,
            'album_sanitized' => $this->album_sanitized,
            'artist' => $this->artist,
            'artist_sanitized' => $this->artist_sanitized,
            'score' => $this->score,
            'status' => $this->status,
            'search_name' => $this->search_name,
            'search_album' => $this->search_album,
            'search_artist' => $this->search_artist,
            'song_id' => $this->song_id,
            'score_breakdown' => $this->score_breakdown,
            'track_number' => $this->track_number,
            'disc_number' => $this->disc_number,
            'year' => $this->year,
            'artwork_url' => $this->artwork_url,
            'all_restults' => $this->all_results,
        ];
    }

    public static function fromSpotifyApiTrack($spotifyTrack, SpotifySearchQuery $spotifySearchQuery): self
    {

        $releaseYear = null;
        if (isset($spotifyTrack->release_date)) {
            $year = substr($spotifyTrack->release_date, 0, 4);
            $releaseYear = is_numeric($year) ? (int) $year : null;
        }

        return new self(
            spotify_api_track_id: $spotifyTrack->id ?? null,
            spotify_api_album_id: $spotifyTrack->album->id ?? null,
            name: $spotifyTrack->name ?? '',
            name_sanitized: $spotifyTrack->name_sanitized ?? null,
            album: $spotifyTrack->album->name ?? '',
            album_sanitized: $spotifyTrack->album_sanitized ?? null,
            artist: $spotifyTrack->artists[0]->name ?? '',
            artist_sanitized: $spotifyTrack->artist_sanitized ?? null,
            score: (int) round($spotifyTrack->score),
            status: $spotifyTrack->status ?? 'error',
            search_name: $spotifySearchQuery->name ?? '',
            search_album: $spotifySearchQuery->album ?? '',
            search_artist: $spotifySearchQuery->artist ?? '',
            song_id: $spotifySearchQuery->song_id ?? 0,
            year: $releaseYear,
            track_number: $spotifySearchQuery->track_number ?? null,
            disc_number: $spotifySearchQuery->disc_number ?? null,
            artwork_url: $spotifyTrack->images[0]->url ?? null,
            score_breakdown: $spotifyTrack->score_breakdown ?? [],
            all_results: []
        );
    }
}
