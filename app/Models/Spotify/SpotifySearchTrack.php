<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

// Pseudo model to make a search via spotify api
class SpotifySearchTrack extends Model
{
    protected $table = null;

    protected $guarded = [];

    public function store(SpotifySearchTrack $spotifySearchTrack)
    {
        // Store the track
        $spotifyTrackModel = new SpotifyTrack;
        $spotifyTrack = $this->convertSpotifySearchTrackToSpotifyTrack($spotifySearchTrack);
        $result = $spotifyTrackModel->store($spotifyTrack);

        return $result['id'];
    }
}
