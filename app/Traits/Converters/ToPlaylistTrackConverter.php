<?php

namespace App\Traits\Converters;

use App\Models\Music\Song;

trait ToPlaylistTrackConverter
{
    public function convertItunesLibraryPlaylistTrackToPlaylistTrack(Song $song, array $itunesLibraryPlaylistTrack, int $playlistId): array
    {

        return [
            'name' => $itunesLibraryPlaylistTrack['name'],
            'playlist_id' => $playlistId,
            'song_id' => $song['id'],
            'order' => $itunesLibraryPlaylistTrack['order'],
            'has_changed' => false,

        ];
    }
}
