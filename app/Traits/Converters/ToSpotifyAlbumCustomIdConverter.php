<?php

namespace App\Traits\Converters;

use App\Models\Music\Album;
use App\Models\Spotify\SpotifyAlbumCustomId;

trait ToSpotifyAlbumCustomIdConverter
{
    public function convertSpotifyApiAlbumToSpotifyAlbumCustomId($spotifyApiAlbum, Album $album)
    {
        $spotifyAlbumCustomId = new SpotifyAlbumCustomId;

        return $spotifyAlbumCustomId->fill([
            'persistent_id' => $album->persistent_id,
            'spotify_api_album_custom_id' => $spotifyApiAlbum->id,
            'artist' => $spotifyApiAlbum->artists[0]->name,
            'name' => $spotifyApiAlbum->name,
        ]);
    }

    public function convertAlbumToSpotifyAlbumCustomId($albumWithSpotifyAlbum)
    {

        $spotifyAlbumCustomId = new SpotifyAlbumCustomId;

        return $spotifyAlbumCustomId->fill([
            'persistent_id' => $albumWithSpotifyAlbum['persistent_id'],
            'spotify_api_album_custom_id' => $albumWithSpotifyAlbum['spotify_api_album_id'],
            'artist' => $albumWithSpotifyAlbum['artist_name'],
            'name' => $albumWithSpotifyAlbum['name'],
        ]);
    }
}
