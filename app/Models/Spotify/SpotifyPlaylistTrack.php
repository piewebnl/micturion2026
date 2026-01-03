<?php

namespace App\Models\Spotify;

use DB;
use Illuminate\Database\Eloquent\Model;

// Spotify Playlist tracks are stored in the database
class SpotifyPlaylistTrack extends Model
{
    protected $guarded = [];

    public function spotifyTrack()
    {
        return $this->belongsTo(SpotifyTrack::class);
    }
}
