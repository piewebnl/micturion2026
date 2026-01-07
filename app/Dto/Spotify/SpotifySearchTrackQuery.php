<?php

namespace App\Dto\Spotify;

use App\Models\Music\Song;

class SpotifySearchTrackQuery
{
    public function __construct(
        public ?int $album_id,
        public ?int $song_id,
        public ?int $track_number,
        public ?int $disc_number,
        public ?int $year,
        public ?string $album_persistent_id,
        public ?string $artist,
        public ?string $album,
        public ?string $name,
        public ?string $song_persistent_id
    ) {}

    public static function fromSong(Song $song): self
    {
        $albumName = $song->album_name ?? $song->album?->name ?? $song->album ?? null;
        $artistName = $song->artist_name
            ?? $song->artist?->name
            ?? $song->album?->artist?->name
            ?? $song->album_artist
            ?? $song->artist
            ?? null;
        $albumPersistentId = $song->album_persistent_id ?? $song->album?->persistent_id ?? null;
        $albumYear = $song->album_year ?? $song->album?->year ?? null;

        return new self(
            album_id: $song->album_id,
            song_id: $song->id,
            track_number: $song->track_number,
            disc_number: $song->disc_number,
            year: is_numeric($albumYear) ? (int) $albumYear : null,
            album_persistent_id: $albumPersistentId,
            artist: $artistName,
            album: $albumName,
            name: $song->name,
            song_persistent_id: $song->persistent_id,
        );
    }
}
