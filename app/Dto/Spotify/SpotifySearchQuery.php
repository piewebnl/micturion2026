<?php

namespace App\Dto\Spotify;

use App\Models\Music\Album;
use App\Models\Music\Song;

class SpotifySearchQuery
{
    public function __construct(
        public ?int $album_id,
        public ?int $song_id,
        public ?string $album_persistent_id,
        public ?string $name,
        public mixed $album,
        public ?string $artist,
        public ?int $year,
        public ?int $track_number,

    ) {}

    public static function fromAlbum(Album $album): self
    {
        return new self(
            album_id: $album->id,
            song_id: null,
            album_persistent_id: $album->persistent_id,
            name: null,
            album: $album->name,
            artist: $album->artist_name,
            year: is_numeric($album->year) ? (int) $album->year : null,
            track_number: null,
        );
    }

    public static function fromSong(Song $song): self
    {
        return new self(
            album_id: $song->album->id,
            song_id: $song->id,
            album_persistent_id: $song->album->persistent_id,
            name: $song->name,
            album: $song->album->name,
            artist: $song->album->artist->name,
            year: is_numeric($song->album->year) ? (int) $song->album->year : null,
            track_number: $song->track_number,
        );
    }
}
