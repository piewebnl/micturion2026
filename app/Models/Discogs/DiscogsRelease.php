<?php

namespace App\Models\Discogs;

use App\Models\Music\Album;
use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DiscogsRelease extends Model
{
    use GlobalScopesTrait;

    protected $guarded = [];

    public function album()
    {
        return $this->hasOne(Album::class, 'persistent_id', 'persistent_album_id');
    }

    public function store(DiscogsRelease $discogsRelease): void
    {

        self::updateOrCreate(
            [
                'release_id' => $discogsRelease->release_id,
            ],
            [
                'album_id' => $discogsRelease->album_id,
                'artist' => $discogsRelease->artist,

                'title' => $discogsRelease->title,
                'date' => $discogsRelease->date,
                'format' => $discogsRelease->format,
                'score' => $discogsRelease->score,
                'country' => $discogsRelease->country,
                'lowest_price' => $discogsRelease->lowest_price,
                'comments' => $discogsRelease->comments,
                'notes' => $discogsRelease->notes,
                'status' => $discogsRelease->status,
                'hash' => $discogsRelease->hash,
                'url' => $discogsRelease->url,
                'artwork_url' => $discogsRelease->artwork_url,
                'artwork_other_urls' => $discogsRelease->artwork_other_urls,
            ]
        );
    }

    public function storeFromDiscogsApiRelease($discogsApiRelease): void
    {

        dd($discogsApiRelease);
        self::updateOrCreate(
            [
                'release_id' => $discogsApiRelease['id'],

            ],
            [
                'artist' => $discogsApiRelease['artist'],
                'title' => $discogsApiRelease['title'],
                'url' => $discogsApiRelease['uri'],
                'notes' => $discogsApiRelease['notes'] ?? null,
                'country' => $discogsApiRelease['country'] ?? null,
                'date' => $discogsApiRelease['released'] ?? null,
                'url' => $discogsApiRelease['uri'] ?? null,
                'artwork_other_urls' => $discogsApiRelease['artwork_other_urls'],
                'lowest_price' => $discogsApiRelease['lowest_price'] ?? 0,
                'status' => 'scraped',
            ]
        );
    }

    public function getDiscogsReleases(array $filterValues): \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection
    {
        return self::select(
            'discogs_releases.id as discogs_release_id',
            'discogs_releases.release_id as discogs_release_realease_id',
            'discogs_releases.album_id as discogs_release_album_id',
            'discogs_releases.artist as discogs_release_artist',
            'discogs_releases.title as discogs_release_title',
            'discogs_releases.format as discogs_release_format',
            'discogs_releases.date as discogs_release_date',
            'discogs_releases.score as discogs_release_score',
            'discogs_releases.country as discogs_release_country',
            'discogs_releases.lowest_price as discogs_release_lowest_price',
            'discogs_releases.url as discogs_release_url',
            'discogs_releases.hash as discogs_release_hash',
            'discogs_releases.status as discogs_release_status',
            'discogs_releases.artwork_url as discogs_release_artwork_url',
            'discogs_releases.notes as discogs_release_notes',
            'albums.id as album_id',
            'albums.persistent_id as album_persistent_id',
            'albums.name as album_name',
            'albums.year as album_year',
            'albums.sort_name as album_sort_name',
            'album_images.slug as album_image_slug',
            'album_images.largest_width as album_image_largest_width',
            'album_images.hash as album_image_hash',
            'artists.name as artist_name',
            'artists.sort_name as artist_sort_name',
            'categories.id as category_id',
            'categories.name as category_name',
            'categories.image_type as category_image_type'
        )
            ->selectRaw('
                GROUP_CONCAT(DISTINCT formats.id SEPARATOR ",") as format_id,
                GROUP_CONCAT(DISTINCT formats.parent_id SEPARATOR ",") as format_parent_id,
                GROUP_CONCAT(DISTINCT IF(formats.parent_id >0, NULL, formats.name) SEPARATOR ", ") as format_name,
                GROUP_CONCAT(DISTINCT IF(formats.parent_id >0, formats.name, null) SEPARATOR ", ") as subformat_name')

            ->leftJoin('albums', 'discogs_releases.album_id', '=', 'albums.id')
            ->leftJoin('album_formats', 'album_formats.album_id', '=', 'albums.id')
            ->leftjoin('categories', 'categories.id', '=', 'albums.category_id')
            ->leftjoin('artists', 'albums.artist_id', '=', 'artists.id')
            ->leftJoin('album_images', 'album_images.album_id', '=', 'albums.id')
            ->leftJoin('formats', 'formats.id', '=', 'album_formats.format_id')
            ->orderBy('discogs_release_artist')
            ->orderBy('albums.sort_name')
            ->discogsReleaseWhereFormats($filterValues)
            ->discogsReleaseWhereKeyword($filterValues)
            ->discogsReleaseWhereMatched($filterValues)
            ->groupBy('discogs_releases.id')
            ->customPaginateOrLimit($filterValues);
    }
}
