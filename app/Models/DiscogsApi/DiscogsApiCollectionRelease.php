<?php

namespace App\Models\DiscogsApi;

use App\Models\Discogs\DiscogsRelease;
use App\Traits\Converters\ToDiscogsReleaseConverter;
use Illuminate\Database\Eloquent\Model;

// Discogs collection release
class DiscogsApiCollectionRelease extends Model
{
    use ToDiscogsReleaseConverter;

    protected $guarded = [];

    // CONVERTER HOORT HIER NIET?
    public function storeFromDiscogApiCollectionRelease(array $discogsApiCollectionRelease)
    {

        $discogsRelease = DiscogsRelease::updateOrCreate(

            [
                'release_id' => $discogsApiCollectionRelease['id'],
            ],
            [
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

    public function storeFromDiscogsReleaseMatcher($processedRelease)
    {
        $discogsRelease = DiscogsRelease::updateOrCreate(

            [
                'release_id' => $processedRelease['release_id'],
            ],
            [
                'album_id' => $processedRelease['album_id'] ?? null,
                'artist' => $processedRelease['artist'],
                'title' => $processedRelease['title'],
                'score' => $processedRelease['score'] ?? null,
                'date' => $processedRelease['released'] ?? null,
                'format' => $processedRelease['format'] ?? null,
                'artwork_url' => $processedRelease['artwork_url'],
                'comments' => $processedRelease['comments'] ?? null,
                'hash' => $processedRelease['hash'] ?? null,
                'status' => 'imported',
            ]

        );
        return $discogsRelease;
    }

    public function storeAllFromDiscogsReleaseMatcher(array $processedReleases)
    {
        foreach ($processedReleases as $processedRelease) {
            $this->storeFromDiscogsReleaseMatcher($processedRelease);
        }

        return count($processedReleases);
    }
}
