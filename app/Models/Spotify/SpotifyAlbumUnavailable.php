<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

class SpotifyAlbumUnavailable extends Model
{
    protected $table = 'spotify_albums_unavailable';

    protected $guarded = [];

    public function store(SpotifyAlbumUnavailable $spotifyAlbumUnavailable)
    {
        $result = SpotifyAlbumUnavailable::updateOrCreate(
            [
                'persistent_id' => $spotifyAlbumUnavailable['persistent_id'],
            ],
            [
                'artist' => $spotifyAlbumUnavailable['artist'],
                'name' => $spotifyAlbumUnavailable['name'],
            ]
        );

        return $result;
    }
}
