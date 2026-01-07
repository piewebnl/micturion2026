<?php

namespace App\Models\Music;

use App\Models\Discogs\DiscogsRelease;
use App\Models\ItunesLibrary\ItunesLibraryTrack;
use App\Scopes\GlobalScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Album extends Model
{
    use GlobalScopesTrait;
    use QueryCache;

    // iTunes field with corresponing model fields
    protected $fields = [
        'album' => 'name',
        'sort_album' => 'sort_name',
        'persistent_album_id' => 'persistent_id',
        'year' => 'year',
        'album_location' => 'location',
        'play_count' => 'play_count',
        'notes' => 'notes',
        'artist_id' => 'artist_id',
        'category_id' => 'category_id',
        'genre_id' => 'genre_id',
        'compilation' => 'is_compilation',
        'date_modified' => 'date_modified',
        'date_added' => 'date_added',
    ];

    // public $with = ['artist', 'albumImage'];

    protected $guarded = [];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function albums()
    {
        return $this->hasMany(Song::class)->orderBy('disc_number')->orderBy('album_number');
    }

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function formats()
    {
        return $this->belongsToMany(Format::class, 'album_formats');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function genres()
    {
        return $this->belongsTo(Genre::class, 'genre_id', 'id');
    }

    public function albumImage()
    {
        return $this->belongsTo(AlbumImage::class, 'id', 'album_id');
    }

    public function spineImage()
    {
        return $this->belongsTo(SpineImage::class, 'id', 'album_id');
    }

    public function discogsReleases()
    {
        return $this->hasMany(DiscogsRelease::class);
    }

    public function tiermakerItems()
    {
        return $this->belongsToMany(TiermakerItem::class, 'tiermaker_item_album')
            ->withPivot('position');
    }

    public function storeItunesLibraryTrack(ItunesLibraryTrack $itunesAlbum)
    {
        if ($itunesAlbum->sort_album != null and $itunesAlbum->artist_id != null) {
            $album = Album::firstOrNew(
                [
                    'sort_name' => $itunesAlbum->sort_album,
                    'artist_id' => $itunesAlbum->artist_id,
                ]
            );

            foreach ($this->fields as $key => $field) {
                $album->{$field} = $itunesAlbum->{$key};
            }
            $album->updated_at = date('Y-m-d H:i:s'); // force updated_at
            $album->save();

            // Save pivot
            $album->formats()->sync($itunesAlbum->format_ids);

            return $album->id;
        }
    }

    public function getTotalAlbumsWithSpotifyAlbum(array $filterValues): int
    {
        $filterValues['page'] = null;
        $albums = $this->getAlbumsWithSpotifyAlbum($filterValues);

        return count($albums);
    }

    public function getAlbumWithSpotifyAlbum(int $id, array $filterValues = [])
    {
        $filterValues['id'] = $id;

        return $this->getAlbumsWithSpotifyAlbum($filterValues)->first();
    }

    public function getAlbumsWithSpotifyAlbum(array $filterValues)
    {
        return Album::select(
            'albums.id as id',
            'albums.name as name',
            'albums.sort_name as sort_name',
            'albums.persistent_id as persistent_id',
            'albums.category_id as category_id',
            'artists.name as artist_name',
            'artists.sort_name as artist_sort_name',
            'spotify_albums.id as spotify_albums_id',
            'spotify_albums.status as spotify_albums_status',
            'spotify_albums.spotify_api_album_id'
        )
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->join('spotify_albums', 'spotify_albums.album_id', '=', 'albums.id')
            ->join('categories', 'categories.id', '=', 'albums.category_id')
            ->whereId($filterValues, 'albums.id', 'id')
            ->whereId($filterValues, 'category_id', 'categories')
            ->whereId($filterValues, 'spotify_albums.status', 'status')
            ->whereNotNull('spotify_albums.spotify_api_album_id')
            ->orderBy('artists.sort_name')
            ->orderBy('albums.sort_name')
            ->groupBy('artists.name')
            ->groupBy('albums.sort_name')
            ->customPaginateOrLimit($filterValues);
    }

    public function getAlbumsWithoutSpotifyAlbum(array $filterValues)
    {
        return Album::select(
            'albums.id as id',
            'albums.name as name',
            'albums.sort_name as sort_name',
            'albums.year as year',
            'artists.name as artist_name',
            'artists.sort_name as artist_sort_name',
            'albums.persistent_id as persistent_id',
            'categories.id as category_id',
            'categories.name as category_name'

        )->leftJoin('spotify_albums', 'album_id', '=', 'albums.id')
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->join('categories', 'categories.id', '=', 'albums.category_id')
            ->orderBy('artists.sort_name')
            ->orderBy('albums.sort_name')
            ->whereId($filterValues, 'albums.id', 'id')
            ->whereId($filterValues, 'artist_id', 'artist_id')
            ->whereId($filterValues, 'category_id', 'categories')
            ->whereNull('spotify_albums.album_id')
            ->customPaginateOrLimit($filterValues);
    }

    public function getAlbumsWithoutdiscogsRelease(array $filterValues)
    {
        return Album::select(
            'albums.id as album_id',
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
            'categories.image_type as category_image_type',
            'discogs_releases.release_id as discogs_release_release_id'
        )
            ->selectRaw('
            GROUP_CONCAT(DISTINCT formats.id SEPARATOR ",") as format_id,
            GROUP_CONCAT(DISTINCT formats.parent_id SEPARATOR ",") as format_parent_id,
            GROUP_CONCAT(DISTINCT IF(formats.parent_id >0, NULL, formats.name) SEPARATOR ", ") as format_name,
            GROUP_CONCAT(DISTINCT IF(formats.parent_id >0, formats.name, null) SEPARATOR ", ") as subformat_name')

            ->leftjoin('discogs_releases', 'discogs_releases.album_id', '=', 'albums.id')
            ->leftjoin('album_formats', 'album_formats.album_id', '=', 'albums.id')
            ->leftjoin('categories', 'categories.id', '=', 'albums.category_id')
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->leftjoin('album_images', 'album_images.album_id', '=', 'albums.id')
            ->leftjoin('formats', 'formats.id', '=', 'album_formats.format_id')
            ->orderBy('artists.sort_name')
            ->orderBy('albums.sort_name')
            ->where('formats.name', '<>', 'None')
            ->where('formats.name', '<>', 'CDR')

            ->albumWithoutDiscogsWhereMatched($filterValues)
            ->groupBy('albums.id')
            ->customPaginateOrLimit($filterValues);
    }

    public function searchAlbumWithArtist($filterValues)
    {

        return Album::select(
            'albums.id as id',
            'albums.name as name',
            'albums.sort_name as sort_name',
            'albums.persistent_id as persistent_id',
            'albums.year as year',
            'artists.id as artist_id',
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
            ->join('artists', 'artists.id', '=', 'albums.artist_id')
            ->leftjoin('album_formats', 'album_formats.album_id', '=', 'albums.id')
            ->leftjoin('categories', 'categories.id', '=', 'albums.category_id')
            ->leftjoin('formats', 'formats.id', '=', 'album_formats.format_id')
            ->albumWhereKeyword($filterValues)
            ->groupBy('albums.id')
            ->orderBy('artist_sort_name')
            ->orderBy('sort_name')
            ->get();
    }

    public function getAlbumIdsWithSongsModified($filterValues)
    {
        return Album::select(
            'albums.id'
        )->join('songs', 'songs.album_id', '=', 'albums.id')
            ->where(
                'songs.date_modified',
                '>=',
                Carbon::now()->subDays($filterValues['days'])
            )
            ->groupBy('albums.id')
            ->customPaginateOrLimit($filterValues)->pluck('id');
    }

    public function getAllYears()
    {

        $years = $this->getCache('get-albums-all-years');

        if (!$years) {
            $years = Album::select('id', 'year')->groupBy('year')->orderBy('year', 'desc')->get();
            $this->setCache('get-albums-all-years', [], $years);
        }

        return $years;
    }

    public function getAmountAllPerYear()
    {
        $amount = [];
        $amount = $this->getCache('get-album-amount-all-per-year');

        if (!$amount) {

            $amount = Album::selectRaw('COUNT(*) as amount, year')
                ->join(
                    'album_formats',
                    'album_formats.album_id',
                    'albums.id'
                )
                ->join(
                    'formats',
                    'album_formats.format_id',
                    'formats.id'
                )
                ->selectRaw('COUNT(*) as amount')
                ->whereNull('formats.parent_id')
                ->whereNot('formats.name', 'None')
                ->groupBy('year')
                ->orderBy('year')
                ->get();

            $this->setCache('get-album-amount-all-per-year', [], $amount);
        }

        return $amount;
    }

    public function getAmountUniquePerYear()
    {

        $amount = [];
        $amount = $this->getCache('get-album-unqiue-all-per-year');

        if (!$amount) {
            $amount = Album::selectRaw('COUNT(DISTINCT albums.id) as amount, year')
                ->joinRelationship('formats')
                ->whereNull('formats.parent_id')
                ->where('formats.name', '<>', 'None')
                ->groupBy('year')

                ->get();

            $this->setCache('get-album-unqiue-all-per-year', [], $amount);
        }

        return $amount;
    }

    public function calculateAlbumRating(Album $album)
    {
        // more than 75% must be rated
        // tracks longer than 2 minutes

    }
}
