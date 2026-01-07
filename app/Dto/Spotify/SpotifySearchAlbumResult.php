<?php

namespace App\Dto\Spotify;

class SpotifySearchAlbumResult
{
    public function __construct(
        public ?string $spotify_api_album_id,
        public string $name,
        public string $artist,
        public int $score,
        public string $status, // error, warning, succes, custom or unavailable
        public string $search_name,
        public string $search_artist,
        public int $album_id,
        public array $score_breakdown = [],
        public ?int $year = null,
        public ?string $artwork_url = null,
        public ?array $all_results = []
    ) {}

    public function toArray(): array
    {
        return [
            'spotify_api_album_id' => $this->spotify_api_album_id,
            'name' => $this->name,
            'artist' => $this->artist,
            'score' => $this->score,
            'status' => $this->status,
            'search_name' => $this->search_name,
            'search_artist' => $this->search_artist,
            'album_id' => $this->album_id,
            'score_breakdown' => $this->score_breakdown,
            'year' => $this->year,
            'artwork_url' => $this->artwork_url,
            'all_restults' => $this->all_results,
        ];
    }

    public static function fromSpotifyApiAlbum($spotifyAlbum, SpotifySearchAlbumQuery $spotifySearchQuery): self
    {

        $releaseYear = null;
        if (isset($spotifyAlbum->release_date)) {
            $year = substr($spotifyAlbum->release_date, 0, 4);
            $releaseYear = is_numeric($year) ? (int) $year : null;
        }

        return new self(
            spotify_api_album_id: $spotifyAlbum->id ?? null,
            name: $spotifyAlbum->name ?? '',
            artist: $spotifyAlbum->artists[0]->name ?? '',
            score: (int) round($spotifyAlbum->score),
            status: $spotifyAlbum->status ?? 'error',
            search_name: $spotifySearchQuery->album ?? '',
            search_artist: $spotifySearchQuery->artist ?? '',
            album_id: $spotifySearchQuery->album_id ?? 0,
            year: $releaseYear,
            artwork_url: $spotifyAlbum->images[0]->url ?? null,
            score_breakdown: $spotifyAlbum->score_breakdown ?? [],
            all_results: []
        );
    }
}
