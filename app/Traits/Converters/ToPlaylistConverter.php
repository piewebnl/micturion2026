<?php

namespace App\Traits\Converters;

use App\Models\Playlist\Playlist;

trait ToPlaylistConverter
{
    public function convertItunesLibraryPlaylistToPlaylist(array $itunesLibraryPlaylist): Playlist
    {
        $playlist = new Playlist;

        return $playlist->fill(
            [
                'name' => $itunesLibraryPlaylist['name'],
                'parent_name' => $itunesLibraryPlaylist['parent_name'],
                'persistent_id' => $itunesLibraryPlaylist['persistent_id'],
                'parent_persistent_id' => $itunesLibraryPlaylist['parent_persistent_id'],
                'has_changed' => false,
            ]
        );
    }
}
