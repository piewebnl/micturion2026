<?php

namespace App\Dto\Spotify;

use App\Models\Music\Album;
use App\Models\Spotify\SpotifySearchTrack;

class SpotifySearchQuery
{
    public function __construct(
        public ?int $album_id,
        public ?string $sort_name,
        public ?string $persistent_id,
        public ?string $name,
        public mixed $album,
        public ?string $artist,
        public ?int $year,
        public ?string $album_name,
        public ?int $track_number,
        public ?int $track_count,
    ) {}

    public static function fromAlbum(Album $album, string $artist): self
    {
        return new self(
            album_id: $album->id,
            sort_name: $album->sort_name,
            persistent_id: $album->persistent_id,
            name: $album->name,
            album: $album,
            artist: $artist,
            year: is_numeric($album->year) ? (int) $album->year : null,
            album_name: null,
            track_number: null,
            track_count: self::resolveTrackCount($album),
        );
    }

    public static function fromTrack(SpotifySearchTrack $track): self
    {
        return new self(
            album_id: null,
            sort_name: null,
            persistent_id: is_string($track['persistent_id'] ?? null) ? $track['persistent_id'] : null,
            name: is_string($track['name'] ?? null) ? $track['name'] : null,
            album: null,
            artist: is_string($track['artist'] ?? null) ? $track['artist'] : null,
            year: is_numeric($track['year'] ?? null) ? (int) $track['year'] : null,
            album_name: is_string($track['album'] ?? null) ? $track['album'] : null,
            track_number: is_numeric($track['track_number'] ?? null) ? (int) $track['track_number'] : null,
            track_count: null,
        );
    }

    private static function resolveTrackCount(Album $album): ?int
    {
        try {
            return (int) $album->songs()->count();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
