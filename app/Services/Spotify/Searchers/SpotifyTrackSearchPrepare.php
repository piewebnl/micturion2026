<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchTrackQuery;
use App\Models\Music\Song;

// Prepare a song for spotify search
class SpotifyTrackSearchPrepare
{
    // Fill the spotify search track model with data to search properly
    public function prepareSpotifySearchTrack(Song $song): SpotifySearchTrackQuery
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

        if (is_string($song->artist_name ?? null)) {
            $artist = $song->artist_name;
        }

        if (is_string($song->album_name ?? null)) {
            $album = $song->album_name;
        }

        if ($artist == 'Various Artists' && isset($song['album_artist'])) {
            $artist = $song['album_artist'];
        }

        $song->artist = $artist;
        $song->album = $album;

        $song->name = str_replace(' / ', ' ', $song->name);

        return SpotifySearchTrackQuery::fromSong($song);
    }
}
