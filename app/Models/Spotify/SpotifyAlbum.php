<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;
use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Models\Spotify\SpotifySearchResultAlbum;
use App\Models\AlbumSpotifyAlbum\AlbumSpotifyAlbum;
use App\Services\SpotifyApi\Getters\SpotifyApiUserAlbumsGetter;

// Spotify albums are retrieved from spotify and are stored in the database (succes or warning)
class SpotifyAlbum extends Model
{
    protected $guarded = [];

    public function albumSpotifyAlbum()
    {
        return $this->hasOne(AlbumSpotifyAlbum::class, 'spotify_album_id', 'id');
    }

    public function store(SpotifyAlbum $spotifyAlbum)
    {
        $result = SpotifyAlbum::updateOrCreate(
            [
                'spotify_api_album_id' => $spotifyAlbum['spotify_api_album_id'],
            ],
            [
                'name' => $spotifyAlbum['name'],
                'name_sanitized' => $spotifyAlbum['name_sanitized'],
                'artist' => $spotifyAlbum['artist'],
                'artwork_url' => $spotifyAlbum['artwork_url'],

            ]
        );

        return $result;
    }

    public function storeFromSpotifySearchResultAlbum(SpotifySearchAlbumResult $spotifySearchAlbumResult)
    {


        $result = SpotifyAlbum::updateOrCreate(
            [
                'spotify_api_album_id' => $spotifySearchAlbumResult->spotify_api_album_id,
            ],
            [
                'name' => $spotifySearchAlbumResult->name,
                'name_sanitized' => $spotifySearchAlbumResult->name_sanitized,
                'artist' => $spotifySearchAlbumResult->artist,
                'artwork_url' => $spotifySearchAlbumResult->artwork_url,

            ]
        );
        return $result;
    }
}
