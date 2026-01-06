<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Music\Song;
use App\Dto\Spotify\SpotifySearchQuery;

// Prepare a song for spotify search
class SpotifyTrackSearchPrepare
{

    // Fill the spotify search track model with data to search properly
    public function prepareSpotifySearchTrack(Song $song): SpotifySearchQuery
    {

        $artist = '';
        $album = '';

        $albumArtistName = $song->album->artist['name'] ?? null;
        if (is_string($albumArtistName)) {
            $artist = $albumArtistName;
        }

        $albumName = $song->album['name'] ?? null;
        if (is_string($albumName)) {
            $album = $albumName;
        }

        if (is_string($song->artist ?? null)) {
            $artist = $song->artist;
        }
        if (is_string($song->album ?? null)) {
            $album = $song->album;
        }
        if ($artist == 'Various Artists' && isset($song['album_artist'])) {
            $artist = $song['album_artist'];
        }

        $song->artist = $artist;
        $song->album = $album;

        $song->name = str_replace(' / ', ' ', $song->name);

        return SpotifySearchQuery::fromSong($song);
    }
}
