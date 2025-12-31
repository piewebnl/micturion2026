<?php

namespace App\Models\Spotify;

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
                'artwork_url' => $spotifyAlbum['artwork_url'],

            ]
        );

        return $result;
    }
}
