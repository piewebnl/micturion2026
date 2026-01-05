<?php

namespace App\Models\Spotify;

use App\Models\Music\Album;
use Illuminate\Database\Eloquent\Model;

class SpotifyAlbumCustomId extends Model
{
    protected $table = 'spotify_album_custom_ids';

    protected $guarded = [];

    public function storeFromSpotifyApiAlbum($spotifyApiAlbum, Album $album)
    {

        $result = SpotifyAlbumCustomId::updateOrCreate(
            [
                'persistent_id' => $album->persistent_id,
            ],
            [
                'spotify_api_album_custom_id' => $spotifyApiAlbum->spotify_api_album_custom_id,
                'artist' => $spotifyApiAlbum->artist,
                'name' => $spotifyApiAlbum->name,
            ]
        );

        return $result;
    }
}
