<?php

namespace App\Services\Spotify\Searchers;

use App\Models\Music\Album;
use App\Dto\Spotify\SpotifySearchQuery;

// Prepare a album for spotify search
class SpotifyAlbumSearchPrepare
{

    // Fill the spotify search track model with data to search properly
    public function prepareSpotifySearchAlbum(Album $album): SpotifySearchQuery
    {

        $name = $album->name;
        $artist = is_string($album->artist['name'] ?? null) ? $album->artist['name'] : '';

        if ($artist === 'Various Artists' && $album->album_artist !== '') {
            $artist = $album->album_artist;
        }

        return SpotifySearchQuery::fromAlbum($album, $artist);
    }
}
