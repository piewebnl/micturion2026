<?php

namespace App\Models\Music;

use App\Models\Discogs\DiscogsRelease;
use App\Models\ItunesLibrary\ItunesLibraryTrack;
use App\Models\Tiermaker\TiermakerAlbum;
use App\Scopes\ArtistScopesTrait;
use App\Scopes\GlobalScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use ArtistScopesTrait;
    use GlobalScopesTrait;
    use QueryCache;

    // iTunes field with corresponing model fields
    protected $fields = [
        'artist' => 'name',
        'sort_artist' => 'sort_name',
    ];

    protected $guarded = [];

    public function albums()
    {
        return $this->hasMany(Album::class, 'artist_id', 'id');
    }

    public function songs()
    {
        return $this->hasManyThrough(Song::class, Album::class, 'artist_id', 'album_id');
    }

    public function albumPurchases()
    {
        return $this->hasManyThrough(
            AlbumPurchase::class,  // Target Model
            Album::class,           // Intermediate Model
            'artist_id',            // Foreign key on `albums` table
            'album_id',             // Foreign key on `discogs_releases` table
            'id',                   // Local key on `artists` table
            'id'                    // Local key on `albums` table
        );
    }

    public function tiermakerItems()
    {
        return $this->hasMany(TiermakerAlbum::class, 'foreign_id', 'id');
    }

    public function discogsReleases()
    {
        return $this->hasManyThrough(
            DiscogsRelease::class,
            Album::class,
            'id',
            'album_id',
            'album_id',
            'id'
        );
    }

    public function getTotalArtistsWithAlbums(array $filterValues): int
    {
        $filterValues['page'] = null;
        $artists = $this->getArtistsWithAlbums($filterValues);

        return count($artists);
    }

    public function getArtistsWithAlbums(array $filterValues)
    {
        $artists = [];
        if (isset($filterValues['sort']) && $filterValues['sort'] != 'random') {
            $artists = $this->getCache('get-artists-with-albums', $filterValues);
        }

        if (!$artists) {

            $keywordSearchIds = [];
            if ((isset($filterValues['keyword']) && $filterValues['keyword'] != '')) {
                $keywordSearchIds = Song::where('name', 'like', '%' . $filterValues['keyword'] . '%')
                    ->orwhere('songs.album_artist', 'LIKE', '%' . $filterValues['keyword'] . '%')->pluck('album_id')->toArray();
            }

            $artists = Artist::select(
                'artists.id as id',
                'artists.name as name',
                'artists.sort_name as sort_name',
                'albums.id as album_id',
                'albums.name as album_name',
                'albums.sort_name as album_sort_name',
                'albums.persistent_id as album_persistent_id',
                'albums.year as album_year',
                'albums.rating as album_rating',
                'albums.play_count as album_play_count',
                'albums.notes as album_notes',
                'albums.genre_id as album_genre_id',
                'spotify_albums.status as spotify_albums_status',
                'album_images.slug as album_image_slug',
                'album_images.largest_width as album_image_largest_width',
                'album_images.hash as album_image_hash',
                'spine_images.slug as spine_image_slug',
                'spine_images.checked as spine_image_checked',
                'categories.id as category_id',
                'categories.name as category_name',
                'categories.image_type as category_image_type',
                'genres.name as genre_name',
                'tiermaker_artists.id as tiermaker_artist_id',
            )
                ->with(['discogsReleases', 'albumPurchases'])
                ->selectRaw('
                GROUP_CONCAT(DISTINCT formats.id SEPARATOR ",") as format_id,
                GROUP_CONCAT(DISTINCT formats.parent_id SEPARATOR ",") as format_parent_id,
                GROUP_CONCAT(DISTINCT IF(formats.parent_id >0, NULL, formats.name) SEPARATOR ", ") as format_name,
                GROUP_CONCAT(DISTINCT IF(formats.parent_id >0, formats.name, null) SEPARATOR ", ") as subformat_name')

                ->join('albums', 'artists.id', '=', 'albums.artist_id')
                ->join('categories', 'categories.id', '=', 'albums.category_id')
                ->join('genres', 'genres.id', '=', 'albums.genre_id')

                ->leftjoin('album_images', 'album_images.album_id', '=', 'albums.id')
                ->leftjoin('spine_images', 'spine_images.album_id', '=', 'albums.id')
                ->leftjoin('album_formats', 'album_formats.album_id', '=', 'albums.id')
                ->leftjoin('formats', 'formats.id', '=', 'album_formats.format_id')
                ->leftjoin('spotify_albums', 'spotify_albums.album_id', '=', 'albums.id')
                ->leftjoin('tiermaker_artists', 'tiermaker_artists.artist_name', '=', 'artists.name')

                // ->whereId($filterValues, 'category_id', 'categories')
                ->whereId($filterValues, 'albums.genre_id', 'genres')
                ->whereId($filterValues, 'albums.persistent_id', 'album_persistent_id')
                ->whereId($filterValues, 'albums.id', 'album_id')
                ->whereId($filterValues, 'artists.id', 'id')
                ->whereId($filterValues, 'artists.id', 'artist')
                ->whereId($filterValues, 'artists.name', 'name')
                ->whereId($filterValues, 'albums.year', 'year')
                ->artistWhereLetter($filterValues)
                ->artistWhereFormats($filterValues)
                ->artistWhereCategoriesAndSongs($filterValues)
                ->artistIncludeCompilations($filterValues)
                ->artistWhereNoAlbumArtwork($filterValues)
                ->artistWhereSpineImagesChecked($filterValues)
                ->artistWhereKeyword($filterValues, $keywordSearchIds)
                ->artistWhereName($filterValues)
                ->groupBy('albums.id')
                ->artistOrderBy($filterValues)
                ->customPaginateOrLimit($filterValues);

            // dd($artists);
            // dd(DB::getQueryLog());

            $this->setCache('get-artists-with-albums', $filterValues, $artists);
        }

        return $artists;
    }

    public function storeItunesLibraryTrack(ItunesLibraryTrack $itunesLibraryTrack)
    {
        if ($itunesLibraryTrack->artist != null) {
            $artist = Artist::firstOrNew(['name' => $itunesLibraryTrack->artist]);

            foreach ($this->fields as $key => $field) {
                $artist->{$field} = $itunesLibraryTrack->{$key};
            }
            $artist->save();

            return $artist->id;
        }
    }
}
