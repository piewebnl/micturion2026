<?php

namespace App\Traits\Renamers;

trait ToSpotifySearchTrackRenamer
{
    // Rename an itunes song to spotify search track (for better search result)
    public function renameSongToSpotifySearchTrack(string $artistName, string $songName)
    {

        $config = config('spotifyRenamer');
        $replaceData = $config['song_to_spotify_track'];

        foreach ($replaceData as $replace) {
            foreach ($replace as $artist => $songs) {
                if ($artist == $artistName) {
                    foreach ($songs as $song => $replaceSong) {
                        if ($song == $songName) {
                            // Return replacement name
                            return $replaceSong;
                        }
                    }
                }
            }
        }

        // Nothing chaned
        return $songName;
    }
}
