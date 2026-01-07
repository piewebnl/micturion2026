<?php

namespace App\Services\Spotify\Searchers;

use App\Dto\Spotify\SpotifySearchAlbumQuery;
use App\Models\Music\Album;

// Prepare a album for spotify search
class SpotifyAlbumSearchPrepare
{
    // Fill the spotify search track model with data to search properly
    public function prepareSpotifySearchAlbum(Album $album): SpotifySearchAlbumQuery
    {

        $artist = is_string($album->artist['name'] ?? null) ? $album->artist['name'] : '';

        if ($artist === 'Various Artists' && $album->album_artist !== '') {
            $artist = $album->album_artist;
        }

        $album->artist = $artist;

        $album->name = str_replace(' / ', ' ', $album->name);

        return SpotifySearchAlbumQuery::fromAlbum($album);
    }
}
