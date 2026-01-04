<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;
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

    public function storeFromSpotifySearchResultAlbum(SpotifySearchResultAlbum $spotifySearchResultAlbum)
    {

        dd($spotifySearchResultAlbum);
        $result = SpotifyAlbum::updateOrCreate(
            [
                'spotify_api_album_id' => $spotifySearchResultAlbum['spotify_api_album_id'],
            ],
            [
                'name' => $spotifySearchResultAlbum['name'],
                'name_sanitized' => $spotifySearchResultAlbum['name_sanitized'],
                'artist' => $spotifySearchResultAlbum['artist'],
                'artwork_url' => $spotifySearchResultAlbum['artwork_url'],

            ]
        );
        return $result;
    }
}
