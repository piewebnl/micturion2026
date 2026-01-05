<?php

namespace App\Dto\Spotify;

class SpotifySearchAlbumResult
{
    public function __construct(
        public ?string $spotify_api_album_id,
        public string $name,
        public ?string $name_sanitized,
        public string $artist,
        public ?string $artist_sanitized,
        public int $score,
        public string $status,
        public string $search_name,
        public string $search_artist,
        public int $album_id,
        public ?string $source,
        public array $score_breakdown = [],
        public ?int $year = null,
        public ?string $artwork_url = null,
        public ?array $all_results = []
        // Unavailabe, Custom ID, Spotify
    ) {}

    public function toArray(): array
    {
        return [
            'spotify_api_album_id' => $this->spotify_api_album_id,
            'name' => $this->name,
            'name_sanitized' => $this->name_sanitized,
            'artist' => $this->artist,
            'artist_sanitized' => $this->artist_sanitized,
            'score' => $this->score,
            'status' => $this->status,
            'search_name' => $this->search_name,
            'search_artist' => $this->search_artist,
            'album_id' => $this->album_id,
            'source' => $this->source,
            'score_breakdown' => $this->score_breakdown,
            'year' => $this->year,
            'artwork_url' => $this->artwork_url,
            'all_restults' => $this->all_results,
        ];
    }
}
