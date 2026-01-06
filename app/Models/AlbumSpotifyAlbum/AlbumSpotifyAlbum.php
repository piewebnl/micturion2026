<?php

namespace App\Models\AlbumSpotifyAlbum;

use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbum;
use App\Scopes\AlbumSpotifyAlbumScopesTrait;
use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;

// Relation table between albums and spotify albums
class AlbumSpotifyAlbum extends Model
{

    /*
    use AlbumSpotifyAlbumScopesTrait, GlobalScopesTrait;

    protected $guarded = [];

    protected $table = 'album_spotify_album';

    public function album()
    {
        return $this->hasOne(Album::class, 'id', 'album_id');
    }

    public function spotifyAlbums()
    {
        return $this->hasOne(SpotifyAlbum::class, 'id', 'spotify_album_id');
    }

    public function store(AlbumSpotifyAlbum $albumSpotifyAlbum)
    {

        $result = AlbumSpotifyAlbum::updateOrCreate(
            [
                'album_id' => $albumSpotifyAlbum['album_id'],
            ],
            [
                'spotify_album_id' => $albumSpotifyAlbum['spotify_album_id'],
                'score' => $albumSpotifyAlbum['score'],
                'status' => $albumSpotifyAlbum['status'],
                'search_artist' => $albumSpotifyAlbum['search_artist'],
                'search_name' => $albumSpotifyAlbum['search_name'],
            ]
        );

        return $result;
    }

    public function storeFromSpotifySearchResultAlbum(SpotifySearchAlbumResult $spotifySearchAlbumResult, SpotifyAlbum $spotifyAlbum)
    {

        $result = AlbumSpotifyAlbum::updateOrCreate(
            [
                'album_id' => $spotifySearchAlbumResult->album_id,
            ],
            [
                'spotify_album_id' => $spotifyAlbum->id,
                'score' => $spotifySearchAlbumResult->score,
                'status' => $spotifySearchAlbumResult->status,
                'search_artist' => $spotifySearchAlbumResult->search_artist,
                'search_name' => $spotifySearchAlbumResult->name,
            ]
        );

        return $result;
    }

    public function getAlbumSpotifyAlbumWithAlbum(int $id, array $filterValues = [])
    {
        $filterValues['id'] = $id;

        return $this->getAlbumSpotifyAlbumWithAlbums($filterValues)->first();
    }

    public function getAlbumSpotifyAlbumWithAlbums(array $filterValues)
    {

        return AlbumSpotifyAlbum::select(
            'album_spotify_album.id as id',
            'album_spotify_album.status as status',
            'album_spotify_album.search_name as search_name',
            'album_spotify_album.search_artist as search_artist',
            'album_spotify_album.score as score',
            'spotify_albums.id as spotify_album_id',
            'spotify_albums.name as spotify_album_name',
            'spotify_albums.artist as spotify_album_artist',
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

        )->leftJoin('spotify_albums', 'album_spotify_album.spotify_album_id', '=', 'spotify_albums.id')
            ->join('albums', 'album_spotify_album.album_id', '=', 'albums.id')
            ->leftjoin('album_images', 'album_images.album_id', '=', 'albums.id')
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->whereId($filterValues, 'album_spotify_album.status', 'status')
            ->whereId($filterValues, 'album_spotify_album.id', 'id')
            ->albumSpotifyAlbumWhereKeyword($filterValues)
            ->orderBy('artists.sort_name')
            ->customPaginateOrLimit($filterValues);
    }
            */
}
