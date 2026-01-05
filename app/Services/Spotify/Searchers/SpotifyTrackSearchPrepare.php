<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Music\Song;
use App\Models\Spotify\SpotifySearchTrack;
use App\Traits\Renamers\ToSpotifySearchAlbumRenamer;
use App\Traits\Renamers\ToSpotifySearchTrackRenamer;

// Prepare a song for spotify search
class SpotifyTrackSearchPrepare
{
    // use ToSpotifySearchAlbumRenamer;
    // use ToSpotifySearchTrackRenamer;

    // Fill the spotify search track model with data to search properly
    public function prepareSpotifySearchTrack(Song $song): SpotifySearchTrack
    {

        $artist = '';
        $album = '';
        $name = $song['name'];

        if (isset($song->album->artist['name']) && is_string($song->album->artist['name'])) {
            $artist = $song->album->artist['name'];
        }
        if (isset($song->album['name'])) {
            $album = $song->album['name'];
        }

        if (isset($song->artist) && is_string($song->artist)) {
            $artist = $song->artist;
        }
        if (isset($song->album) && is_string($song->album)) {
            $album = $song->album;
        }
        if ($artist == 'Various Artists' && isset($song['album_artist'])) {
            $artist = $song['album_artist'];
        }

        $spotifySearchTrack = new SpotifySearchTrack;
        $spotifySearchTrack->fill(
            [
                'song_id' => $song->id,
                'persistent_id' => $song->persistent_id, // we need this to find a custom id
                'name' => $name,
                'album' => $album,
                'artist' => $artist,
                'track_number' => $song->track_number,
                'year' => $song->year,
            ]
        );

        return $spotifySearchTrack;
    }
}
