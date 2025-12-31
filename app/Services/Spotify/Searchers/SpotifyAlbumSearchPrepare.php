<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Music\Album;
use App\Models\Spotify\SpotifySearchAlbum;
use App\Traits\Renamers\ToSpotifySearchAlbumRenamer;

// Prepare a album for spotify search
class SpotifyAlbumSearchPrepare
{
    use ToSpotifySearchAlbumRenamer;

    // Fill the spotify search track model with data to search properly
    public function prepareSpotifySearchAlbum(Album $album)
    {

        $artist = '';
        $name = $album['name'];

        if (isset($album->artist['name']) && is_string($album->artist['name'])) {
            $artist = $album->artist['name'];
        }

        if ($artist == 'Various Artists' and $album['album_artist'] != '') {
            $artist = $album['album_artist'];
        }

        $spotifySearchAlbum = new SpotifySearchAlbum;
        $spotifySearchAlbum->fill(
            [
                'album_id' => $album->id,
                'sort_name' => $album->sort_name,
                'persistent_id' => $album->persistent_id, // we need this to find a custom id
                'name' => $name,
                'name' => $name,
                'album' => $album,
                'artist' => $artist,
                'track_number' => $album->track_number,
                'year' => $album->year,
            ]
        );

        return $spotifySearchAlbum;
    }
}
