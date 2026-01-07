<?php

namespace App\Dto\Spotify;

use App\Models\Music\Album;

class SpotifySearchAlbumQuery
{
    public function __construct(
        public ?int $album_id,
        public ?int $year,
        public ?string $album_persistent_id,
        public ?string $artist,
        public ?string $album
    ) {}

    public static function fromAlbum(Album $album): self
    {
        return new self(
            album_id: $album->id,
            album_persistent_id: $album->persistent_id,
            album: $album->name,
            artist: $album->artist_name,
            year: is_numeric($album->year) ? (int) $album->year : null,
        );
    }
}
