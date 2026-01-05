<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchQuery;
use App\Models\Music\Album;

// Prepare a album for spotify search
class SpotifyAlbumSearchPrepare
{
    // Fill the spotify search track model with data to search properly
    public function prepareSpotifySearchAlbum(Album $album): SpotifySearchQuery
    {

        $artist = is_string($album->artist['name'] ?? null) ? $album->artist['name'] : '';

        if ($artist === 'Various Artists' && $album->album_artist !== '') {
            $artist = $album->album_artist;
        }

        $album->artist = $artist;

        return SpotifySearchQuery::fromAlbum($album);
    }
}
