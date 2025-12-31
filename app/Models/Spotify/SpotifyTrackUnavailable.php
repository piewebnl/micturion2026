<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

class SpotifyTrackUnavailable extends Model
{
    protected $table = 'spotify_tracks_unavailable';

    protected $guarded = [];

    public function store(SpotifyTrackUnavailable $spotifyTrackUnavailable)
    {
        $result = SpotifyTrackUnavailable::updateOrCreate(
            [
                'persistent_id' => $spotifyTrackUnavailable['persistent_id'],
            ],
            [
                'artist' => $spotifyTrackUnavailable['artist'],
                'album' => $spotifyTrackUnavailable['album'],
                'name' => $spotifyTrackUnavailable['name'],
            ]
        );

        return $result;
    }
}
