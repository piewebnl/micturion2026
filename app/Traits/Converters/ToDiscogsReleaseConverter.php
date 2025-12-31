<?php

namespace App\Traits\Converters;

use App\Models\Discogs\DiscogsRelease;

trait ToDiscogsReleaseConverter
{
    public function convertDiscogsApiCollectionReleaseToDiscogsRelease($discogsApiCollectionRelease): DiscogsRelease
    {
        $discogsRelease = new DiscogsRelease;

        return $discogsRelease->fill(
            [
                'release_id' => $discogsApiCollectionRelease['id'],
                'album_id' => $discogsApiCollectionRelease['album_id'] ?? null,
                'artist' => $discogsApiCollectionRelease['basic_information']['artists'][0]['name'],
                'title' => $discogsApiCollectionRelease['basic_information']['title'],
                'score' => $discogsApiCollectionRelease['score'] ?? null,
                'date' => $discogsApiCollectionRelease['released'] ?? null,
                'format' => $discogsApiCollectionRelease['format'] ?? null,
                // 'url' => $discogsApiCollectionRelease['basic_information']['resource_url'],
                'artwork_url' => $discogsApiCollectionRelease['basic_information']['thumb'],
                'comments' => $discogsApiCollectionRelease['comments'] ?? null,
                'hash' => $discogsApiCollectionRelease['hash'] ?? null,
                'status' => 'imported',

            ]
        );
    }
}
