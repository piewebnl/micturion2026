<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

// Pseudo model to make a search via spotify api
class SpotifySearchAlbum extends Model
{
    protected $table = null;

    protected $guarded = [];

    public function store(SpotifySearchAlbum $spotifySearchAlbum)
    {
        // Store the album
        $spotifyAlbumModel = new SpotifyAlbum;
        $spotifyAlbum = $this->convertSpotifySearchAlbumToSpotifyAlbum($spotifySearchAlbum);
        $result = $spotifyAlbumModel->store($spotifyAlbum);

        return $result['id'];

        // CONVERTER -> en naar spotify TRACK
        /*
        $result = SpotifyAlbum::updateOrCreate(
            [
                'spotify_id' => $spotifySearchAlbum['spotify_id'],

            ],
            [
                //'persistent_id' => $spotifySearchAlbum['persistent_id'],
                'sort_name' => $spotifySearchAlbum['sort_name'],
                'artist' => $spotifySearchAlbum['artist'],
                'name' => $spotifySearchAlbum['name'],
                'album' => $spotifySearchAlbum['album'],
                'track_number' => $spotifySearchAlbum['track_number'],
                'disc_number' => $spotifySearchAlbum['disc_number'],
                'spotify_album_id' => $spotifySearchAlbum['spotify_album_id'],
                'artwork_url' => $spotifySearchAlbum['artwork_url'],
            ]
        );
        */

        return $result['id'];
    }
}
