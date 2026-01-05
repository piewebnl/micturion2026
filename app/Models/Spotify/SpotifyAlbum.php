<?php

namespace App\Models\Spotify;

use App\Dto\Spotify\SpotifySearchAlbumResult;
use App\Models\AlbumSpotifyAlbum\AlbumSpotifyAlbum;
use Illuminate\Database\Eloquent\Model;

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
                'artist_sanitized' => $spotifyAlbum['artist_sanitized'],
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
                'artist_sanitized' => $spotifySearchAlbumResult->artist_sanitized,
                'artwork_url' => $spotifySearchAlbumResult->artwork_url,

            ]
        );

        return $result;
    }
}
