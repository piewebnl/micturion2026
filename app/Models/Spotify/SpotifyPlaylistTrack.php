<?php

namespace App\Models\Spotify;

use DB;
use Illuminate\Database\Eloquent\Model;

// Spotify Playlist tracks are stored in the database
class SpotifyPlaylistTrack extends Model
{
    protected $guarded = [];

    /*
    public function spotifyTracks()
    {
        return $this->hasMany(SpotifyTrack::class, 'spotify_api_track_id', 'track_id');
    }
    */

    public function spotifyTrack()
    {
        return $this->belongsTo(SpotifyTrack::class);
    }

    public function deleteNotChanged(SpotifyPlaylist $playlist)
    {
        SpotifyPlaylistTrack::where('spotify_playlist_id', $playlist->id)->where('has_changed', 0)->delete();
        DB::table('spotify_playlist_tracks')->where('spotify_playlist_id', $playlist->id)->update(['has_changed' => 0]);
    }
}
