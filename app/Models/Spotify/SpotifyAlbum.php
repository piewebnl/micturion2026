<?php

namespace App\Models\Spotify;

use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Models\Music\Album;
use App\Models\Music\Artist;
use App\Models\Music\Song;
use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

// Spotify albums are retrieved from spotify and are stored in the database (succes or warning)
class SpotifyAlbum extends Model
{
    protected $guarded = [];

    use BelongsToThrough;
    use GlobalScopesTrait;

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }

    public function songs()
    {
        return $this->hasMany(Song::class, 'album_id', 'album_id');
    }

    public function artist()
    {
        return $this->belongsToThrough(Artist::class, Album::class);
    }

    public function getSpotifyAlbumWithAlbum(array $filterValues)
    {

        return self::select(
            'spotify_albums.id as id',
            'spotify_albums.name as spotify_album_name',
            'spotify_albums.artist as spotify_album_artist',
            'spotify_albums.status as status',
            'spotify_albums.search_name as search_name',
            'spotify_albums.search_artist as search_artist',
            'spotify_albums.score as score',
            'spotify_albums.id as spotify_album_id',
            'spotify_albums.spotify_api_album_id as spotify_album_spotify_api_album_id',
            'albums.id as album_id',
            'albums.name as album_name',
            'albums.sort_name as album_sort_name',
            'albums.persistent_id as persistent_album_id',
            'album_images.slug as album_image_slug',
            'album_images.largest_width as album_image_largest_width',
            'album_images.hash as album_image_hash',
            'artists.name as artist_name',
            'artists.sort_name as artist_sort_name',
            // DB::raw('COUNT(*) as `count`')

        )->join('albums', 'spotify_albums.album_id', '=', 'albums.id')
            ->leftjoin('album_images', 'album_images.album_id', '=', 'albums.id')
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->whereId($filterValues, 'status', 'status')
            ->whereId($filterValues, 'spotify_albums.id', 'id')
            ->spotifyAlbumWhereKeyword($filterValues)
            ->orderBy('artists.sort_name')
            ->customPaginateOrLimit($filterValues);
    }

    public function store(SpotifyAlbum $spotifyAlbum)
    {
        $result = SpotifyAlbum::updateOrCreate(
            [
                'spotify_api_album_id' => $spotifyAlbum['spotify_api_album_id'],
            ],
            [
                'name' => $spotifyAlbum['name'],
                'artist' => $spotifyAlbum['artist'],
                'artwork_url' => $spotifyAlbum['artwork_url'],

            ]
        );

        return $result;
    }

    public function storeFromSpotifySearchResultAlbum(SpotifySearchAlbumResult $spotifySearchAlbumResult, Album $album)
    {

        $result = SpotifyAlbum::updateOrCreate(
            [
                'spotify_api_album_id' => $spotifySearchAlbumResult->spotify_api_album_id,
            ],
            [
                'album_id' => $album->id,
                'name' => $spotifySearchAlbumResult->name,
                'artist' => $spotifySearchAlbumResult->artist,
                'artwork_url' => $spotifySearchAlbumResult->artwork_url,
                'score' => $spotifySearchAlbumResult->score,
                'search_name' => $spotifySearchAlbumResult->search_name,
                'search_artist' => $spotifySearchAlbumResult->search_artist,
                'status' => $spotifySearchAlbumResult->status,

            ]
        );

        return $result;
    }
}
