<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

class SpotifyAlbumCustomId extends Model
{
    protected $table = 'spotify_album_custom_ids';

    protected $guarded = [];

    public function store(SpotifyAlbumCustomId $spotifyAlbumCustomId)
    {

        $result = SpotifyAlbumCustomId::updateOrCreate(
            [
                'persistent_id' => $spotifyAlbumCustomId['persistent_id'],
            ],
            [
                'spotify_api_album_custom_id' => $spotifyAlbumCustomId['spotify_api_album_custom_id'],
                'artist' => $spotifyAlbumCustomId['artist'],
                'name' => $spotifyAlbumCustomId['name'],
            ]
        );

        return $result;
    }
}
