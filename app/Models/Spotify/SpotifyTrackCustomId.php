<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

class SpotifyTrackCustomId extends Model
{
    protected $table = 'spotify_track_custom_ids';

    protected $guarded = [];

    public function store(SpotifyTrackCustomId $spotifyTrackCustomId)
    {
        $result = SpotifyTrackCustomId::updateOrCreate(
            [
                'persistent_id' => $spotifyTrackCustomId['persistent_id'],
            ],
            [
                'spotify_api_track_custom_id' => $spotifyTrackCustomId['spotify_api_track_custom_id'],
                'artist' => $spotifyTrackCustomId['artist'],
                'album' => $spotifyTrackCustomId['album'],
                'name' => $spotifyTrackCustomId['name'],
            ]
        );

        return $result;
    }
}
