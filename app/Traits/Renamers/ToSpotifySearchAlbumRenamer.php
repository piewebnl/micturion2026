<?php

namespace App\Traits\Renamers;

trait ToSpotifySearchAlbumRenamer
{
    // Rename a spotify search album result to itunes album (for better match result)
    public function renameAlbumToSpotifySearchAlbum(string $artistName, string $albumName)
    {

        $config = config('spotifyRenamer');
        $replaceData = $config['album_to_spotify_album'];

        foreach ($replaceData as $replace) {
            foreach ($replace as $artist => $albums) {
                if ($artist == $artistName) {
                    foreach ($albums as $album => $replaceAlbum) {

                        if ($album == $albumName) {
                            // Return replacement name
                            return $replaceAlbum;
                        }
                    }
                }
            }
        }

        // Nothing chaned
        return $albumName;
    }
}
